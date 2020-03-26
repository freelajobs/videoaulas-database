<?php

require_once "../class/ClModels.php";

$log = 'logs/Schendule.txt';

$type = isset($_POST['type']) ? $_POST['type'] : "";
$id = isset($_POST['id']) ? $_POST['id'] : "1";
$maxTime = isset($_POST['maxTime']) ? $_POST['maxTime'] : "1";

$locations = $ClassModels->GetModelLocations($id);

function CreateDay($diary, $day) {
	if (!is_null($diary)) {
		$day_hours = $day['hours'];
		$hours = Array();
		
		$last_hour = null;
		$max_hours = 0;
		for ($d = 0; $d < sizeof($day_hours); $d++) {
			if (
				(int)$diary['id'] === $day_hours[$d]['idLocation'] && 
				$day['open'] === true &&
				$day_hours[$d]['selected'] === false
			) {
				//Define Max Hour
				if ($last_hour + 1 === (int)$day_hours[$d]['id']){
					$max_hours = $max_hours + 1;
					$last_hour = (int)$day_hours[$d]['id'];
				} else {
					$last_hour = (int)$day_hours[$d]['id'];
					$max_hours = $max_hours === 0 ? 1 : $max_hours;
				}
				//Add Hour in array
				array_push(
					$hours, 
					$day_hours[$d]['id'] < 10 ? 
						'0' . $day_hours[$d]['id'] . ':00' : 
						$day_hours[$d]['id'] . ':00'
				);
			}
		}
		

		$old_max = explode(":", $diary['max_time']);
		if ((int)$old_max[0] > $max_hours) {
			$diary['max_time'] = (int)$old_max[0] < 10 ? 
				'0' . (int)$old_max[0] . ':00' : 
				(int)$old_max[0] . ':00';
		} else {
			$diary['max_time'] = $max_hours < 10 ? 
				'0'. $max_hours . ':00' : 
				$max_hours . ':00';
		}

		$day = Array(
				'name' => $day['name'],
				'open' => $day['open'],
				'hours' => sizeof($hours) === 0 ? null : $hours
		);
		
		if ($max_hours > 0) {
			$day['max_time'] = $max_hours < 10 ? 
				'0' . $max_hours . ':00' : 
				$max_hours . ':00';
		};

		array_push(
			$diary['days'], 
			$day
		);
	}
	return $diary;
}
if ($locations['status'] === true) {
	// Get Locales
	$schendule = $ClassModels->GetModelSchendule($id);
	if ($schendule['status'] === true) {
		// Create Locales Json
		$locals = $locations['data'];
		$diary = Array();
		for ($i = 0; $i < sizeof($locals); $i++) {
			$location = $locals[$i];
			$local = Array(
				'id' => $location['id'],
				'max_time' => (int)$location['max_time'] < 10 ? 
					'0' . $location['max_time'] . ':00' : 
					$location['max_time'] . ':00',
				'days' => Array()
			);
			array_push($diary, $local);
		}

		//Create Days in Locales Array 
		$weake = $schendule['data'];
		for ($i = 0; $i < sizeof($weake); $i++) {
			$day = $weake[$i];
			$diary[0] = CreateDay($diary[0], $day);
			$diary[1] = CreateDay($diary[1], $day);
			$diary[2] = CreateDay($diary[2], $day);
			$diary[3] = CreateDay($diary[3], $day);
		}

		//Create Result Array
		$result['status'] = true;
		$result['error'] = "";
		$result['data'] = $diary;
	} else {
		$result = Array (
			"status" => 'false',
			"error" => 'Não foi possível buscar essas informações no momento, tente mais tarde.'
		);
	}
} else {
	$result = Array (
		"status" => 'false',
		"error" => 'Não foi possível buscar essas informações no momento, tente mais tarde.'
	);
}

//Create Json
$json = json_encode($result);

// Save Log
file_put_contents($log, $json);

//Return Json or error
echo $json ? $json : json_last_error_msg();
?>
