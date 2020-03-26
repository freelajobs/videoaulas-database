<?php

require_once "../../server/DbMailer.php";
require_once "../class/ClPartner.php";

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
    $data = json_decode($postdata, true);
    $mail = $data['mail'];

    //Register New User
    $result = $ClassPartner->GetPassword(
        $mail
    );

    if ($result['status'] === false) {
        $result['error'] = "Email inválido";
    } else {
        $password = $result['data']['password'];
        $nick = $result['data']['first_name'];

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
