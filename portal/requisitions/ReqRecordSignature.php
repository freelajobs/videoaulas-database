<?php

require_once "../class/ClRecordSignature.php";

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
    $data = json_decode($postdata, true);
    $type = $data['type'];
    
	// options
	switch ($type) {
		case 'client':
			$result = $ClassRecordSignature->GetTotalClient();
		break;
		case 'model':
			$result = $ClassRecordSignature->GetTotalPartner();
		break;
		default:
			$result = $ClassRecordSignature->GetTotal();
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
