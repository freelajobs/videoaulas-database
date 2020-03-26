<?php
require_once "../class/ClModels.php";
require_once "../class/ClUserComment.php";

$log = 'logs/GetModel.txt';

$id  = isset($_POST['id']) ? $_POST['id'] : "1";
// Pega as favoritas do usuário

// Pegar a modelo
// Pegar os locais
// Pegar os comentários
// Pegar os serviços

$services = $ClassModels->GetAllServices();
$model = $ClassModels->GetModel($id);

if ($model['status'] === true) {
    $error = null;
    //GetLocations
    $locations = $ClassModels->GetModelLocations($id);
    if ($locations['status'] === true) {
        $model['data']['locations'] = $locations['data'];
        $model['data']['max_time'] = $ClassModels->MaxAttributeArray(
            $locations['data'], 
            'max_time'
        );
        $model['data']['max_time'] = $model['data']['max_time'] . '.00';
    } else {
        $error = 'Não foi possível encontrar os locais de atendimento desta modelo';
    }
    //GetComments
    if ($error == null) {
        $comment = $ClassUserComment->GetForPartner($id);
        if ($comment['status'] === true) {
            $model['data']['comments'] = $comment['data'];
        } else if ($comment['error'][0] === "00000") {
        } else {
            $error = 'Não foi possível encontrar os comentários desta modelo';
        }
    }
    //GetServices
    if ($error == null) {
        $model_services = $ClassModels->GetModelServiçe($id);
        if ($model_services['status'] === true) {
            $model_services = $ClassModels->SortServices(
                $services['data'], 
                $model_services['data']
            );
            $model['data']['price'] = $model_services['data'][0]['price'];
            unset($model_services['data'][0]);
            if (sizeof($model_services['data'] > 0)) {
                for ($k = 1; $k < sizeof($model_services['data']); $k++) {
                    $service = $model_services['data'][$k];
                    if ($service['active'] == true) {
                        array_push(
                            $model['data']['services'], 
                            Array(
                                'id' => $service['id'],
                                'name' => $service['name'],
                                'price' => $service['price'],
                            )
                        );
                    }
                }
            }

        } else {
            $error = 'Não foi possível encontrar os comentários desta modelo';
        }
    }

    $result = $error === null ? 
        $model : 
        $result = Array (
            "status" => 'false',
            "error" => $error
        ); 
    // $comments  = $ClassModels->GetModel($id);
    // $services  = $ClassModels->GetModel($id);
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
