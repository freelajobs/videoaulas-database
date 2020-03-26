<?php

require_once "../class/ClMarketingStatus.php";

$type = isset($_POST['type']) ? $_POST['type'] : "";

//Login options
switch ($type) {
	case 'add':
		$result = $ClassMarketingStatus->Add(
			$_POST['name'],
			$_POST['description']
		);
	break;
	case 'update':
		$result = $ClassMarketingStatus->Update(
			$_POST['id'],
			$_POST['name'],
			$_POST['description'],
			$_POST['blocked'] == "true" ? 1 : 0
		);
	break;
	case 'get':
		$result = $ClassMarketingStatus->Get(
		$_POST['id']
		);
	break;
	default:
		$result = $ClassMarketingStatus->GetAll();
		break;
}

//Create Json
$json = json_encode($result);

//Return Json or error
echo $json ? $json : json_last_error_msg();

?>
