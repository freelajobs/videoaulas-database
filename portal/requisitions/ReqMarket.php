<?php

require_once "../class/ClMarket.php";

$type = isset($_POST['type']) ? $_POST['type'] : "";

//Login options
switch ($type) {
	case 'add':
		$_app = $DbImages->SaveImage('market', 'app_image', 'app', $_POST['title']);
		$result = $ClassMarket->Add(
			$_POST['title'],
			$_POST['mail'],
			$_POST['url'],
			$_POST['duration'],
			$_POST['value'],
			$_POST['description'],
			$_app['name']
		);
	break;
	case 'update':
		$_app = $DbImages->SaveImage('market', 'app_image', 'app', $_POST['title']);
		$result = $ClassMarket->Update(
			$_POST['id'],
			$_POST['title'],
			$_POST['mail'],
			$_POST['url'],
			$_POST['duration'],
			$_POST['value'],
			$_POST['description'],
			!is_null($_app) ? $_app['name'] : null
		);
	break;
	case 'get':
		$result = $ClassMarket->Get(
		$_POST['id']
		);
	break;
	default:
		$result = $ClassMarket->GetAll();
		break;
}

//Create Json
$json = json_encode($result);

//Return Json or error
echo $json ? $json : json_last_error_msg();

?>
