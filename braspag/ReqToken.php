<?php

include('../http/library/Requests.php');

// Authorization
$client_id="d5c03d15-7d22-4520-b291-1cb340cf2084";
$client_secret="XyRZAPEz55HxrXnLoka0uXx9OMOjqR6An+IrY9jqhT8=";

$base64 = base64_encode($client_id .":".$client_secret);

// Url Token
$endpoint='https://authsandbox.braspag.com.br/';
$requisition='oauth2/token';

$url= $endpoint.$requisition;
// Header
$header=array(
  'Authorization'=>'Basic ' . $base64,
  'Content-Type'=>'application/x-www-form-urlencoded',
);

// Body
$body=array(
  'grant_type' => 'client_credentials'
);

// Register loader
Requests::register_autoloader();

// Request
$request = Requests::post(
  $url, 
  $header, 
  $body
);

// Return Data
$data = json_decode($request->body, true);

if ($data['error'] !== null) {
  $result = array(
    'status' => false,
    'error' => array(
      'type'=>$data['error'],
      'description'=>$data['error_description'],
    )
  );  
} else {
  $result = array(
    'status' => true,
    'data' => $data
  ); 
}

//Create Json
$json = json_encode($result);

//Return Json or error
echo $json ? $json : json_last_error_msg();

?>
