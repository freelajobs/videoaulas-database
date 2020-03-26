<?php

require_once "../class/ClSchendule.php";

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
    $data  = json_decode($postdata, true);
	$type  = $data['type'];
	$token = $data['id'];
	$date  = $data['date'];
	
	switch ($type) {
		case 'getAll':
			$result = $ClassSchendule->GetAll(
				$token, 
				$ClassSchendule->ConvertDataServer($date)
			);
			break;
		case 'getOne':
			$result = $ClassSchendule->GetOne($token);
			break;
		case 'update':
			$data = json_decode($data['data'], true);
			$days = array();
			$max_hours = array();
			foreach ($data as $key => $value) {
				$address = array();
				$day =  array();
				$open = false;
				$day_hours = null;
				// Start Hours
				$last_hour = null;
				foreach ($value['hours'] as $hour_key => $hour_value) {
					$idHour = $hour_value['id'];
					$idLocation = $hour_value['idLocation'] != -1 ? $hour_value['idLocation'] : "-";
					$selected = $hour_value['selected'] != "" ? $hour_value['selected'] : 0;
					if ($open == false)
					{
						$open = $selected == 1 ? 1 : 0;
					}

					$hourData = "[" . $idHour . "." . $idLocation . "." . $selected . "]";
					array_push($day, $hourData);

					// Create day_hours
					if($idLocation !== '-' && $open) {
						if($day_hours == null && $last_hour == null) {
							$day_hours = array (
								array(
									'id'=>$idLocation,
									'hour'=>array($idHour)
								)
							);
						} else {
							$last_hour = $idHour;
							$obj = null;
							foreach ($day_hours as $key => $value) {
								$obj = array(
									'id'=>$idLocation,
									'hour'=>array($idHour)
								);
								if($value['id'] == $idLocation) {
									array_push($day_hours[$key]['hour'], $idHour);
									$obj = null;
									break;
								} 
							}
							if($obj != null) {
								array_push($day_hours, $obj);
							}
						}
					}
				}
				// create days
				array_push($days, array(
					"open"=>$open,
					"schendule"=>$day
				));

				// Populate max_hours
				if($day_hours != null) {
					array_push($max_hours, $day_hours);
				}
			}

			$sequence_hours = array();
			for ($i=0; $i < sizeof($max_hours); $i++) { 
				$locations = $max_hours[$i];
				for ($j=0; $j < sizeof($locations); $j++) { 
					$hours = $locations[$j]['hour'];
					$size = sizeof($hours);
					$hour = $ClassSchendule->getLongestSeq($hours, $size);
					array_push($sequence_hours,
						array(
							'id'=>$locations[$j]['id'],
							'hour'=> $hour
						)
					);
				}
			}

			// filter hours
			$filter_hour = array();
			for ($i=0; $i < sizeof($sequence_hours); $i++) { 
				$object = $sequence_hours[$i];
				$add = true;
				for ($j=0; $j < sizeof($sequence_hours); $j++) { 
					$compare_object = $sequence_hours[$j];
					if(
						$object['id'] == $compare_object['id'] &&
						$object['hour'] < $compare_object['hour']
					) {
						$add = false;
						break;
					}
				}
				if($add == true) {
					array_push($filter_hour, $object);
				}
			}

			// verify exist
			$response = $ClassSchendule->GetExist($token);
			if ($response["status"] === true) {
				$result = $ClassSchendule->Update($token, $days);
			} else {
				$result = $ClassSchendule->Add($token, $days);
			}

			// update hour
			if($result["status"] === true) {
				foreach ($filter_hour as $key => $value) {
					
					$result = $ClassSchendule->UpdateTimeAddress(
						$filter_hour[$key]['id'],
						$filter_hour[$key]['hour']
					);
					if($result["status"] === false) {
						break;
					}
				}
			}

			break;
		default:
			$result = $ClassSchendule->Get($token);
			break;
	}

} else {
	$result = array(
        'status' => 'false',
        'error' => $postdata
    );  
}

//Create Json
$json = json_encode($result);

//Return Json or error
echo $json ? $json : json_last_error_msg();

?>