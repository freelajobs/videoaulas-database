<?php

require_once "../class/ClLogin.php";

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
    $data = json_decode($postdata, true);
    $mail = $data['mail'];
	$password = $data['password'];

	$result = $ClassLogin->Login(
		$mail,
		$password
	);

	if ($result['error'][0] === '00000') {
		$result['error'] = 'Login e/ou senha inválido';
	}

} else {
	$result = array(
        'status' => 'false',
        'error' => $postdata
    );  
}

//Create Json
$json = json_encode($result);

//Return Json or error
echo $json ? $json : json_last_error_msg();
?>
