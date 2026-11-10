<?php
if (!function_exists('findData')) {
    function findData($db, $str, $params)
    {
        $rs = $db->query($str, $params);
        return $rs->getNumRows() > 0 ? true : false;
    }
}
if (!function_exists('findIndexInArray')) {
    function findIndexInArray($arr, $key, $value)
    {
        return array_search($value, array_column($arr, $key));
    }
}
if (!function_exists('calculateAge')) {
    function calculateAge($birth, $format = 'Y-m-d')
    {
        $tanggal_lahir = date($format, strtotime($birth));
        $birthDate = new \DateTime($tanggal_lahir);
        $today = new \DateTime("today");
        if ($birthDate > $today) {
            return array("year" => 0, "month" => 0, "day" => 0);
        }
        $y = $today->diff($birthDate)->y;
        $m = $today->diff($birthDate)->m;
        $d = ($today->diff($birthDate)->d);
        return array("year" => $y, "month" => $m, "day" => $d);
    }
    if (!function_exists('tokenHash')) {
        function tokenHash($user, $pass)
        {
            return base64_encode($user . $pass);
        }
    }
}
if (!function_exists('map_checkbox')) {
    function map_checkbox($array, $value, $start, $wrap1, $wrap2, $join = ',', $split = '')
    {
        $v = array();
        $i = $value;
        $exp = explode($join, $i);
        foreach ($array as $key => $val) {
            $ket = '';
            $n = '&ensp;&#9744;&ensp;';
            if ($split != '') {
                foreach ($exp as $value) {
                    $dt = explode($split, $value);
                    if ($dt[0] == ($key + $start)) {
                        $n = '&ensp;&#9745;&ensp;';
                        if ($dt[1] != '') $ket = ': ' . $dt[1] . '';
                    }
                }
            } else if (in_array($key + $start, $exp)) {
                $n = '&ensp;&#9745;&ensp;';
            }
            $res = $wrap1 . $n . $val . $ket . $wrap2;
            array_push($v, $res);
        }
        $results = implode("", $v);
        return $results;
    }
}
if (!function_exists('map_checkbox_down')) {
    function map_checkbox_down($array, $value, $start, $wrap1, $wrap2, $join = ',', $split = '')
    {
        $v = array();
        $i = $value;
        $exp = explode($join, $i);
        foreach ($array as $key => $val) {
            $ket = '';
            $n = '&ensp;&#9744;&ensp;';
            if ($split != '') {
                foreach ($exp as $value) {
                    $dt = explode($split, $value);
                    if ($dt[0] == ($start - $key)) {
                        $n = '&ensp;&#9745;&ensp;';
                        if ($dt[1] != '') $ket = ': ' . $dt[1] . '';
                    }
                }
            } else if (in_array($start - $key, $exp)) {
                $n = '&ensp;&#9745;&ensp;';
            }
            $res = $wrap1 . $n . $val . $ket . $wrap2;
            array_push($v, $res);
        }
        $results = implode("", $v);
        return $results;
    }
}
if (!function_exists('tgl_indo')) {
 function tgl_indo($tanggal)
 {
    $bulan = array(
        1 =>   'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );
    $pecahkan = explode('-', $tanggal);
    return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
 }
}
if (!function_exists('null_to_empty')) {
 function null_to_empty($array, $replacement = array(), $variable = '')
 {
     foreach ($array as $key => &$value) {
         if ($variable !== '') {
             $a = (is_null($value) && in_array($key, $variable, true));
         } else {
             $a = is_null($value);
         }
         $value = (is_array($value)) ? null_to_empty($value, $replacement, $variable) : $value;
         $value = $a ? $replacement : $value;
     }
     return $array;
 }
}
if (!function_exists('createFolder')) {
 function createFolder($folderWithPath) {
  if (!is_dir($folderWithPath)) {
   shell_exec("mkdir {$folderWithPath}");
   $created = is_dir($folderWithPath) ? true : false;
  }else {
   $created = true;
  }
  return $created;
 }
}
if (!function_exists('loadImageFromFile')) {
 function loadImageFromFile($filenameWithPath) {
  $stream = false;
  $isExist = file_exists($filenameWithPath);
  if ($isExist) {
   $file = file_get_contents($filenameWithPath);
   $stream = 'data:image/png;base64,' . base64_encode($file);
  }
  return $stream;
 }
} 
if (!function_exists('saveImageToFile')) {
 function saveImageToFile($filenameWithPath, $data) {
  $base64 = explode(';', $data);
  $img = str_replace('base64,', '', $base64[1]);
  $img = str_replace(' ', '+', $img);
  $tandatangan = base64_decode($img);
  $isExist = file_exists($filenameWithPath);
  if ($isExist) unlink($filenameWithPath);
  $success = file_put_contents($filenameWithPath, $tandatangan);
  return $success;
 }
}
if (!function_exists('deleteFile')) {
 function deleteFile($filenameWithPath) {
  $isExist = file_exists($filenameWithPath);
  if ($isExist) unlink($filenameWithPath);
  $isExist = file_exists($filenameWithPath);
  return !$isExist;
 }
}
if (!function_exists('create_image')) {
 function create_image($sample, $no_kunjungan, $folder, $category)
 {
  $path = env('app.root');
  $base64 = explode(';', $sample);
  $img = str_replace('base64,', '', $base64[1]);
  $img = str_replace(' ', '+', $img);
  $img_decode = base64_decode($img);
  $filename = $category . '_' . $no_kunjungan . '.png';
  $isExist = file_exists("{$path}/files/{$folder}/{$filename}");
  if ($isExist) unlink("{$path}/files/{$folder}/{$filename}");
  $results = file_put_contents("{$path}/files/{$folder}/{$filename}", $img_decode);
  if ($results != true) $results = array("message" => "gagal create " . $category);
  return $results;
 } 
}
if (!function_exists('tcheckbox')) {
 function tcheckbox($dataset, $index) 
 {
  return (isset($dataset[$index]) && $dataset[$index] == 'on' ? '&#9745;' : '&#9744;');
 }
}
if (!function_exists('tradio')) {
 function tradio($dataset, $index, $value)
 {
     return (isset($dataset[$index]) && $dataset[$index] == $value ? '&#9745;' : '&#9744;');
 }
}