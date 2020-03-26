<?php

require_once "../class/ClBankAccount.php";

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
    $data = json_decode($postdata, true);
    $type = $data['type'];
    
	// options
	switch ($type) {
		case 'get':
			$result = $ClassBankAccount->GetData(
				$data['id']
			);
		break;
		case 'add':
			$result = $ClassBankAccount->Add(
				$data['bank'],
				$data['agency'],
				$data['account'],
				$data['account_type'],
				$data['payment_type']
			);
		break;
		case 'update':
			$result = $ClassBankAccount->Update(
				$data['id'],
				$data['bank'],
				$data['agency'],
				$data['account'],
				$data['account_type'],
				$data['payment_type'],
				$ClassBankAccount->ConvertBoolServer($data['active'])
			);
		break;
		default:
			$result = $ClassBankAccount->Get();
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
