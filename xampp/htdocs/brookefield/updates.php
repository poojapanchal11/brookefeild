<?php
$uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri_segments = explode('/', $uri_path);
$id = $uri_segments[2];
$curl = curl_init('http://www.eparichaya.com/googletagapi/gtag/js/updates?META_DATA=e-PARICHAYA5140425864&id='.$id); 
curl_setopt($curl, CURLOPT_FAILONERROR, true); 
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true); 
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);   
$result = curl_exec($curl); 
echo $result; 
?>
