<?php

require_once "../class/ClMarketStatus.php";

$type = isset($_POST['type']) ? $_POST['type'] : "";

//Login options
switch ($type) {
	case 'add':
		$result = $ClassMarketStatus->Add(
			$_POST['name'],
			$_POST['description']
		);
	break;
	case 'update':
		$result = $ClassMarketStatus->Update(
			$_POST['id'],
			$_POST['name'],
			$_POST['description'],
			$_POST['blocked'] == "true" ? 1 : 0
		);
	break;
	case 'get':
		$result = $ClassMarketStatus->Get(
		$_POST['id']
		);
	break;
	default:
		$result = $ClassMarketStatus->GetAll();
		break;
}

//Create Json
$json = json_encode($result);

//Return Json or error
echo $json ? $json : json_last_error_msg();

?>
