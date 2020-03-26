<?php

include('../http/library/Requests.php');

// Url Token
$token = $_POST['token'];
$pk = $_POST['pk'];

$endpoint='https://apiquerysandbox.cieloecommerce.cielo.com.br/';
$requisition='1/sales/';
$url= $endpoint.$requisition.$pk;

// Header
$header=array(
  'Authorization: Bearer ' . $token,
  'Content-Type: application/json',
  'cache-control: no-cache'
);

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => $header
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}

?>
