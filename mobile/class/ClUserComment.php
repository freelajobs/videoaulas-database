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

class ClassUserComment extends DbConnect
{
	private static $instance = NULL;
	private $_response = array(
		'status' => '',
		'error'  => '',
		'data'   => ''
	);

	private $_image_client_path = "http://database.redwindow.com.br/images/clients/";

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
     * Add User in database
     * recive mail, password, first name
     */
    public function Add(
		$_idUser, 
		$_idPlan, 
		$_idPartiner, 
		$_idService, 
		$_type, 
		$_description, 
		$_ratting, 
		$_creation
	) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array(
			$_idUser, 
			$_idPlan, 
			$_idPartiner, 
			$_idService, 
			$_type, 
			$_description, 
			$_ratting, 
			$_creation
		);
        //Call and return Database Function
		return $this->CallDatabase 
		(
			$_database, $_data, function($_pdo, $_parameters) {
				//Create Query
				$_query = $_pdo->prepare("
					INSERT INTO `Comments`
					( 
						`id_user`,
						`id_plan`,
						`id_partner`,
						`id_service`,
						`type`,
						`description`,
						`ratting`,
						`created`
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

	/**
	* Pega os comentarios de cliente
	*/
	public function GetClient($_id) {
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
					`id_user`,
					`id_plan`,
					`id_partner`,
					`id_service`,
					`type`,
					`description`,
					`ratting`,
					`created`
					FROM Comments
					WHERE id_user = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = null;
				//Adjust recive info
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
					$_row['created'] = $this->ConvertDataHourClient($_row['created']);
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
	* Pega os comentarios de parceiro
	*/
	public function GetForPartner($_id) {
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_id);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database, $_data, function($_pdo, $_parameters) {
				//Create Query
				$_query = $_pdo->prepare("SELECT 
					c.id id,
					c.id_user id_user,
					c.description message,
					c.ratting rating,
					c.created created,
					u.url_profile image,
					u.nick_name nick
					FROM Comments c
					LEFT JOIN Users u ON u.id = c.id_user
					WHERE `id_partner` = ? && `type` = 'ticket_comment'
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = Array();
				//Adjust recive info
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
					$tmp = Array();
					$tmp['id'] = $_row['id'];
					$tmp['type'] = 'comment';
					$tmp['nick'] = $_row['nick'];
					$tmp['image'] = strlen($_row['image']) > 1 ? 
						$this->_image_client_path . $_row['image'] : "";

					$tmp['rating'] = $_row['rating'];
					$tmp['message'] = $_row['message'];
					$tmp['created'] = $this->ConvertDataHourClient($_row['created']);
					array_push($_data, $tmp);
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
	* Pega os comentarios de plano
	*/
	public function GetPlan($_id) {
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
					`id_user`,
					`id_plan`,
					`id_partner`,
					`id_service`,
					`type`,
					`description`,
					`ratting`,
					`created`
					FROM Comments
					WHERE id_plan = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = null;
				//Adjust recive info
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
					$_row['created'] = $this->ConvertDataHourClient($_row['created']);
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
	* Pega os comentarios de serviço
	*/
	public function GetService($_id) {
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
					`description` message,
					`ratting`,
					`created`
					FROM Comments
					WHERE id_service = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = Array();
				//Adjust recive info
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
					$_row['created'] = $this->ConvertDataHourClient($_row['created']);
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
}

$ClassUserComment = ClassUserComment::GetInstance();

// echo json_encode($ClassUserComment->GetClient(1));
?>