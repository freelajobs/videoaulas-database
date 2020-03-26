<?php

require_once "../class/ClUser.php";
require_once "../class/ClUserSignature.php";
require_once "../class/ClModels.php";

$log = 'logs/Login.txt';

$type     = isset($_POST['type']) ? $_POST['type'] : "";
$mail     = isset($_POST['mail']) ? $_POST['mail'] : "";
$password = isset($_POST['password']) ? $_POST['password'] : "";
$imei     = isset($_POST['imei']) ? $_POST['imei'] : "";

//Verify email exist
$result = $ClassUser->GetRegister(
	$mail
);

if ($result['status'] === true) {
	//Verify password exist
	$result = $ClassUser->Login(
		$mail,
		$password
	);
	if ($result['status'] === true) {
		$id = $result['data']['id'];
		
		// Get User Data
		$result = $ClassUser->GetData($id);
		$categories = $ClassModels->GetCategories();
		$services = $ClassModels->GetServices();
		if ($result['status'] === true) {
			// Get Signature
			$signature = $ClassUserSignature->Get($result['data']['id']);
			$result['data']['signature'] = $signature['status'] === true ? 
				$signature['data'] : Array();
			$result['data']['services'] = $services['data'];
			$result['data']['categories'] = $categories['data'];
			
			// Update Device
			$ClassUser->UpdateDevice($id, $imei);
		} else {
			$result = Array (
				"status" => 'false',
				"error" => 'Tente novamente mais tarde'
			);
		}
	} else {
		$result = Array (
			"status" => 'false',
			"error" => 'Senha errada'
		);
	}
} else {
	$result = Array (
        "status" => 'false',
        "error" => 'Usuário não cadastrado'
    );
}

//Create Json
$json = json_encode($result);

// Save Log
file_put_contents($log, 
	"\n Requisition: " . $_SERVER['REQUEST_METHOD'] .
	"\n type: " . $type .
	"\n mail: " . $mail .
	"\n password: " . $password .
	"\n imei: " . $imei . 
	"\n response: " . $json
);

//Return Json or error
echo $json ? $json : json_last_error_msg();
?>
