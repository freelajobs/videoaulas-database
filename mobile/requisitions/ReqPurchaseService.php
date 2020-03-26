<?php

require_once "../class/ClPurchase.php";

$type = isset($_POST['type']) ? $_POST['type'] : "";

$card = isset($_POST['card']) ? $_POST['card'] : [];

$idUser = isset($_POST['idUser']) ? $_POST['idUser'] : "";
$idModel = isset($_POST['idModel']) ? $_POST['idModel'] : "";

$priceHour = isset($_POST['priceHour']) ? $_POST['priceHour'] : "";
$totalHours = isset($_POST['totalHours']) ? $_POST['totalHours'] : "";
$location = isset($_POST['location']) ? $_POST['location'] : "";
$services = isset($_POST['services']) ? $_POST['services'] : [];
$totalPrice = isset($_POST['totalPrice']) ? $_POST['totalPrice'] : "";
$dataPurchase = isset($_POST['dataPurchase'])  ? $_POST['dataPurchase']  : "";
$dataSchendule = isset($_POST['dataSchendule']) ? $_POST['dataSchendule'] : "";
$hourSchendule = isset($_POST['hourSchendule']) ? $_POST['hourSchendule'] : "";

$purchaseDate = date('Y-m-d H:i:s');

$log = 'logs/PurchaseService.txt';

// TODO: Efetuar a compra então fazer isso ai
// Get Model Schendule for this day/hours
// Update Model Schendule this day/Hours
// Create Ticket

$result = array(
	'status' => 'true',
	'error'  => '',
	'data'   => array(
		'id' => '1'
	)
);

//Create Json
$json = json_encode($result);

// Save Log
file_put_contents($log, $json);

//Return Json or error
echo $json ? $json : json_last_error_msg();
?>
