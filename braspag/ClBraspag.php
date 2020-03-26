<?php

$LIST_INCLUDE = array(
	'../../http/library/Requests.php',
	'../../server/DbConnect.php'
);
foreach ($LIST_INCLUDE as $value)
{
	if (!file_exists($value))
	{
		die('Classe não encontrada ' . $value);
	}
	require_once $value;
}

class ClassBraspag extends DbConnect
{
	private static $instance      = NULL;
	
    private $client_id     = "d5c03d15-7d22-4520-b291-1cb340cf2084";
    private $client_secret = "XyRZAPEz55HxrXnLoka0uXx9OMOjqR6An+IrY9jqhT8=";
	
	private $endpoint_token	= 'https://authsandbox.braspag.com.br/';
	private $req_token	    = 'oauth2/token';

	private $endpoint_purchase ='https://apisandbox.cieloecommerce.cielo.com.br/';
	private $req_purchase  	   = '1/sales/';
	private $req_cancel  	   = '/void';

	private $_response = array(
		'status' => '',
		'error'  => '',
		'data'   => ''
	);

	function __construct()
	{

	}

	/**
	 * Pega a instancia unica do objeto de classe
	 */
	public static function GetInstance()
	{
			if(is_null(self::$instance)){
					self::$instance = new self();
			}
			return self::$instance;
	}

	/**
	 * Get on System User in database
	 * recive id
	 */
	public function GenerateToken()
	{
		// URL
		$url= $this->endpoint_token . $this->req_token;
		// Header
		$base64 = base64_encode($this->client_id . ":" . $this->client_secret);
		$header = array(
			'Authorization'=>'Basic ' . $base64,
			'Content-Type'=>'application/x-www-form-urlencoded',
		);
		// Body
		$body = array(
			'grant_type' => 'client_credentials'
		);
		// Register loader
		Requests::register_autoloader();
		// Request
		$request = Requests::post(
			$url, 
			$header, 
			$body
		);
		// Create Data from response
		$data = json_decode($request->body, true);
        //Tratamento da resposta
        if (isset($data['error']))
        {
            $this->_response["status"] = false;
			$this->_response["error"]  = array(
				'type'=>$data['error'],
				'description'=>$data['error_description']
			);
        }
        else
        {
            $this->_response["status"] = true;
            $this->_response["data"]   = $data;
        }
        return $this->_response;
	}

	/**
	 * Get on System User in database
	 * recive id
	 */
	public function PurchaseSimple(
		$operation_id,
		$token, 
		$customer_id, $customer_name, $customer_mail, $customer_identity,
		$card_number, $card_holder, $card_expiration, $card_security, $card_flag, 
		$purchase_is_credit, $purchase_id, $purchase_name, $purchase_value, $purchase_name_note,
		$browserfingerprint
	) {
		// URL
		$url= $this->endpoint_purchase . $this->req_purchase;
		// Header
		$header = array(
			'Authorization: Bearer ' . $token,
			'Content-Type: application/json',
			'cache-control: no-cache'
		);
		// All Body Data
		// Customer Data
		$customer = array(
			"name"         => $customer_name . 'Accept',
			"email"        => $customer_mail,
			"identity"     => $customer_identity,
			"identitytype" => "CPF"
		);
		// Card Data
		$card = array(
			"CardNumber"     => $card_number,
			"Holder"         => $card_holder,
			"ExpirationDate" => $card_expiration,
			"SecurityCode"   => $card_security,
			"Brand"          => $card_flag,
			"SaveCard"       => "false" //change in future?
		);
		// Purhcase info
		$payment = array(
			"type"           => $purchase_is_credit === true ? "splittedcreditcard" : "splitteddebitcard",
			"amount"         => $purchase_value, // value in cents
			"capture"        => true, // valid purchase now
			"installments"   => 1, // number parcels
			"softdescriptor" => $purchase_name_note, // name in fature
			$purchase_is_credit === true ? "CreditCard" : "DebitCard" => $card
		);
		// fraudanalysis
		$payment['fraudanalysis'] = $this->GenerateFraudanAlysis(
			$purchase_value, 
			$purchase_name, 
			$purchase_id, 
			$browserfingerprint
		);
		// splitpayment
		$payment['splitpayments'] = $this->GenerateFixSplitPayment(
			$purchase_value
		);
		// Create Body
		$body = array(
			'merchantorderid' => $operation_id,
			'customer'=>$customer,
			'payment'=> $payment
		);
		$body_data = json_encode($body, true);

		// Create Curl
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $body_data,
			CURLOPT_HTTPHEADER => $header
		));
		// Execute Curl
		$response = curl_exec($curl);
		$err = curl_error($curl);
		// Close Curl
		curl_close($curl);

        //Tratamento da resposta
        if ($err)
        {
            $this->_response["status"] = false;
			$this->_response["error"]  = $err;
        }
        else
        {
            $this->_response["status"] = true;
			$this->_response["data"]   = json_decode($response, true);
			$this->_response["request"]  = $body;
        }
        return $this->_response;
	}

		/**
	 * Get on System User in database
	 * recive id
	 */
	public function PurchaseCancel($token, $purchase_id)
	{
		// URL
		$url = $this->endpoint_purchase . $this->req_purchase . $purchase_id . $this->req_cancel;
		// Header
		$header = array(
			'Authorization: Bearer ' . $token,
			'Content-Type: application/json',
			'cache-control: no-cache'
		);
		
		// Create Curl
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "PUT",
			CURLOPT_POSTFIELDS => "",
			CURLOPT_HTTPHEADER => $header
		));
		// Execute Curl
		$response = curl_exec($curl);
		$err = curl_error($curl);
		// Close Curl
		curl_close($curl);

        //Tratamento da resposta
        if ($err)
        {
            $this->_response["status"] = false;
			$this->_response["error"]  = $err;
        }
        else
        {
            $this->_response["status"] = true;
			$this->_response["data"]   = json_decode($response, true);
        }
        return $this->_response;
	}

	private function GenerateFraudanAlysis($purchase_value, $purchase_name, $purchase_id, $browserfingerprint) {
		$fraudanalysis = array(
			"sequence"         => "analysefirst",
			"sequencecriteria" => "onsuccess",
			"provider"         => "cybersource",
			"captureonlowrisk" => false,
			"voidonhighrisk"   => false,
			"totalorderamount" => $purchase_value,
			"browser"          => array(
				"ipaddress"           => getHostByName(getHostName()),
				"browserfingerprint"  => $browserfingerprint 
			),
			"cart"             => array(
				"isgift"          => false,
				"returnsaccepted" => false,
				"items"           => array(
					array(
					"name"        => $purchase_name, // name in my system
					"quantity"    => 1, // quantity
					"sku"         => $purchase_id, //id in my system
					"unitprice"   => $purchase_value / 100
					)
				)
			),
			"MerchantDefinedFields" => array(
				array (
					"Id"	=> $purchase_name,
				   	"Value"	=> $purchase_id
				)
			)
		);
		return $fraudanalysis;
	}

	private function GenerateFixSplitPayment($purchase_value) {
		$splitpayments = array(
			array(
			  "subordinatemerchantid" => $this->client_id,
			  "amount"                => $purchase_value,
			  "fares"                 => array(
				"mdr" => 100,
				"fee" => 0
			  )
			)
		);
		return $splitpayments;
	}
}

$ClassBraspag = ClassBraspag::GetInstance();

?>
