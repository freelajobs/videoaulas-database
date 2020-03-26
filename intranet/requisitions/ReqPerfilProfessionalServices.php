<?php

require_once "../class/ClPartner.php";

$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$id_user = filter_input(INPUT_POST, 'id_user', FILTER_SANITIZE_NUMBER_INT);
$active = filter_input(INPUT_POST, 'active', FILTER_VALIDATE_BOOLEAN);
$value = filter_input(INPUT_POST, 'value', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

$msg_error = "";

if (!isset($type)) {
    $msg_error = $msg_error . "type ";
}
if (!isset($id_user)) {
    $msg_error = $msg_error . " - id_user ";
}

if (strlen($msg_error) > 0) {
    $result = array(
        'status' => 'false',
        'error' => "campos com erro: " . $msg_error
    );
} else {
    switch ($type) {
        case 'update':
            $partner_services = $ClassPartner->GetPartnerServices($id_user);
            $active = $id === 1 ? true : $active;
            $result = $ClassPartner->UpdateService(
                    $id_user, $id, $value, $active === false ? 0 : 1, $partner_services['data']
            );
            break;
        default:
            $all_services = $ClassPartner->GetAllServices();
            $partner_services = $ClassPartner->GetPartnerServices($id_user);
            $red_window_values = $ClassPartner->GetRedWindowPercents();
            $result = $ClassPartner->SortPartnerToWeb($all_services['data'], $partner_services['data']);

            $result['data'] = array(
                "red_value" => $red_window_values['data'],
                "services" => $result['data']
            );
            break;
    }
}

//Create Json
$json = json_encode($result);

//Return Json or error
echo $json ? $json : json_last_error_msg();
