<?php
require_once "../class/ClPartnerPlans.php";
require_once "../class/ClPartner.php";
require_once "../../braspag/ClBraspag.php";

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	$data = json_decode($postdata, true);
	
	$purchase_name_note = "adega red";

	$pk 				=  $data['pk'];
	$plan_pk 			=  $data['plan_pk'];
	$cardNumber 		=  $data['cardNumber'];
	$cardHolder 		=  $data['cardHolder'];
	$expirationMonth 	=  $data['expirationMonth'];
	$expirationYear 	=  $data['expirationYear'];
	$ccv 				=  $data['ccv'];
	$flag 				=  $data['flag'];
	$is_credit 		    =  $data['is_credit'];
	$browserfingerprint =  $data['browserfingerprint'];

	$plan = $ClassPlan->GetId($plan_pk);
	if($plan['status'] === false) {
		$result = array(
			'status' => false,
			'error' => 'plano não encontrado'
		); 
	} else {
		$partner = $ClassPartner->GetPersonalData($pk);
		if($partner['status'] === false) {
			$result = array(
				'status' => false,
				'error' => 'usuário não encontrado'
			); 
		} else {
			$token = $ClassBraspag->GenerateToken();
			
			if($token['status'] === false) {
				$result = array(
					'status' => false,
					'error' => $token['error']
				); 
			} else {
				$result = array(
					'status' => true,
					'data' => array(
						'operation_id' => $pk . $plan_pk . date("dmYHis"),
						'customer' => $partner['data'],
						'purchase' => $plan['data'],
					)
				); 
				// Operation ID
				$operation_id = $pk . $plan_pk . date("dmYHis");
				// Token
				$token_pk = $token['data']['access_token'];
				// Customer Data
				$customer = $partner['data'];
				$customer_name = $customer['first_name'] . ' ' . $customer['last_name'];
				$customer_mail = $customer['mail'];
				$customer_identity = str_replace('.','',$customer['cpf']);
				$customer_identity = str_replace('-','',$customer_identity);
				// Card Data
				$card_expiration = $expirationMonth . '/' . $expirationYear;
				// Purchase Data
				$purchase = $plan['data'];
				$purchase_name = $purchase['name'];
				$purchase_value = $purchase['value'] * 100;
				// Create Purchase
				$payment = $ClassBraspag->PurchaseSimple(
					// Operation
					$operation_id,
					// Token
					$token_pk,
					// Customer
					$pk, 
					$customer_name,
					$customer_mail,
					$customer_identity,
					// Card
					$cardNumber, 
					$cardHolder, 
					$card_expiration, 
					$ccv, 
					$flag, 
					// Purchase
					$is_credit, 
					$plan_pk, 
					$purchase_name, 
					$purchase_value, 
					$purchase_name_note,
					// Finger
					$browserfingerprint
				);
				if($payment['status'] === true) {
					$result = $payment;

					// Se der boa, Atualizar o usuário com o plano


					// $fraudan_alysis = $payment['data']['FraudAnalysis'];
					// if($fraudan_alysis['StatusDescription'] === 'Reject') {
					// 	$result = array(
					// 		'status' => false,
					// 		'error' => $fraudan_alysis
					// 	); 
					// }
				} // Payment
			} // Token
		} // User
	} // Plan
	
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
