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
	private $url_profile = "http://database.redwindow.com.br/images/perfil/"; 
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
	 * Add System User in database
	 * recive mail, password, first name
	 */
	public function Add($_name, $_description)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_name, $_description);
		//Call and return Database Function
		return $this->CallDatabase(
				$_database,
				$_data,
				function($_pdo, $_parameters)
				{
					//Create Query
					$_query = $_pdo->prepare("INSERT INTO `ServiceCard`
						(`name`, `description`)
						VALUES (?, ?)
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
	public function Update($_id, $_name, $_description, $_blocked)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_name, $_description, $_blocked, $_id);
		//Call and return Database Function
		return $this->CallDatabase(
				$_database,
				$_data,
				function($_pdo, $_parameters)
				{
					//Create Query
					$_query = $_pdo->prepare("UPDATE ServicePayment
						SET name = ?, description = ?, blocked = ?
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
					sc.value,
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
					us.created_date user_created_date,
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
					pt.created_date partner_created_date,
					cg.name partner_category,
					sp.id payment_id,
					sp.name payment_name,
					sp.description payment_description,
					sc.services_name,
					sc.services_value,

					CASE WHEN cmp.id IS NULL THEN NULL ELSE cmp.id END AS comment_partner_id,
					CASE WHEN cmp.id_user IS NULL THEN NULL ELSE cmp.id_user END AS comment_partner_id_user,
					CASE WHEN cmp.id_partner IS NULL THEN NULL ELSE cmp.id_partner END AS comment_partner_id_partner,
					CASE WHEN cmp.id_plan IS NULL THEN NULL ELSE cmp.id_plan END AS comment_partner_id_plan,
					CASE WHEN cmp.id_service IS NULL THEN NULL ELSE cmp.id_service END AS comment_partner_id_service,
					CASE WHEN cmp.type IS NULL THEN NULL ELSE cmp.type END AS comment_partner_type,
					CASE WHEN cmp.description IS NULL THEN NULL ELSE cmp.description END AS comment_partner_description,
					CASE WHEN cmp.ratting IS NULL THEN NULL ELSE cmp.ratting END AS comment_partner_ratting,
					CASE WHEN cmp.created IS NULL THEN NULL ELSE cmp.created END AS comment_partner_created,

					CASE WHEN cmu.id IS NULL THEN NULL ELSE cmu.id END AS comment_user_id,
					CASE WHEN cmu.id_user IS NULL THEN NULL ELSE cmu.id_user END AS comment_user_id_user,
					CASE WHEN cmu.id_partner IS NULL THEN NULL ELSE cmu.id_partner END AS comment_user_id_partner,
					CASE WHEN cmu.id_plan IS NULL THEN NULL ELSE cmu.id_plan END AS comment_user_id_plan,
					CASE WHEN cmu.id_service IS NULL THEN NULL ELSE cmu.id_service END AS comment_user_id_service,
					CASE WHEN cmu.type IS NULL THEN NULL ELSE cmu.type END AS comment_user_type,
					CASE WHEN cmu.description IS NULL THEN NULL ELSE cmu.description END AS comment_user_description,
					CASE WHEN cmu.ratting IS NULL THEN NULL ELSE cmu.ratting END AS comment_user_ratting,
					CASE WHEN cmu.created IS NULL THEN NULL ELSE cmu.created END AS comment_user_created

					FROM ServiceCard sc
					LEFT JOIN Users us ON us.id = sc.id_user
					LEFT JOIN Partners pt ON pt.id = sc.id_partner
					LEFT JOIN ServiceStatus st ON st.id = sc.id_status
					LEFT JOIN ServicePayment sp ON sp.id = sc.id_payment
					LEFT JOIN Categories cg ON cg.id = pt.id_category
					LEFT JOIN Comments cmp ON cmp.id = sc.id_comment_partner
					LEFT JOIN Comments cmu ON cmu.id = sc.id_comment_user
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
					
					$_row['user_created_date'] = $this->ConvertDataHourClient($_row['user_created_date']);
					$_row['user_gender'] = $this->ConvertBoolGender($_row['user_gender']);
					$_row['user_blocked'] = $this->ConvertBoolClient($_row['user_blocked']);

					$_row['partner_created_date'] = $this->ConvertDataHourClient($_row['partner_created_date']);
					$_row['partner_blocked'] = $this->ConvertBoolClient($_row['partner_blocked']);
					$_row['partner_gender'] = $this->ConvertBoolGender($_row['partner_gender']);

					$_row['user_picture'] = strlen($_row['user_url_perfil']) > 1 ? 
						$this->url_profile . $_row['user_url_perfil'] : 
						null;
					
					$comment_partner = array();
					$comment_user = array();
					
					if($_row['comment_partner_id'] !== null) {
						$comment_partner = array(
							'id' => $_row['comment_partner_id'],
							'id_user' => $_row['comment_partner_id_user'],
							'id_partner' => $_row['comment_partner_id_partner'],
							'id_plan' => $_row['comment_partner_id_plan'],
							'id_service' => $_row['comment_partner_id_service'],
							'type' => $_row['comment_partner_type'],
							'description' => $_row['comment_partner_description'],
							'ratting' => $_row['comment_partner_ratting'],
							'created' => $_row['comment_partner_created']
						);
					}

					if($_row['comment_user_id'] !== null) {
						$comment_user = array(
							'id' => $_row['comment_user_id'],
							'id_user' => $_row['comment_user_id_user'],
							'id_partner' => $_row['comment_user_id_partner'],
							'id_plan' => $_row['comment_user_id_plan'],
							'id_service' => $_row['comment_user_id_service'],
							'type' => $_row['comment_user_type'],
							'description' => $_row['comment_user_description'],
							'ratting' => $_row['comment_user_ratting'],
							'created' => $_row['comment_user_created']
						);
					}

					// $services = $this->CreateServices(
					// 	$_row['services_name'],
					// 	$_row['services_value']
					// );

					// $total_price = 0;
					// foreach ($services as $key => $value) {
					// 	$total_price = $total_price + floatval($services[$key]['price']);
					// }

					$_tmp = array(
						'ticket' => array(
							'id'=> $_row['id'],
							'date_purchase'=> $_row['date_purchase'],
							'date_service'=> $_row['date_service'],
							'duration'=> $_row['duration'],
							'payment_status'=> $_row['payment_status'],
							'price'=> number_format((float)$_row['value'], 2, '.', ''),
							'status'=> $_row['status'],
						),
						'comments' => array(
							'partner' => $comment_partner,
							'user' => $comment_user
						),
						// 'services' => $services,
						'client' => array(
							'blocked' => $_row['user_blocked'],
							'name' => $_row['user_name'],
							'nick' => $_row['user_nick'],
							'mail' => $_row['user_mail'],
							'phone' => $_row['user_fone'],
							'cpf' => $_row['user_cpf'],
							'age' => $_row['user_age'],
							'gender' => $_row['user_gender'],
							'address' => $_row['user_address'],
							'description' => $_row['user_description'],
							'user_picture' => $_row['user_picture'],
							'created_date' => $_row['user_created_date']
						),
						'partner' => array(
							'blocked' => $_row['partner_blocked'],
							'name' => $_row['partner_name'],
							'nick' => $_row['partner_nick'],
							'mail' => $_row['partner_mail'],
							'phone' => $_row['partner_fone'],
							'cpf' => $_row['partner_cpf'],
							'age' => $_row['partner_age'],
							'gender' => $_row['partner_gender'],
							'address' => $_row['partner_address'],
							'category' => $_row['partner_category'],
							'value' => isset($_row['partner_value']) ? $_row['partner_value'] : '',
							'description' => $_row['partner_description'],
							'created_date' => $_row['partner_created_date']
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
	 * Get tickets complete
	 * recive id
	 */
	public function GetToday($id)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($id);
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
					sc.work_address, 
					sc.status,
					sc.value
					FROM ServiceCard sc
					LEFT JOIN Users us ON us.id = sc.id_user
					LEFT JOIN Partners pt ON pt.id = sc.id_partner
					LEFT JOIN ServiceStatus st ON st.id = sc.id_status
					WHERE sc.id_partner = ? && DAY(sc.date_service) = DAY(CURRENT_DATE) && MONTH(sc.date_service) = MONTH(CURRENT_DATE) && YEAR(sc.date_service) = YEAR(CURRENT_DATE)
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

	/**
	 * Get tickets complete
	 * recive id
	 */
	public function GetFinished($id)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($id);
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
					sc.work_address, 
					sc.status,
					sc.value
					FROM ServiceCard sc
					LEFT JOIN Users us ON us.id = sc.id_user
					LEFT JOIN Partners pt ON pt.id = sc.id_partner
					LEFT JOIN ServiceStatus st ON st.id = sc.id_status
					WHERE (sc.status = 'Completo' OR sc.status = 'Cancelado') AND sc.id_partner = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data_completed = array();
				$_data_canceled = array();
				//Adjust recive info
				while($_row = $_query->fetch(PDO::FETCH_ASSOC))
				{
					$_row['date_purchase'] = $this->ConvertDataHourClient($_row['date_purchase']);
					$_row['date_service'] = $this->ConvertDataHourClient($_row['date_service']);
					$_tmp = $_row;
					
					if($_tmp['status'] === 'Completo'){
						array_push($_data_completed, $_tmp);
					} else {
						array_push($_data_canceled, $_tmp);
					}
				}

				$_data = array (
					'completed' => $_data_completed,
					'canceled' => $_data_canceled
				);

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
	 * Get tickets schendule
	 * recive id
	 */
	public function GetSchendule($id)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($id);
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
					sc.status,
					sc.work_address,
					sc.value
					FROM ServiceCard sc
					LEFT JOIN Users us ON us.id = sc.id_user
					LEFT JOIN Partners pt ON pt.id = sc.id_partner
					LEFT JOIN ServiceStatus st ON st.id = sc.id_status
					WHERE sc.status = 'Aguardando' AND sc.id_partner = ?
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

$ClassServiceTickets = ClServicesTickets::GetInstance();

// echo json_encode($ClassServiceTickets->GetSchendule());
// echo json_encode($ClassServiceTickets->GetFinished());
// echo json_encode($ClassServiceTickets->Get(4));

?>
