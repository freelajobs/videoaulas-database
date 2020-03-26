<?php

require_once "../class/ClSchendule.php";

$type = isset($_POST['type']) ? $_POST['type'] : "";
// $type= "getOne";
if($type == "getAll")
{
	$token = isset($_POST['token']) ? $token['token'] : 1;
	$date  = isset($_POST['date']) ? $_POST['date'] : date("Y-m-d H:i:s");
	$result = $ClassSchendule->GetAll($token, $date);
}
else if($type == "getOne")
{
	$token = isset($_POST['token']) ? $_POST['token'] : 1;
	$result = $ClassSchendule->GetOne($token);

	file_put_contents("post.log", "",true);
	file_put_contents("post.log",print_r($result, true));
}
else if($type == "update")
{
	$token = json_decode($_POST['token'], true);
	$data = json_decode($_POST['data'], true);
	$days = array();
	foreach ($data as $key => $value) {
		$day =  array();
		$open = false;
		foreach ($value['hours'] as $hour_key => $hour_value) {
				$idHour = $hour_value['id'];
				$idLocation = $hour_value['idLocation'] != -1 ? $hour_value['idLocation'] : "-";
				$selected = $hour_value['selected'] != "" ? $hour_value['selected'] : 0;
				if ($open == false)
				{
						$open = $selected == 1 ? 1 : 0;
				}
				$hourData = "[" . $idHour . "," . $idLocation . "," . $selected . "]";
				array_push($day, $hourData);
		}
		array_push($days, array(
			"open"=>$open,
			"schendule"=>$day
		));
	}
	$response = $ClassSchendule->GetExist($token, $days);
	if ($response["status"] == true) {
		$result = $ClassSchendule->Update($token, $days);
	} else {
		$result = $ClassSchendule->Add($token, $days);
	}
}
else
{
	$token = isset($token) ? $token : 1;
	$result = $ClassSchendule->Get($token);
}

//Create Json
$json = json_encode($result);

//Return Json or error
echo $json ? $json : json_last_error_msg();
?>
