<?php

require_once "../class/ClUser.php";
require_once "../class/ClUserSignature.php";

$log = 'logs/UserUpdateProfile.txt';

$type = isset($_POST['type']) ? $_POST['type'] : "";

$id = isset($_POST['id']) ? $_POST['id'] : "";

if (isset($_FILES['profileImage'])) { 
	$result = $DbImages->SaveImage('client', 'profileImage', 'profile', $id);

	$file = $result['name'];
	// Update Document Url 
	$result = $ClassUser->UpdateProfilePicture($id, $file);

	$result = $ClassUser->GetData($id);
	$signature = $ClassUserSignature->Get($id);
	$result['data']['signature'] = $signature['status'] === true ? 
		$signature['data'] : Array();

} else {
	$result = Array (
		"status" => 'false',
		"error" => 'Não foi possível atualizar sua imagem de perfil, tente novamente mais tarde.'
	);
}

//Create Json
$json = json_encode($result);

// Save Log
file_put_contents($log, $json);

//Return Json or error
echo $json ? $json : json_last_error_msg();
?>
