<?php

$LIST_INCLUDE = array(
	"../../server/DbImages.php",
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

class ClassTickets extends DbConnect
{
	private static $instance = NULL;
	private $_image_path = 'http://database.redwindow.com.br/images/partners_app/';
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

	public function GetAll($_id) 
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_id);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				//Create Query
				$_query = $_pdo->prepare("SELECT 
					`id`,
					`work_address` address,
					`date` dataPurchase,
					`date_service` dataRealized,
					`status`
					FROM `ServiceCard`
					WHERE `id_user` = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = Array();
				//Adjust recive info
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
					// Convert Data
					$_row['dataPurchase'] = $this->ConvertDataHourClient($_row['dataPurchase']);
					$_row['dataRealized'] = $this->ConvertDataHourClient($_row['dataRealized']);
					
					array_push($_data, $_row);
				}
				//Tratamento da resposta
				if (is_null($_data)) {
					$this->_response["status"] = false;
					$this->_response["error"] = $_query->errorInfo();
				} else {
					$this->_response["status"] = true;
					$this->_response["data"] = $_data;
				}
				return $this->_response;
			}
        );
    }

	public function Get($_id) 
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_id);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				//Create Query
				$_query = $_pdo->prepare("SELECT 
					sc.id,
					sc.status,
					sc.date 'dataPurchase',
					sc.date_service 'dataRealized',
					sc.duration, 
					sc.services_value,
					sc.work_address 'local',
					sc.id_partner 'modelId',
					pt.gender,
					pt.age,
					pt.celfone 'phone',
					pt.description,
					pt.url_pic_1 'picture',
					pt.nick_name,
					sc.services_name
					FROM `ServiceCard` sc
					LEFT JOIN `Partners` pt ON pt.id = sc.id_partner
					WHERE sc.id = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = null;
				//Adjust recive info
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
					$tmp = Array();
					$tmp['id'] = $_row['id'];
					$tmp['status'] = $_row['status'];
					// Convert Data
					$tmp['dataPurchase'] = $this->ConvertDataHourClient($_row['dataPurchase']);
					$tmp['dataRealized'] = $this->ConvertDataHourClient($_row['dataRealized']);
					
					$tmp['duration'] = $this->ConvertHourClient($_row['duration'], 'H:i:s');
					$tmp['value'] = 0.00;
					$tmp['local'] = $_row['local'];
					$tmp['modelNick'] = $_row['nick_name'];
					$tmp['modelId'] = $_row['modelId'];
					$tmp['modelGender'] = $this->ConvertBoolGender($_row['gender']);
					$tmp['modelAge'] = $_row['age'];
					$tmp['modelFone'] = $_row['phone'];
					$tmp['modelDescription'] = $_row['description'];
					$tmp['modelPicture'] = strlen($_row['picture']) > 1 ? 
						$this->_image_path . $_row['picture'] : "";
					
					$tmp['comment'] = null;
					$tmp['services'] = $this->CreateServices(
						$_row['services_name'],
						$_row['services_value']
					);

					foreach ($tmp['services'] as $service) {
						$tmp['value'] = $tmp['value'] + $service['price'];
					}

					$tmp['value'] = $tmp['value'] . '.00';
					$_data = $tmp;
				}
				//Tratamento da resposta
				if (is_null($_data)) {
					$this->_response["status"] = false;
					$this->_response["error"] = $_query->errorInfo();
				} else {
					$this->_response["status"] = true;
					$this->_response["data"] = $_data;
				}
				return $this->_response;
			}
        );
	}
	
	private function CreateServices($names, $prices)
	{
		$services = array();
		$names  = explode("|", $names);
		$prices = explode("|", $prices);

		for ($i=0; $i < sizeof($names); $i++) { 
			$service = Array(
				"name" => $names[$i],
				"price" => $prices[$i],
			);
			array_push($services, $service);
		}

		return $services;
	} 
}

$ClassTickets = ClassTickets::GetInstance();

echo json_encode($ClassTickets->Get(4));
?>
