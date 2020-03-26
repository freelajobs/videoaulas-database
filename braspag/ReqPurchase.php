<?php

include('../http/library/Requests.php');

// Authorization
$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJjbGllbnRfbmFtZSI6IlJlZFdpbmRvdyIsImNsaWVudF9pZCI6ImQ1YzAzZDE1LTdkMjItNDUyMC1iMjkxLTFjYjM0MGNmMjA4NCIsInNjb3BlcyI6WyJ7XCJTY29wZVwiOlwiU3BsaXRNYXN0ZXJcIixcIkNsYWltc1wiOltdfSIsIntcIlNjb3BlXCI6XCJDaWVsb0FwaVwiLFwiQ2xhaW1zXCI6W119Il0sInJvbGUiOlsiU3BsaXRNYXN0ZXIiLCJDaWVsb0FwaSJdLCJpc3MiOiJodHRwczovL2F1dGhzYW5kYm94LmJyYXNwYWcuY29tLmJyIiwiYXVkIjoiVVZReGNVQTJjU0oxZmtRM0lVRW5PaUkzZG05dGZtbDVlbEI1SlVVdVFXZz0iLCJleHAiOjE1NTA3NjkzOTgsIm5iZiI6MTU1MDY4Mjk5OH0.u5DnC7IFrjfgRsgi8QEqLdV33jIIDUea4WcvvwMXJFQ';
$credit = true;
$data_body = file_get_contents('php://input');
$value = 15000;
$split = true;

// Url Token
$endpoint='https://apisandbox.cieloecommerce.cielo.com.br/';
$requisition='1/sales/';
$url = $endpoint.$requisition;

// Header
$header = array(
  'Authorization: Bearer ' . $token,
  'Content-Type: application/json',
  'cache-control: no-cache'
);

// Data

// Body
$customer = array(
  "name"         => "Teste Accept",
  "email"        => "teste@teste.com.br",
  "identity"     => "0752879980",
  "identitytype" => "CPF"
);

// Card
$card = array(
  "CardNumber"     => "4481530710186111",
  "Holder"         => "Yamilet Taylor",
  "ExpirationDate" => "12/2019",
  "SecurityCode"   => "693",
  "Brand"          => "Visa",
  "SaveCard"       => "false"
);

// Purchase
$payment = array(
  "type"           => $credit === true ? "creditcard" : "debitcard", // splittedcreditcard, splitteddebitcard, creditcard, debitcard
  "amount"         => $value, //centavos
  "capture"        => true,
  "installments"   => 1,
  "softdescriptor" => "Red Window", //nome na fatura
  $credit === true ? "CreditCard" : "DebitCard" => $card
);

// fraudanalysis
$fraudanalysis = array(
  "sequence"         => "analysefirst",
  "sequencecriteria" => "onsuccess",
  "provider"         => "cybersource",
  "captureonlowrisk" => false,
  "voidonhighrisk"   => false,
  "totalorderamount" => $value,
  "browser"          => array(
    "ipaddress"           => getHostByName(getHostName()),
    "browserfingerprint"  => "123456654322"
  ),
  "cart"             => array(
    "isgift"          => false,
    "returnsaccepted" => false,
    "items"           => array(
      array(
        "name"        => "Produto teste",
        "quantity"    => 1,
        "sku"         => 563, //id do produto no meu sistema
        "unitprice"   => number_format((float)$value / 100, 2, '.', '') //preço em R$
      )
    )
  )
);

$payment['fraudanalysis'] = $fraudanalysis;

// Split
if($split === true) {
  $splitpayments = array(
    array(
      "subordinatemerchantid" => "e59a65be-b1d7-4989-b61e-d13258b8495a",
      "amount"                => $value/2,
      "fares"                 => array(
        "mdr" => 5,
        "fee" => 150
      )
    ),
    array(
      "subordinatemerchantid" => "e59a65be-b1d7-4989-b61e-d13258b8495a",
      "amount"                => $value/2,
      "fares"                 => array(
        "mdr" => 5,
        "fee" => 150
      )
    )
  );
  $payment['splitpayments'] = $splitpayments;
}

// Create Body
$body = array(
  'merchantorderid' => 1000,
  'customer'=>$customer,
  'payment'=> $payment
);

// Convert to json
$body_data = json_encode($body, true);

echo $body_data;
return ;
// Call server
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $data_body,
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