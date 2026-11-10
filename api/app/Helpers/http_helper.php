<?php
if ( ! function_exists('querystring'))
{
	function querystring($params)
	{
  $formated = array();
  foreach($params as $key => $value) {
   array_push($formated, $key."=".$value);
  }
  return implode("&", $formated);
 }
}
if ( ! function_exists('http_get'))
{
	function http_get($url, $params)
	{
  $targetUrl = $url;
  if (count($params) > 0) {
   $targetUrl = $url."?".querystring($params);
  }
  $headers = array(
   "Accept: application/json",
   "Content-Type: application/x-www-form-urlencoded",
   "Authorization-Key: app202101281103"
  );
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $targetUrl);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);  
  $response = curl_exec($curl);
  curl_close($curl);  
  return json_decode($response,true);
	}
}
if ( ! function_exists('http_post'))
{
 function http_post($url, $params, $headers = array())
 {
  if (count($headers) == 0) {
   $headers = array(
    "Accept: application/json",
    "Content-Type: application/x-www-form-urlencoded",
    "Authorization-Key: app202101281103"
   );
  }
  $data = querystring($params);
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);  
  $response = curl_exec($curl);
  curl_close($curl);
  return json_decode($response,true);
 }
}

if ( ! function_exists('http_postfile'))
{
 function http_postfile($url, $params, $headers = array())
 {
  if (count($headers) == 0) {
   $headers = array(
    "Accept: */*",
    "Content-type: multipart/form-data"
   );
  }
  $handle = curl_init();
  curl_setopt($handle, CURLOPT_URL, $url);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($handle, CURLOPT_POSTFIELDS, $params);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);  
  $response = curl_exec($handle);
  curl_close ($handle);
  return $response;
 }  
}
if ( ! function_exists('http_del'))
{
	function http_del($url, $params)
	{
  $targetUrl = $url;
  if (count($params) > 0) {
   $targetUrl = $url."?".querystring($params);
  }
  $headers = array(
   "Accept: application/json",
   "Content-Type: application/x-www-form-urlencoded",
   "Authorization-Key: app202101281103"
  );
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $targetUrl);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl, CURLOPT_DELETE, true);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);  
  $response = curl_exec($curl);
  curl_close($curl);  
  return json_decode($response,true);
	}
}