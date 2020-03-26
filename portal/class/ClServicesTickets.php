<?php
date_default_timezone_set('America/Sao_Paulo');
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

class ClServicesTickets extends DbConnect
{
    private static $instance = NULL;
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
	public function Get($_id)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_id);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database,
			$_data,
			function($_pdo, $_parameters)
			{
				//Create Query
				$_query = $_pdo->prepare("SELECT 
					sc.id,
					us.nick_name user_nick,
					pt.nick_name partner_name,
					us.url_profile user_url_perfil,
					sc.date date_purchase,
					sc.date_service date_service,
					sc.duration,
					st.name payment_status,
					sc.status,
					sc.work_address map_address,
					sc.latitude map_latitude,
					sc.longitude map_longitude,
					CONCAT(us.first_name, ' ', us.last_name) user_name,
					us.mail user_mail,
					us.phone user_fone,
					us.cpf user_cpf,
					us.age user_age,
					us.gender user_gender,
					us.home_address user_address,
					us.description user_description,
					us.blocked user_blocked,
					pt.nick_name partner_nick,
					CONCAT(pt.first_name, ' ', pt.last_name) partner_name,
					pt.mail partner_mail,
					pt.fone partner_fone,
					pt.cpf partner_cpf,
					pt.age partner_age,
					pt.gender partner_gender,
					pt.address partner_address,
					pt.description partner_description,
					pt.blocked partner_blocked,
					cg.name partner_category,
					sp.id payment_id,
					sp.name payment_name,
					sp.description payment_description
					FROM ServiceCard sc
					LEFT JOIN Users us ON us.id = sc.id_user
					LEFT JOIN Partners pt ON pt.id = sc.id_partner
					LEFT JOIN ServiceStatus st ON st.id = sc.id_status
					LEFT JOIN ServicePayment sp ON sp.id = sc.id_payment
					LEFT JOIN Categories cg ON cg.id = pt.id_category
					WHERE sc.id = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = null;
				//Adjust recive info
				while($_row = $_query->fetch(PDO::FETCH_ASSOC))
				{
					$_row['date_purchase'] = $this->ConvertDataHourClient($_row['date_purchase']);
					$_row['date_service'] = $this->ConvertDataHourClient($_row['date_service']);

					$_row['user_gender'] = $this->ConvertBoolClient($_row['user_gender']);
					$_row['user_blocked'] = $this->ConvertBoolClient($_row['user_blocked']);

					$_row['partner_blocked'] = $this->ConvertBoolClient($_row['partner_blocked']);
					$_row['partner_gender'] =$this->ConvertBoolClient($_row['partner_gender']);

					$_row['user_url_perfil'] = strlen($_row['user_url_perfil']) > 1 ? 
						"http://database.redwindow.com.br/images/perfil/".$_row['user_url_perfil'] : 
						null;

					$_comment_array = null;
					$_services = Array();
					if(isset($_row['comment_id']) && $_row['comment_id'] !== null)
					{
						$_row['comment_date'] =  $this->ConvertDataHourClient(
							$_row['comment_date'], 
							'd-M-Y'
						);

						$_comment_array = Array(
							'id' => $_row['comment_id'],
							'title'=> $_row['user_nick'],
							'description'=> $_row['comment_description'],
							'ratting'=> $_row['comment_ratting'],
							'date'=> $_row['comment_date'],
							'url_perfil'=> $_row['user_url_perfil']
						);
					}
					
					if(isset($_row['services_id']) && $_row['services_id'] !== null) {
						$_ids = explode(",", $_row['services_id']);
						for ($i=0; $i < count($_ids); $i++)
						{
							$_service = $this->GetService($_ids[$i]);
							array_push($_services, $_service);
						}
					}

					$_tmp = array(
						'ticket' => array(
							'id'=> $_row['id'],
							'date_purchase'=> $_row['date_purchase'],
							'date_service'=> $_row['date_service'],
							'duration'=> $_row['duration'],
							'payment_status'=> $_row['payment_status'],
							'status'=> $_row['status']
						),
						'comment' => $_comment_array,
						'services' => $_services,
						'client' => array(
							'blocked' => $_row['user_blocked'],
							'name' => $_row['user_name'],
							'nick' => $_row['user_nick'],
							'mail' => $_row['user_mail'],
							'fone' => $_row['user_fone'],
							'cpf' => $_row['user_cpf'],
							'age' => $_row['user_age'],
							'male' => $_row['user_gender'],
							'address' => $_row['user_address'],
							'description' => $_row['user_description']
						),
						'partner' => array(
							'blocked' => $_row['partner_blocked'],
							'name' => $_row['partner_name'],
							'nick' => $_row['partner_nick'],
							'mail' => $_row['partner_mail'],
							'fone' => $_row['partner_fone'],
							'cpf' => $_row['partner_cpf'],
							'age' => $_row['partner_age'],
							'male' => $_row['partner_gender'],
							'address' => $_row['partner_address'],
							'category' => $_row['partner_category'],
							'value' => isset($_row['partner_value']) ? $_row['partner_value'] : '',
							'description' => $_row['partner_description']
						),
						'payment' => array(
							'id' => $_row['payment_id'],
							'name' => $_row['payment_name'],
							'description' => $_row['payment_description']
						),
						'map' => array(
							'address' => $_row['map_address'],
							'latitude' => $_row['map_latitude'],
							'longitude' => $_row['map_longitude']
						)
					);
					$_data = $_tmp;
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

	/**
	 * Get on Service data
	 * recive id
	 */
	public function GetService($_id)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_id);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database,
			$_data,
			function($_pdo, $_parameters)
			{
				//Create Query
				$_query = $_pdo->prepare("SELECT name, description value FROM Services WHERE id = ?");
				//Execute Query
				$_query->execute($_parameters);

				$_data = null;
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
					$_data = $_row;
				}
				return $_data;
			}
		);
	}

	/**
	 * Get on System User in database
	 * recive id
	 */
	public function GetAll()
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array();
		//Call and return Database Function
		return $this->CallDatabase(
				$_database,
				$_data,
				function($_pdo, $_parameters)
				{
					//Create Query
					$_query = $_pdo->prepare("SELECT 
						sc.id, 
						us.nick_name user_name,
						pt.nick_name partner_name, 
						sc.date date_purchase, 
						sc.date_service date_service, 
						sc.duration, 
						st.name payment_status, 
						sc.status
						FROM ServiceCard sc
						LEFT JOIN Users us ON us.id = sc.id_user
						LEFT JOIN Partners pt ON pt.id = sc.id_partner
						LEFT JOIN ServiceStatus st ON st.id = sc.id_status
					");
					//Execute Query
					$_query->execute($_parameters);
					$_data = array();
					//Adjust recive info
					while($_row = $_query->fetch(PDO::FETCH_ASSOC))
					{
						$_row['date_purchase'] = $this->ConvertDataHourClient($_row['date_purchase']);
						$_row['date_service'] = $this->ConvertDataHourClient($_row['date_service']);
						$_tmp = $_row;
						array_push($_data, $_tmp);
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

$ClassServiceTickets = ClServicesTickets::GetInstance();

echo json_encode($ClassServiceTickets->Get(4));
?>
