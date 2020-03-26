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

class ClassUser extends DbConnect
{
  	private static $instance = NULL;
	private $_response = array(
		'status' => '',
		'error'  => '',
		'data'   => ''
	);

	private $_image_path = "http://database.redwindow.com.br/images/";
	private $_documents = "documents/";
	private $_profile = "client/";

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
	public function GetRegister($_mail) {
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_mail);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				//Create Query
				$_query = $_pdo->prepare("SELECT mail
					FROM Users
					WHERE mail = ?
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
	* Verifica se o usuário já está cadastrado
	*/
	public function GetPassword($_mail) {
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_mail);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				//Create Query
				$_query = $_pdo->prepare("
					SELECT 
						`mail`, 
						`password`,
						`nick_name`
					FROM Users
					WHERE mail = ?
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
	* Verifica se o usuário já está cadastrado
	*/
	public function Login($_mail, $_password) {
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_mail, $_password);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				//Create Query
				$_query = $_pdo->prepare("SELECT id
					FROM Users
					WHERE `mail` = ? AND `password` = ?
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
    public function AddUser(
		$_mail, $_nick, $_name, $_last_name, $_phone, $_age, $_address, $_password, $_gender, 
		$_registerDate, $_cpf
	) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array(
			$_mail, $_nick, $_name, $_last_name, $_phone, $_age, $_address, $_password, $_gender, 
			$_registerDate, $_cpf
		);
        //Call and return Database Function
		return $this->CallDatabase 
		(
			$_database, $_data, function($_pdo, $_parameters) {
				//Create Query
				$_query = $_pdo->prepare("
					INSERT INTO `Users`
					( 
						`mail`,
						`nick_name`,
						`first_name`,
						`last_name`,
						`phone`,
						`age`,
						`home_address`,
						`password`,
						`gender`,
						`created_date`,
						`cpf`,
						`service_terms`,
						`privacy_politic`,
						`approved`
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
						?,
						?,
						true,
						true,
						true
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
		
	public function GetData($_id) {
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
					`mail`,
					`nick_name` nick,
					`first_name`,
					`last_name`,
					`phone`,
					`age`,
					`home_address` address,
					`password`,
					`gender`,
					`cpf`,
					`url_cpf` urlDocument,
					`url_profile` urlPicture
					FROM `Users`
					WHERE `id` = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = null;
				//Adjust recive info
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
					//booleans fields
					$_row['gender'] = $_row['gender'] == 0 ? "male" : "female";
					//personal pics
					$_row['urlDocument'] = strlen($_row['urlDocument']) > 1 ? 
						$this->_image_path . $this->_documents . $_row['urlDocument'] : "";
					$_row['urlPicture'] = strlen($_row['urlPicture']) > 1 ? 
						$this->_image_path. $this->_profile  . $_row['urlPicture'] : 
						$this->_image_path. "portal/img.jpg";

					$_row['login'] = $_row['mail'];
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
    public function UpdateDocument($id, $file) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($file, $id);
        //Call and return Database Function
        return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				$set = "url_cpf = ?";
				//Create Query
				$_query = $_pdo->prepare("
					UPDATE `Users`
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
	
	/**
     * Update Partner Address in database
     * recive id_address, address, $latitude, $longitude
     */
    public function UpdateProfilePicture($id, $file) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($file, $id);
        //Call and return Database Function
        return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				$set = "url_profile = ?";
				//Create Query
				$_query = $_pdo->prepare("
					UPDATE `Users`
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
	
	/**
     * Update Partner Address in database
     * recive id_address, address, $latitude, $longitude
     */
    public function UpdateProfile(
		$id, $_nick, $_phone, $_age, $_address
	) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($_nick, $_phone, $_age, $_address, $id);
        //Call and return Database Function
        return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				$set = "
					nick_name = ?,
					phone = ?, 
					age = ?,
					home_address = ?
				";
				//Create Query
				$_query = $_pdo->prepare("
					UPDATE `Users`
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

	public function UpdateDevice($_id, $_imei) {
		//Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($_imei, $_id);
        //Call and return Database Function
        return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				$set = "imei = ?";
				//Create Query
				$_query = $_pdo->prepare("
					UPDATE `Users`
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

	public function UpdatePassword($_id, $_password) {
		//Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($_password, $_id);
        //Call and return Database Function
        return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				$set = "password = ?";
				//Create Query
				$_query = $_pdo->prepare("
					UPDATE `Users`
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

$ClassUser = ClassUser::GetInstance();

?>