<?php
require_once "../class/ClPartnerPlans.php";

//TODO: Efetuar a compra então fazer isso ai
$plans = $ClassPlan->Get();

if ($plans['status'] === false) {
	$result = Array (
		"status" => 'false',
		"error" => 'não há planos disponíveis, no momento.'
	);
} else {
    $result = $plans;
}

//Create Json
$json = json_encode($result);

//Return Json or error
echo $json ? $json : json_last_error_msg();
?>
