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

class ClassUserSignature extends DbConnect
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
	* Verifica se o usuário já está cadastrado
	*/
	public function GetRegister($_id) {
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_id);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				//Create Query
				$_query = $_pdo->prepare("SELECT id
					FROM UsersSignature
					WHERE id_user = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = null;
				//Adjust recive info
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
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

    /**
     * Add User in database
     * recive mail, password, first name
     */
    public function Add(
		$_idUser, $_name, $_description, $_fullDescription, $_datePurchase, $_duration, $_dateExpired, $_value,
		$_upgrade
	) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array(
			$_idUser, $_name, $_description, $_fullDescription, $_datePurchase, $_duration, $_dateExpired, $_value,
			$_upgrade
		);
        //Call and return Database Function
		return $this->CallDatabase 
		(
			$_database, $_data, function($_pdo, $_parameters) {
				//Create Query
				$_query = $_pdo->prepare("
					INSERT INTO `UsersSignature`
					( 
						`id_user`,
						`name`,
						`description`,
						`full_description`,
						`date_purchase`,
						`duration`,
						`date_expired`,
						`value`,
						`upgrade`
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
						?
					)
				");
				//Execute Query
				$_query->execute($_parameters);

				$id = $_pdo->lastInsertId();
				//Tratamento da resposta
				if (is_null($_query->errorInfo())) {
					$this->_response["status"] = false;
					$this->_response["error"] = $_query->errorInfo();
					$this->_response["data"] = null;
				} else {
					$this->_response["status"] = true;
					$this->_response["error"] = null;
					$this->_response["data"] = $id;
				}
				return $this->_response;
			}
        );
	}
		
	public function Get($_id) {
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
					`name`,
					`description`,
					`full_description`,
					`date_purchase`,
					`date_expired`,
					`duration`,
					`value`,
					`upgrade`,
					`canceled`
					FROM `UsersSignature`
					WHERE `id_user` = ? && `canceled` = 0 
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = null;
				//Adjust recive info
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
					// Convert Data
					$_row['date_purchase'] = $this->ConvertDataHourClient($_row['date_purchase']);
					$_row['date_expired'] = $this->ConvertDataHourClient($_row['date_expired']);
					// Convert Boolean
					$_row['upgrade'] = $this->ConvertBoolClient($_row['upgrade']);
					$_row['canceled'] = $this->ConvertBoolClient($_row['canceled']);

					$_data = $_row;
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
	
	/**
     * Update Partner Address in database
     * recive id_address, address, $latitude, $longitude
     */
	public function Update($id, $_name, $_description, $_fullDescription, $_datePurchase, 
		$_dateExpire, $_value, $_upgrade
	) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array(
			$_name, $_description, $_fullDescription, $_datePurchase, 
			$_dateExpire, $_value, $_upgrade, $id
		);
        //Call and return Database Function
        return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				$set = "
					`name` = ?,
					`description` = ?,
					`full_description` = ?,
					`date_purchase` = ?,
					`date_expire` = ?,
					`duration` = ?,
					`value` = ?,
					`upgrade` = ?
				";
				//Create Query
				$_query = $_pdo->prepare("
					UPDATE `UsersSignature`
					SET " . $set . "
					WHERE id = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				//Tratamento da resposta
				if (is_null($_query->errorInfo())) {
					$this->_response["status"] = false;
					$this->_response["error"] = $_query->errorInfo();
				} else {
					$this->_response["status"] = true;
				}
				return $this->_response;
			}
        );
	}

	public function CancelSignature($id, $status = 1) 
	{
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($status, $id);
        //Call and return Database Function
        return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				$set = "
					`canceled` = ?
				";
				//Create Query
				$_query = $_pdo->prepare("
					UPDATE `UsersSignature`
					SET " . $set . "
					WHERE id = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				//Tratamento da resposta
				if (is_null($_query->errorInfo())) {
					$this->_response["status"] = false;
					$this->_response["error"] = $_query->errorInfo();
				} else {
					$this->_response["status"] = true;
				}
				return $this->_response;
			}
        );
	}
}

$ClassUserSignature = ClassUserSignature::GetInstance();

// echo json_encode($ClassUserSignature->Get(1));
?>