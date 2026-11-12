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
    if ($tmp === false) {
      return '';
    }
    $i = count($tags);
    for ($x = 0; $x < $i; $x++) {
      try {
        $tag = $tags[$x];
        $val = isset($datas[$x]) ? (string)$datas[$x] : '';
        $rawTag = trim($tag, '{}/');
        // Replace exact matches first
        $tmp = str_replace(array('{{' . $rawTag . '}}', '{' . $rawTag . '}', $tag), $val, $tmp);
        // Fallback if tag was provided as regex
        if (strpos($tag, '/') === 0) {
          $escapedVal = str_replace(array('\\', '$'), array('\\\\', '\\$'), $val);
          $tmp = preg_replace($tag, $escapedVal, $tmp);
        }
      } catch (Exception $e) {
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
    if (empty($root) || !is_dir($root)) {
      $root = realpath(dirname(__DIR__, 2));
    }
    $baseUrl = getenv('app.baseURL');
    if (empty($baseUrl)) {
      $baseUrl = 'http://127.0.0.1:8099/evoHRIS_v2.1/api';
    }
    $printerService = getenv('app.services');
    $file_name = $outputName;

    if (!is_dir("{$root}/cetak")) {
      @mkdir("{$root}/cetak", 0777, true);
    }

    $file_dir = "{$root}/cetak/{$file_name}";
    if ($type == "array") {
      $templatefile = $template['body'];
      if (isset($template['header'])) {
        $templateheader['header'] = $template['header'];
        $templateheader['placeholder'] = isset($template['header_placeholder']) ? $template['header_placeholder'] : null;
        $templateheader['data'] = isset($template['header_data']) ? $template['header_data'] : null;
        if ($templateheader['placeholder'] !== null && $templateheader['data'] !== null) {
          $src_header = render("{$root}/app/Templates/{$templateheader['header']}.html", $templateheader['placeholder'],  $templateheader['data']);
        } else {
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
    if (stripos($src, '<html') !== false || stripos($src, '<!DOCTYPE') !== false) {
      $src_html = $src;
    } else {
      $tags_html = array("{{body}}", "{body}");
      $rep_html = array($src, $src);
      $src_html = render("{$root}/app/Templates/basic.html", $tags_html, $rep_html);
      if (empty($src_html)) {
        $src_html = $src;
      }
    }
    file_put_contents($file_dir . '.html', $src_html);
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
      curl_setopt($ch, CURLOPT_TIMEOUT, 3);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
      $output = curl_exec($ch);
      curl_close($ch);
    }
    $results = array();
    if ($outputType == 'file') {
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
        @unlink($file_dir . '.html');
      } else {
        // Fallback directly to HTML preview if microservice PDF is unavailable
        if (file_exists($file_dir . '.html')) {
          $code = 200;
          $results = array("code" => $code, "results" => array(array("file" => "{$baseUrl}/cetak/{$file_name}.html")));
        } else {
          $code = 204;
          $results = array("code" => $code, "results" => $results);
        }
      }
    } else if ($outputType == 'stream') {
      $code = 200;
      $res = json_decode($output, true);
      $results = array("code" => $code, "results" => array(array("file" => $res['data'][0]['url'] ?? '')));
    } else if ($outputType == 'html') {
      $code = 200;
      $results = array("code" => $code, "results" => array(array("file" => "{$baseUrl}/cetak/{$file_name}.html")));
    }
    return $results;
  }
}