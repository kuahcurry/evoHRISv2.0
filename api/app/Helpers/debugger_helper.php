<?php
if (!function_exists('logger')) {
 function logger($str, $params) {
  $patterns = array();
  foreach($params as $key => $value) {
   array_push($patterns, '#'.($key+1));
   $str = substr_replace($str,"#".($key+1),strpos($str, '?'), 1);
  }
  $stringTmp = preg_replace('/\#\d+/', '\'$0\'', $str);
  foreach($patterns as $key => $value) {
   $stringTmp = preg_replace('/\''.$value.'\'/', "'{$params[$key]}'", $stringTmp);
  }
  return $stringTmp;
 }
}