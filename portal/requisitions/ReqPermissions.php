<?php

require_once "../class/ClPermissions.php";

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	$data = json_decode($postdata, true);
	$type = $data['type'];
		
	//Login options
	switch ($type) {
		case 'add':
			$result = $ClassPermissions->Add(
				$data['name'],
				$data['description'],
				$ClassPermissions->ConvertBoolServer($data['service_type']),
				$ClassPermissions->ConvertBoolServer($data['service_status']),
				$ClassPermissions->ConvertBoolServer($data['service_payment']),
				$ClassPermissions->ConvertBoolServer($data['service_tickets']),
				$ClassPermissions->ConvertBoolServer($data['partner_category']),
				$ClassPermissions->ConvertBoolServer($data['partner_consult']),
				$ClassPermissions->ConvertBoolServer($data['client']),
				$ClassPermissions->ConvertBoolServer($data['market_status']),
				$ClassPermissions->ConvertBoolServer($data['market_consult']),
				$ClassPermissions->ConvertBoolServer($data['notifications']),
				$ClassPermissions->ConvertBoolServer($data['permissions']),
				$ClassPermissions->ConvertBoolServer($data['users']),
				$ClassPermissions->ConvertBoolServer($data['reports']),
				$ClassPermissions->ConvertBoolServer($data['plan_costs']),
				$ClassPermissions->ConvertBoolServer($data['plan_models']),
				$ClassPermissions->ConvertBoolServer($data['plan_clients'])
			);
		break;
		case 'update':
			$result = $ClassPermissions->Update(
				$data['id'],
				$data['name'],
				$data['description'],
				$ClassPermissions->ConvertBoolServer($data['service_type']),
				$ClassPermissions->ConvertBoolServer($data['service_status']),
				$ClassPermissions->ConvertBoolServer($data['service_payment']),
				$ClassPermissions->ConvertBoolServer($data['service_tickets']),
				$ClassPermissions->ConvertBoolServer($data['partner_category']),
				$ClassPermissions->ConvertBoolServer($data['partner_consult']),
				$ClassPermissions->ConvertBoolServer($data['client']),
				$ClassPermissions->ConvertBoolServer($data['market_status']),
				$ClassPermissions->ConvertBoolServer($data['market_consult']),
				$ClassPermissions->ConvertBoolServer($data['notifications']),
				$ClassPermissions->ConvertBoolServer($data['permissions']),
				$ClassPermissions->ConvertBoolServer($data['users']),
				$ClassPermissions->ConvertBoolServer($data['reports']),
				$ClassPermissions->ConvertBoolServer($data['plan_costs']),
				$ClassPermissions->ConvertBoolServer($data['plan_models']),
				$ClassPermissions->ConvertBoolServer($data['plan_clients'])
			);
		break;
		case 'remove':
			$result = $ClassPermissions->Remove(
				$data['id']
			);
		break;
		case 'get':
			$result = $ClassPermissions->Get(
				$data['id']
			);
		break;
		default:
			$result = $ClassPermissions->GetAll();
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

?>
