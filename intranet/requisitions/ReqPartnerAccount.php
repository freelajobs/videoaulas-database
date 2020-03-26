<?php

require_once "../class/ClPartnerAccount.php";

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
    $data = json_decode($postdata, true);
    $type = $data['type'];
    
	// options
	switch ($type) {
		case 'get':
			$result = $ClassAccount->GetData(
				$data['id']
			);
		break;
		case 'add':
			$result = $ClassAccount->Add(
                $data['id_partner'],
				$data['bank'],
				$data['agency'],
				$data['account'],
				$data['account_type']
			);
		break;
		case 'update':
			$result = $ClassAccount->Update(
				$data['id'],
				$data['bank'],
				$data['agency'],
				$data['account'],
				$data['account_type'],
				$ClassAccount->ConvertBoolServer($data['active'])
			);
		break;
		default:
			$result = $ClassAccount->Get($data['id_partner']);
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
