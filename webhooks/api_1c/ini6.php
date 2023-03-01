<?php

$data = array(
    'id' => $_GET["id"],
	'date' => $_GET["date"],
);

$sdata = date("Y-m-d",strtotime($data["date"]));
$data = array(
    'id' => $_GET["id"],
    'date' => $sdata,
);  

$content = json_encode($data);
$ch = curl_init('http://192.168.0.10/base1c/hs/stop/');                                                                      
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
curl_setopt($ch, CURLOPT_POSTFIELDS, $content);                                                                  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json',                                                                                
    'Content-Length: ' . strlen($content),
    'Authorization: Basic '. base64_encode("rest:123"))                                                                       
);                                                                                                                   
$json_response = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
$response = json_decode($json_response, true);
$result = json_decode($response);
?>