<?php
require_once "../class/ClModels.php";
$log = 'logs/GetModels.txt';

// Pega todos os serviços do servidor
$services = $ClassModels->GetAllServices();

$page = $_POST["page"];
$filter = isset($_POST['filter']) ? $_POST['filter'] : null;

// pega todas as modelos

if($filter === null) {
    $models = $ClassModels->Get();
} else {
    $models = $ClassModels->Get();
}

if ($models['status'] === false) {
	$result = Array (
		"status" => 'false',
		"error" => 'não há modelos disponíveis, no momento.'
	);
} else {
    // Arruma os serviços de cada modelo
    for ($i = 0; $i < sizeof($models['data']); $i++) {
        $model = $models['data'][$i];
        $model_services = $ClassModels->SortServices($services['data'], $model['services']);
        $model['price'] = $model_services['data'][0]['price'];
        // Remove o serviço de hora
        unset($model_services['data'][0]);
        $model['services'] = Array();
        if (sizeof($model_services['data'] > 0)) {
            for ($k = 1; $k < sizeof($model_services['data']); $k++) {
                $service = $model_services['data'][$k];
                if ($service['active'] == true) {
                    array_push($model['services'], $service['name']);
                }
            }
        }
        
        $models['data'][$i] = $model;
    }
    
    // Eventualmente usar o paginator
    $models['page'] = Array(
        'number' => 1,
        'total' => '1',
    ); 

    $result = $models;
}

//Create Json
$json = json_encode($result);

// Save Log
file_put_contents($log, "\n Post: \n" . json_encode($_POST) . "\n Responde: \n". $json);

//Return Json or error
echo $json ? $json : json_last_error_msg();
?>
