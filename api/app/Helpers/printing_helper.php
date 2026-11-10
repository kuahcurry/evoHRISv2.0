<?php
if (!function_exists('render')) {
  function render($url, $tags, $datas, $mode = 'file')
  {
    if ($mode == 'curl') {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_URL, $url);
      $tmp = curl_exec($ch);
      curl_close($ch);
    } else {
      $tmp = file_get_contents($url);
    }
    $i = count($tags);
    for ($x = 0; $x < $i; $x++) {
      try {
        $tmp = preg_replace($tags[$x], $datas[$x], $tmp);
      } catch (Exception $e) {
        print_r($tags[$x]);
        print_r($datas[$x]);
        continue;
      }
    }
    return $tmp;
  }
}
if (!function_exists('printing')) {
  function printing($template, $tagData, $inputData, $outputName, $docType = 'pdf', $orientation = 'portrait', $outputType = 'file', $papersize = 'A4', $requireEsign = false, $esignAttributes = array())
  {
			 $endpoint = "tools/export/pdf";
				$ext = '.pdf';
    if ($docType == 'excel') {
 				$endpoint = "tools/export/excel";
				 $ext = '.xlsx';
				}
    $templatefile = $template;
    $templateheader = array();
    $templatefooter = array();
    $type = gettype($template);
    $root = getenv('app.root');
    $baseUrl = getenv('app.baseURL');
    $printerService = getenv('app.services');
    $file_name = $outputName;
    $file_dir = "{$root}/cetak/{$file_name}";
    if ($type == "array") {
      $templatefile = $template['body'];
      if (isset($template['header'])) {
       $templateheader['header'] = $template['header'];
       $templateheader['placeholder'] = isset($template['header_placeholder']) ? $template['header_placeholder'] : null;
       $templateheader['data'] = isset($template['header_data']) ? $template['header_data'] : null;
       if ($templateheader['placeholder'] !== null && $templateheader['data'] !== null) {
        $src_header = render("{$root}/app/Templates/{$templateheader['header']}.html", $templateheader['placeholder'],  $templateheader['data']);
       }else {
        $src_header = file_get_contents("{$root}/app/Templates/{$templateheader['header']}.html");
       }
       file_put_contents($file_dir.'_header.html', $src_header);
      }
      if (isset($template['footer'])) {
       $templatefooter['footer'] = $template['footer'];
       $templatefooter['placeholder'] = $template['footer_placeholder'];
       $templatefooter['data'] = $template['footer_data'];
       $src_footer = render("{$root}/app/Templates/{$templatefooter['footer']}.html", $templatefooter['placeholder'],  $templatefooter['data']);
       file_put_contents($file_dir.'_footer.html', $src_footer);
      }
    }
    @unlink($file_dir . '.html');
    $src = render("{$root}/app/Templates/{$templatefile}.html", $tagData, $inputData);
    $results = preg_replace('/\s\s+/', ' ', $src);
    $tags_html = array("{{body}}");
    $rep_html = array($src);
    $src_html = render("{$root}/app/Templates/basic.html", $tags_html, $rep_html);
    $preparedHtml = preg_replace('/\s\s+/', ' ', $src_html);
    file_put_contents($file_dir . '.html', $preparedHtml);
    $parameters = array();
    if (count($templateheader) > 0) array_push($parameters, "header={$file_dir}_header.html"); 
    if (count($templatefooter) > 0) array_push($parameters, "footer={$file_dir}_footer.html");
    array_push($parameters, "src={$file_dir}.html", "tar={$file_dir}{$ext}");
    if ($orientation !== 'portrait') array_push($parameters, "orientation={$orientation}");
				if ($papersize !== 'A4') array_push($parameters, "papersize={$papersize}");
    if ($outputType !== 'html') {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "{$printerService}/{$endpoint}");
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, implode("&", $parameters));
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $output = curl_exec($ch);
      curl_close($ch);
    }
    $results = array();
    if ($outputType == 'file') {
      $code = 204;
      $res = json_decode($output, true);
      $file_exist = file_exists($file_dir . $ext);
      if ($file_exist) {
        $code = 200;
        $results = array("code" => $code, "results" => array(array("file" => "{$baseUrl}/cetak/{$file_name}{$ext}")));
        if ($requireEsign) {
          $rs = signDocument($esignAttributes);
          $res_baru = $rs['res_baru'];
          $res = $rs['res'];
          $path = $rs['path'];
          if ($rs['code'] != 200) {
            $code = $rs['code'];
            $results = $rs['status'];
            $res_baru = $rs['res_baru'];
            $path = '';
            $results = array("code" => $code, "results" => $results, "res_baru" => $res_baru, "res" => $res, "path" => $path);
          }
        }
      } else {
        $results = array("code" => $code, "results" => $results);
      }
    } else if ($outputType == 'stream') {
      $code = 200;
      $res = json_decode($output, true);
      $results = array("code" => $code, "results" => array(array("file" => $res['data'][0]['url'])));
    } else if ($outputType == 'html') {
      $code = 200;
      $results = array("code" => $code, "results" => array(array("file" => "{$baseUrl}/cetak/{$file_name}.html")));
    }
    if ($outputType !== 'html') @unlink($file_dir . '.html');
    return $results;
  }
}