<?php
require_once "../class/ClPartnerPlans.php";
require_once "../class/ClPartner.php";
require_once "../../braspag/ClBraspag.php";

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	$data = json_decode($postdata, true);

	$plan_pk 	 =  $data['plan_pk'];
	$purchase_pk =  $data['purchase_pk'];

	$token = $ClassBraspag->GenerateToken();

	// GetPurchase

	if($token['status'] === false) {
		$result = array(
			'status' => false,
			'error' => $token['error']
		); 
	} else {
		// Token
		$token_pk = $token['data']['access_token'];

		// Create Purchase
		$result = $ClassBraspag->PurchaseCancel(
			$token_pk,
			$purchase_pk
		);

		// Cancel Plan
	} // Token
	
} else {
	$result = array(
        'status' => false,
        'error' => $postdata
    );  
}

//Create Json
$json = json_encode($result);

//Return Json or error
echo $json ? $json : json_last_error_msg();
?>
