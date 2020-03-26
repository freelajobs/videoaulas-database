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

class ClassPlans extends DbConnect
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
	 * Add System User in database
	 * recive mail, password, first name
	 */
	public function Add(
		$name, $description, $full_description, $duration, $value, $the_best, 
		$type, $power, $economic
	) {
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array(
			$name, $description, $full_description, $duration, $value, $the_best, 
			$type, $power, $economic
		);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database,
			$_data,
			function($_pdo, $_parameters)
			{
				//Create Query
				$_query = $_pdo->prepare("INSERT INTO `Plans`
					(
						`name`, 
						`description`, 
						`full_description`, 
						`duration`, 
						`value`, 
						`the_best`,
						`type`, 
						`power`, 
						`economic`,
						`active` 
					)
					VALUES 
					(
						?,
						?,
						?,
						?,
						?,
						?,
						?,
						?,
						?,
						1
					)
				");
				//Execute Query
				$_query->execute($_parameters);

				$id = $_pdo->lastInsertId();
				//Tratamento da resposta
				if(is_null($_query->errorInfo()))
				{
					$this->_response["status"] = false;
					$this->_response["error"]  = $_query->errorInfo();
				}
				else
				{
					$this->_response["status"] = true;
					$this->_response["data"] = $id;
				}
				return $this->_response;
			}
		);
	}

	/**
	 * Get on System User in database
	 * recive id
	 */
	public function Update(
		$id, $name, $description, $full_description, $duration, $value, $the_best, 
		$type, $power, $economic, $active
	) {
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array(
			$name, $description, $full_description, $duration, $value, $the_best, 
			$type, $power, $economic, $active, $id
		);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database,
			$_data,
			function($_pdo, $_parameters)
			{
				$set = "
					name = ?, 
					description = ?, 
					full_description = ?, 
					duration = ?, 
					value = ?, 
					the_best = ?, 
					type = ?, 
					power = ?, 
					economic = ?, 
					active = ?
				";

				//Create Query
				$_query = $_pdo->prepare("UPDATE Plans
					SET " . $set . "
					WHERE id = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				//Tratamento da resposta
				if(is_null($_query->errorInfo()))
				{
					$this->_response["status"] = false;
					$this->_response["error"]  = $_query->errorInfo();
				}
				else
				{
					$this->_response["status"] = true;
				}
				return $this->_response;
			}
		);
	}

	/**
	* Busca os planos para usuários, que estão ativos no servidor
	*/
	public function GetAll($type) {
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($type);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				//Create Query
				$_query = $_pdo->prepare("SELECT 
					`id`,
					`name`,
					`the_best`,
					`description`,
					`duration`,
					`value`,
					`active`
					FROM Plans
					WHERE`type` = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = Array();
				//Adjust recive info
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
					// Convert Boolean
					$_row['active'] = $this->ConvertBoolClient($_row['active']);
					$_row['the_best'] = $this->ConvertBoolClient($_row['the_best']);
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
	public function Get($id) {
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
					$_row['the_best'] = $this->ConvertBoolClient($_row['the_best']);
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

$ClassPlans = ClassPlans::GetInstance();

// echo json_encode($ClassPlans->GetAll('partner'));
// echo json_encode($ClassPlans->GetPartner(2));
?>