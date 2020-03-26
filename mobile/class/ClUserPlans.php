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

class ClassPlan extends DbConnect
{
  	private static $instance = NULL;
	private $_response = array(
		'status' => '',
		'error'  => '',
		'data'   => ''
	);

	private $_image_path = "http://database.redwindow.com.br/images/clients/";

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
	* Busca os planos para usuários, que estão ativos no servidor
	*/
	public function Get() {
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array();
		//Call and return Database Function
		return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				//Create Query
				$_query = $_pdo->prepare("SELECT 
					`id`,
					`name`,
					`the_best`,
					`description`,
					`full_description`,
					`duration`,
					`value`,
					`active`,
					`type`,
					`power`,
					`economic`
					FROM Plans
					WHERE `active` = 1 && `type` = 'user'
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = Array();
				//Adjust recive info
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
					// Convert Boolean
					$_row['active'] = $this->ConvertBoolClient($_row['active']);
					array_push($_data, $_row);
				}
				//Tratamento da resposta
				if (is_null($_data)) {
					$this->_response["status"] = false;
					$this->_response["error"] = $_query->errorInfo();
					$this->_response["data"] = null;
				} else {
					$this->_response["status"] = true;
					$this->_response["error"] = null;
					$this->_response["data"] = $_data;
				}
				return $this->_response;
			}
		);
	}

		/**
	* Busca os planos para usuários, que estão ativos no servidor
	*/
	public function GetId($id) {
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($id);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				//Create Query
				$_query = $_pdo->prepare("SELECT 
					`id`,
					`name`,
					`the_best`,
					`description`,
					`full_description`,
					`duration`,
					`value`,
					`active`,
					`type`,
					`power`,
					`economic`
					FROM Plans
					WHERE `id` = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data;
				//Adjust recive info
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
					// Convert Boolean
					$_row['active'] = $this->ConvertBoolClient($_row['active']);
					$_data = $_row;
				}
				//Tratamento da resposta
				if (is_null($_data)) {
					$this->_response["status"] = false;
					$this->_response["error"] = $_query->errorInfo();
					$this->_response["data"] = null;
				} else {
					$this->_response["status"] = true;
					$this->_response["error"] = null;
					$this->_response["data"] = $_data;
				}
				return $this->_response;
			}
		);
	}
}

$ClassPlan = ClassPlan::GetInstance();

// echo json_encode($ClassPlan->Get());
?>