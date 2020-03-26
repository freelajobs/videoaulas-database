<?php

require_once "../class/ClPurchase.php";

$type = isset($_POST['type']) ? $_POST['type'] : "";
$card = isset($_POST['card']) ? $_POST['card'] : "";
$idCard = isset($_POST['idCard']) ? $_POST['idCard'] : "";
$idUser = isset($_POST['idUser']) ? $_POST['idUser'] : "";
$idModel = isset($_POST['idModel']) ? $_POST['idModel'] : "";
$priceHour = isset($_POST['priceHour']) ? $_POST['priceHour'] : "";
$totalHours = isset($_POST['totalHours']) ? $_POST['totalHours'] : "";
$location = isset($_POST['location']) ? $_POST['location'] : "";
$services = isset($_POST['services']) ? $_POST['services'] : "";
$totalPrice = isset($_POST['totalPrice']) ? $_POST['totalPrice'] : "";
$dataPurchase = isset($_POST['dataPurchase'])  ? $_POST['dataPurchase']  : "";
$dataSchendule = isset($_POST['dataSchendule']) ? $_POST['dataSchendule'] : "";
$hourSchendule = isset($_POST['hourSchendule']) ? $_POST['hourSchendule'] : "";

$file = 'logs/ReqPurchase.txt';

file_put_contents($file, json_encode(array(
	'data' => array(
		'card' => $card,
		'idCard' => $idCard,
		'idUser' => $idUser,
		'idModel' => $idModel,
		'priceHour' => $priceHour,
		'totalHours' => $totalHours,
		'services' => $services,
		'totalPrice' => $totalPrice,
		'location' => $location,
		'dataPurchase ' => $dataPurchase ,
		'dataSchendule' => $dataSchendule,
		'hourSchendule' => $hourSchendule
	)
)));

if ( $idCard == "") {
	$result = array(
		'status' => 'true',
		'error'  => '',
		'data'   => null
	);
} else {
	$result = array(
		'status' => 'true',
		'error'  => '',
		'data'   => null
	);
}
//Create Json
$json = json_encode($result);

//Return Json or error
echo $json ? $json : json_last_error_msg();
?>
