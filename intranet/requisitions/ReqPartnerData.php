<?php

require_once "../class/ClPartner.php";

$postdata = file_get_contents("php://input");
if(isset($postdata) && !empty($postdata))
{
    $data = json_decode($postdata, true);
    $type = $data['type'];
	
    // options
	switch ($type) {
		case 'get_personal':
			$result = $ClassPartner->GetPersonalData(
				$data['id']
			);
		break;
		case 'get_profissional':
			$result = $ClassPartner->GetProfissionalData(
				$data['id']
			);
		break;
		case 'update':
			$result = $ClassPartner->UpdatePersonalProfile(
				$data['id'],
				$data['first_name'],
				$data['last_name'],
				$data['password'],
				$data['phone'],
				$data['cel_phone'],
				$data['age'],
				$data['cpf'],
				$data['address']
			);
		break;
		case 'update_profile_profissional':
			$result = $ClassPartner->UpdateProfissionalProfile(
				$data['id'],
				$data['nick'],
				$data['description']
			);
		break;
		default:
            $result = array(
                'status' => 'false',
                'error' => 'função não declarada'
            );  
		break;
	}
} else {
	$result = array(
		'status' => 'false',
		'error' => 'dados não enviados'
	);  
}
		
//Create Json
$json = json_encode($result);

//Return Json or error
echo $json ? $json : json_last_error_msg();
