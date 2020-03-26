<?php

require_once "../class/ClPartner.php";

$postdata = file_get_contents("php://input");
if(isset($postdata) && !empty($postdata))
{
    $data = json_decode($postdata, true);
    $type = $data['type'];

    // options
	switch ($type) {
		case 'add':
            $result = $ClassPartner->AddAddress(
                $data['token'],
                $data['address'],
                $data['latitude'],
                $data['longitude'],
                $data['description'] 
            );
		break;
		case 'update':
            $result = $ClassPartner->UpdateAddress(
                $data['pk'],
                $data['address'],
                $data['latitude'],
                $data['longitude'],
                $data['description'] 
            );
		break;
		case 'remove':
            $result = $ClassPartner->RemoveAddress(
                $data['pk']
            );
		break;
		default:
            $result = $ClassPartner->GetAddress(
                $data['pk']
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