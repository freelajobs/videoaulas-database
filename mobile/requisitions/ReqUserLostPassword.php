<?php

require_once "../../server/DbMailer.php";
require_once "../class/ClUser.php";

$mail = isset($_POST['mail']) ? $_POST['mail'] : "";

$log = 'logs/LostPassword.txt';

$result = $ClassUser->GetPassword($mail);

if ($result['status'] === true) {
	$password = $result['data']['password'];
	$nick = $result['data']['nick_name'];

	//Esqueceu a senha
	$html = $DbMailer->LostPassword(
		$nick, 
		$password, 
		$mail
	);

	$subject = 'Esqueceu sua senha?';
	$result = $DbMailer->SendMail(
		$mail, 
		array(), 
		$subject, 
		utf8_decode($html)
	);
} else {
	$result = Array (
		"status" => 'false',
		"error" => 'Este email ainda não está cadastrado na Red Window.'
	);
} 
//Create Json
$json = json_encode($result);

// Save Log
file_put_contents($log, $json);

//Return Json or error
echo $json ? $json : json_last_error_msg();
?>
