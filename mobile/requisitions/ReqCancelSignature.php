<?php

require_once "../class/ClUserComment.php";
require_once "../class/ClUserSignature.php";
require_once "../class/ClUser.php";
require_once "../class/ClPurchase.php";
$log = 'logs/CancelSignature.txt';

$type   = isset($_POST['type']) ? $_POST['type'] : "";
$idUser = isset($_POST['idUser']) ? $_POST['idUser'] : "";
$idPlan = isset($_POST['idPlan']) ? $_POST['idPlan'] : "";
$motive = isset($_POST['motive']) ? $_POST['motive'] : "";
$cancelDate = date('Y-m-d H:i:s');

$comment = $ClassUserComment->Add(
		$idUser, 
		$idPlan, 
		NULL, 
		NULL, 
		"plan_cancel", 
		$motive, 
		NULL, 
		$cancelDate
);

if ($comment['status'] === true) {
	$plan = $ClassUserSignature->CancelSignature($idPlan);
	if ($plan['status'] === true) {
		// TODO: Remove do sistema de pagamento e só ai devolve o user
		$user = $ClassUser->GetData($idUser);
		$user['data']['signature'] = Array();
		$result	= $user;
	} else {
		$result = Array (
			"status" => 'false',
			"error" => 'Não cancelar seu plano, tente novamente mais tarde.',
			"log" => $plan['error']
		);
	}
} else {
	$result = Array (
        "status" => 'false',
		"error" => 'Não salvar seu comentário, tente novamente mais tarde.',
		"log" => $comment['error']
    );
}

//Create Json
$json = json_encode($result);

// Save Log
file_put_contents($log, $json);

//Return Json or error
echo $json ? $json : json_last_error_msg();
?>
