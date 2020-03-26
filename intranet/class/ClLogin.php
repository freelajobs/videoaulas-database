<?php

$LIST_INCLUDE = array(
	"../../server/DbConnect.php"
);
foreach ($LIST_INCLUDE as $value)
{
	if (!file_exists($value))
	{
		die('Classe não encontrada ' . $value);
	}
	require_once $value;
}

class ClassLogin extends DbConnect
{
	private static $instance = NULL;
	private $path_image = "https://database.redwindow.com.br/images/partners/";  
	private $default_image = "https://database.redwindow.com.br/images/system_profile/default_user.png";  
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
	 * Return SystemUser data
	 * recive mail and password strings
	 */
	public function Login($_mail, $_password)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_mail, $_password);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database,
			$_data,
			function($_pdo, $_parameters)
			{
				//Create Query
				$_query = $_pdo->prepare("
					SELECT 
						pt.id,
						pt.first_name,
						pt.last_name,
						pt.mail,
						pt.password,
						pt.address,
						pt.latitude,
						pt.longitude,
						pt.fone,
						pt.celfone,
						pt.cpf,
						pt.age,
						pt.gender,
						pt.service_terms,
						pt.blocked,
						pt.statusDescription,
						pt.approved,
						pt.nick_name,
						pt.description,
						pt.url_cpf,
						pt.url_profile,
						pt.url_pic_1,
						pt.url_pic_2,
						pt.url_pic_3,
						pt.url_pic_4,
						pt.url_pic_5,
						pt.rate,
						pt.reproved,
						pt.id_category category_id,
						ct.name category_name, 
						ps.id signature_id,
						ps.name signature_name,
						ps.full_description signature_description,
						ps.date_purchase signature_date_purchase,
						ps.date_expired signature_date_expired,
						ps.duration signature_duration,
						ps.value signature_value,
						ps.upgrade signature_upgrade,
						ps.canceled signature_canceled
					FROM Partners pt
					LEFT JOIN PartnerSignature ps ON ps.id_partner = pt.id
					LEFT JOIN Categories ct ON ct.id = pt.id_category
					WHERE pt.mail = ? AND pt.password = ?
					
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = null;
				//Adjust recive info
				while($_row = $_query->fetch(PDO::FETCH_ASSOC))
				{
					if ($_row['id'] !== null) {
						$_tmp = Array(
							"token" => $_row['id'],
							"mail" => $_row['mail'],
							"password" => $_row['password'],
							"first_name" => $_row['first_name'],
							"last_name" => $_row['last_name'],
							"nick_name" => $_row['nick_name'],
							"description" => $_row['description'],
							"rate" => $_row['rate'],
							"address" => $_row['address'],
							"latitude" => $_row['latitude'],
							"longitude" => $_row['longitude'],
							"phone" => $_row['fone'],
							"celphone" => $_row['celfone'],
							"cpf" => $_row['cpf'],
							"age" => $_row['age'],
							"gender" => $this->ConvertBoolClient($_row['gender']),
							"statusDescription" => $_row['statusDescription'],
							"blocked" => $this->ConvertBoolClient($_row['blocked']),
							"approved" => $this->ConvertBoolClient($_row['approved']),
							"reproved" => $this->ConvertBoolClient($_row['reproved']),
							"service_terms" => $this->ConvertBoolClient($_row['service_terms']),
							'url_profile' => (
								strlen($_row['url_profile']) > 1 ? 
									$this->path_image.$_row['url_profile'] : 
									$this->default_image
							),
							'url_cpf' => (
								strlen($_row['url_cpf']) > 1 ? 
									$this->path_image.$_row['url_cpf'] : 
									$this->default_image
							),
							'category' => array(
								"id" => $_row['category_id'],
								"name" => $_row['category_name']
							)
						);

						$_signature = null;
						if($_tmp['signature_name'] !== null) {
							$_signature = array(
								'id' => $_tmp['signature_id'],
								'name' => $_tmp['signature_name'],
								'description' => $_tmp['signature_description'],
								'date_purchase' => $_tmp['signature_date_purchase'],
								'date_expired' => $_tmp['signature_date_expired'],
								'duration' => $_tmp['signature_duration'],
								'value' => $_tmp['signature_value'],
								'upgrade' => $_tmp['signature_upgrade'],
								'canceled' => $_tmp['signature_canceled']
							);
						}
						$_tmp['signature'] = $_signature;
						$_data = $_tmp;
					}
				}

				//Tratamento da resposta
				if(is_null($_data))
				{
					$this->_response["status"] = false;
					$this->_response["error"]  = $_query->errorInfo();
				} 
				else
				{
					$this->_response["status"] = true;
					$this->_response["data"]   = $_data;
				}
				return $this->_response;
			}
		);
	}
}

$ClassLogin = ClassLogin::GetInstance();
// echo json_encode($ClassLogin->Login('keila_azure@gmail.com', '240489'));
?>
