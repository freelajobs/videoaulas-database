<?php

require_once "../class/ClUser.php";

$log = 'logs/UserRegister.txt';

$type = isset($_POST['type']) ? $_POST['type'] : "";
// Get Values
$mail	  	  = isset($_POST["login"]) ? $_POST["login"] : "";
$nick   	  = isset($_POST['nick']) ? $_POST['nick'] : "";
$name   	  = isset($_POST['name']) ? $_POST['name'] : "";
$last_name    = isset($_POST['last_name']) ? $_POST['last_name'] : "";
$phone 	 	  = isset($_POST['phone']) ? $_POST['phone'] : "";
$age    	  = isset($_POST['age']) ? $_POST['age'] : "";
$address  	  = isset($_POST['address']) ? $_POST['address'] : "";
$password  	  = isset($_POST['password']) ? $_POST['password'] : "";
$gender  	  = isset($_POST['gender']) ? $_POST['gender'] : "";
$registerDate = date('Y-m-d H:i:s');
$cpf    	  = isset($_POST['cpf']) ? $_POST['cpf'] : "";
$cpf_url 	  = isset($_FILES['documentImage']) ? $_FILES['documentImage'] : null;
$imei 		  = isset($_POST['imei']) ? $_POST['imei'] : "";

//Verify user exist
$result = $ClassUser->GetRegister(
	$mail
);

// Register User
if ($result['status'] === false) {
    $result = $ClassUser->AddUser(
		$mail,
		$nick,
		$name,
		$last_name,
		$phone,
		$age,
		$address,
		$password,
		$gender === 'false' ? 0 : 1,
		$registerDate,
		$cpf
	);
	// Save Document
	if ($result['status'] === true) { 
		$id = $result['data'];
		if (isset($_FILES['documentImage'])) {
			$result = $DbImages->SaveImage('documents', 'documentImage', 'cpf', $id);
			$file = $result['name'];
			// Update Document Url 
			$result = $ClassUser->UpdateDocument($id, $file);

			// Get User Data
			if ($result['status'] === true) {
				$result = $ClassUser->GetData($id);
			} else {
				$result = $ClassUser->GetData($id);
			}
			
			$result['data']['signature'] = Array();

			// Update Device
			$ClassUser->UpdateDevice($id, $imei);

		} else {
			// Get User Data
			$result = $ClassUser->GetData($id);
			// $result['data']['signature'] = Array();

			// Update Device
			$ClassUser->UpdateDevice($id, $imei);
		}
	}
} else {
	$result = Array (
        "status" => 'false',
        "error" => 'O email informado já está cadastrado.'
    );
}

//Create Json
$json = json_encode($result);

// Save Log
file_put_contents($log, 
	"\n Requisition: " . $_SERVER['REQUEST_METHOD'] .
	"\n type: " . $type .
	"\n mail: " . $mail .
	"\n nick: " . $nick .
	"\n name: " . $name .
	"\n last_name: " . $last_name .
	"\n phone: " . $phone .
	"\n age: " . $age .
	"\n address: " . $address .
	"\n password: " . $password .
	"\n gender: " . $gender .
	"\n registerDate: " . $registerDate .
	"\n cpf: " . $cpf .
	"\n imei: " . $imei .
	"\n response: " . $json 
);

//Return Json or error
echo $json ? $json : json_last_error_msg();
?>
