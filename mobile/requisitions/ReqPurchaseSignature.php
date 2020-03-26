<?php
require_once "../class/ClPurchase.php";
require_once "../class/ClUserPlans.php";
require_once "../class/ClUserSignature.php";
require_once "../class/ClUser.php";
$log = 'logs/PurchaseSignature.txt';

$_id	 			= isset($_POST['idUser']) ? $_POST['idUser'] : "1";
$_idPlan	 		= isset($_POST['idPlan']) ? $_POST['idPlan'] : "1";
$card 				= isset($_POST['card']) ? $_POST['card'] : [
	"number"=>"0000000000000001",
	"name"=>"rodrigo pimentel",
	"validate"=>"22-2222",
	"code"=>"2222"
];

$plan = $ClassPlan->GetId($_idPlan);


if ($plan['status'] === false) {
	$result = Array (
		"status" => 'false',
		"error" => 'plano não encontrado, por favor tente novamente mais tarde.'
	);
}

$plan = $plan['data'];
$purchaseDate = date('Y-m-d H:i:s');
$expireDate = date('Y-m-d H:i:s', strtotime('+' . $plan['duration'] .' months'));
$upgrade = $plan['power'] === 'basic' || $plan['power'] === 'medium';

$result = Array (
	"status" => 'false',
	"error" => 'não foi possivel finalizar sua assinatura, por favor tente mais tarde'
);

$json = json_encode($result);

file_put_contents($log, json_encode($card));

echo $json ? $json : json_last_error_msg();

return;
//TODO: Efetuar a compra então fazer isso ai
$signature = $ClassUserSignature->Add(
	$_id,
	$plan['name'],
	$plan['description'],
	$plan['full_description'],
	$purchaseDate,
	$plan['duration'],
	$expireDate,
	$plan['value'],
	$upgrade
);

if ($signature['status'] === true) {
	$user = $ClassUser->GetData($_id);
	$new_signature = $ClassUserSignature->Get($user['data']['id']);

	$user['data']['signature'] = $new_signature['data'];
	$result = $user;
} else {
	$result = Array (
		"status" => 'false',
		"error" => 'não foi possivel finalizar sua assinatura, por favor tente mais tarde'
	);
}

//Create Json
$json = json_encode($result);

// Save Log
file_put_contents($log, $json);

//Return Json or error
echo $json ? $json : json_last_error_msg();
?>
