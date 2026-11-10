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
  function printing($template, $tagData, $inputData, $outputName, $outputFormat = 'pdf', $orientation = 'portrait', $outputType = 'file')
  {
    $root = getenv('app.root');
    $baseUrl = getenv('app.baseURL');
    $printerService = getenv('app.services');
    $src = render("{$root}/app/Templates/{$template}.html", $tagData, $inputData);
    $results = preg_replace('/\s\s+/', ' ', $src);
    $tags_html = array("{{body}}");
    $rep_html = array($src);
    $src_html = render("{$root}/app/Templates/basic.html", $tags_html, $rep_html);
    $preparedHtml = preg_replace('/\s\s+/', ' ', $src_html);
    $file_name = $outputName;
    $file_dir = "{$root}/cetak/{$file_name}";
    file_put_contents($file_dir . '.html', $preparedHtml);
    $parameters = "src={$file_dir}.html&tar={$file_dir}.pdf&output={$outputType}";
    if ($orientation !== 'portrait') $parameters = "src={$file_dir}.html&tar={$file_dir}.pdf&orientation={$orientation}&output={$outputType}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "{$printerService}/tools/export/pdf");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
    $results = array();
    if ($outputType == 'file') {
      $code = 204;
      $file_exist = file_exists($file_dir . '.pdf');
      if ($file_exist) {
        $code = 200;
        array_push($results, array(
          "file" => "{$baseUrl}/cetak/{$file_name}.pdf?v=" . time()
        )); //perubahan agar tidak nge cache 
        // array_push($results, array("file" => "{$baseUrl}/cetak/{$file_name}.pdf"));
      }
    } else if ($outputType == 'stream') {
      $code = 200;
      $res = json_decode($output, true);
      array_push($results, array("file" => $res['data'][0]['url']));
    }
    @unlink($file_dir . '.html');
    return array("code" => $code, "results" => $results);
  }
}
