<?php
date_default_timezone_set('America/Sao_Paulo');
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

class ClassSchendule extends DbConnect
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
	 * Add System User in database
	 * recive mail, password, first name
	 */
	public function Add($_id, $_days)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array(	$_days[0]["open"], $_days[1]["open"], $_days[2]["open"], $_days[3]["open"], $_days[4]["open"], $_days[5]["open"], $_days[6]["open"],
			implode(",",$_days[0]["schendule"]), implode(",",$_days[1]["schendule"]), implode(",",$_days[2]["schendule"]), implode(",",$_days[3]["schendule"]),
			implode(",",$_days[4]["schendule"]), implode(",",$_days[5]["schendule"]), implode(",",$_days[6]["schendule"]),
			$_id);
		//Call and return Database Function
		return $this->CallDatabase(
				$_database,
				$_data,
				function($_pdo, $_parameters)
				{
					//Create Query
					$_query = $_pdo->prepare("INSERT INTO `PartnerDiary`
						(`open_seg`,`open_ter`,`open_qua`,`open_qui`,`open_sex`,`open_sab`,`open_dom`,
							`seg_schendule`,`ter_schendule`,`qua_schendule`,`qui_schendule`,`sex_schendule`,`sab_schendule`,`dom_schendule`,
							`completed`, `id_partner`)
						VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,1,?)
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
	public function Update($_id, $_days)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array(
				$_days[0]["open"], $_days[1]["open"], $_days[2]["open"], $_days[3]["open"], $_days[4]["open"], $_days[5]["open"], $_days[6]["open"],
				implode("|",$_days[0]["schendule"]), implode("|",$_days[1]["schendule"]), implode("|",$_days[2]["schendule"]), implode("|",$_days[3]["schendule"]),
				implode("|",$_days[4]["schendule"]), implode("|",$_days[5]["schendule"]), implode("|",$_days[6]["schendule"]),
		 		$_id);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database,
			$_data,
			function($_pdo, $_parameters)
			{
				$set = "open_seg = ?, open_ter = ?, open_qua = ?, open_qui = ?, open_sex = ?, open_sab = ?, open_dom = ?,
				seg_schendule = ?, ter_schendule = ?, qua_schendule = ?, qui_schendule = ?, sex_schendule = ?, sab_schendule = ?,
				dom_schendule = ?, completed = 1";

				//Create Query
				$_query = $_pdo->prepare("UPDATE `PartnerDiary`
					SET " . $set . "
					WHERE id_partner = ?
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

	private function CreateSchendule($hoursArray, $tag)
	{
		$hours = array();//starHours
		foreach ($hoursArray as $key => $value) {
			$data = str_replace("[","",$value);
			$data = str_replace("]","",$data);
			$data = explode(",", $data);
			array_push($hours, array( //StartHour
				"id"=>(int)$data[0],//id_hour
				"idLocation"=> $data[1] == "-" ? -1 : (int)$data[1]
			));
		}
		$day = array(
			"day"=>$tag,
			"hours"=>$hours
		);
		return $day;
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
				$_query = $_pdo->prepare("SELECT *
					FROM PartnerDiary
					WHERE id_partner = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = null;
				//Adjust recive info
				while($_row = $_query->fetch(PDO::FETCH_ASSOC))
				{
					$_data = array();

					$_data['id'] = $_row['id'];
					$_data['id_partner'] = $_row['id_partner'];

					$_data['open_seg'] = $_row['open_seg'] == 0 ? false : true;
					$_data['open_ter'] = $_row['open_ter'] == 0 ? false : true;
					$_data['open_qua'] = $_row['open_qua'] == 0 ? false : true;
					$_data['open_qui'] = $_row['open_qui'] == 0 ? false : true;
					$_data['open_sex'] = $_row['open_sex'] == 0 ? false : true;
					$_data['open_sab'] = $_row['open_sab'] == 0 ? false : true;
					$_data['open_dom'] = $_row['open_dom'] == 0 ? false : true;

					$schendule = array();
					$seg = $this->CreateSchendule(explode("|", $_row['seg_schendule']), "seg");
					$ter = $this->CreateSchendule(explode("|", $_row['ter_schendule']), "ter");
					$qua = $this->CreateSchendule(explode("|", $_row['qua_schendule']), "qua");
					$qui = $this->CreateSchendule(explode("|", $_row['qui_schendule']), "qui");
					$sex = $this->CreateSchendule(explode("|", $_row['sex_schendule']), "sex");
					$sab = $this->CreateSchendule(explode("|", $_row['sab_schendule']), "sab");
					$dom = $this->CreateSchendule(explode("|", $_row['dom_schendule']), "dom");

					array_push($schendule, $seg);
					array_push($schendule, $ter);
					array_push($schendule, $qua);
					array_push($schendule, $qui);
					array_push($schendule, $sex);
					array_push($schendule, $sab);
					array_push($schendule, $dom);

					$_data['schendule'] = $schendule;

					$_data['today'] = $_row['today'];
					$_data['completed'] = $_row['completed'] == 0 ? false : true;

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
	 * Get on System User in database
	 * recive id
	 */
	public function GetAll($_id, $_date)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_id, $_date);
		//Call and return Database Function
		return $this->CallDatabase(
				$_database,
				$_data,
				function($_pdo, $_parameters)
				{
					//Create Query
					$_query = $_pdo->prepare("SELECT sc.id id, us.nick_name nick, sc.duration duration, sc.date_service date_service, sc.work_address address, st.name completed
							FROM `ServiceCard` sc
							LEFT JOIN `Users` us ON us.id = sc.id_user
							LEFT JOIN `ServiceStatus` st ON st.id = sc.id_status
							WHERE sc.id_user = ? AND date(sc.date_service) = date(?)
					");
					//Execute Query
					$_query->execute($_parameters);
					$_data = array();
					//Adjust recive info
					while($_row = $_query->fetch(PDO::FETCH_ASSOC))
					{
						$_date = array();

						$_date['id'] = $_row['id'];
						$_date['nick'] = $_row['nick'];
						$_date['time_purchase'] = DateTime::createFromFormat('H:i:s',$_row['duration'])->format('H:i');
						$_date['date_schendule'] = DateTime::createFromFormat('Y-m-d H:i:s',$_row['date_service'])->format('H:i');
						$_date['address'] = $_row['address'];
						$_date['status'] = $_row['completed'];

						array_push($_data, $_date);
					}
					//Tratamento da resposta
					if(is_null($_data))
					{
						$this->_response["status"] = false;
						if ($_query->errorInfo()[0] !== "00000")
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

	private function CreateServices($serviceArray, $priceArray)
	{
		$names = explode("|", $serviceArray);
		$prices = explode("|", $priceArray);
		$service = array();
		foreach ($names as $key => $value) {
			array_push($service, array(
				"name"=>$names[$key],
				"price"=>number_format((float)$prices[$key], 2, '.', '')
			));
		}
		return $service;
	}

	/**
	 * Get on System User in database
	 * recive id
	 */
	public function GetOne($_id)
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
					$_query = $_pdo->prepare("SELECT sc.id id, sc.id_user id_user, sc.id_partner id_partner,
						sc.date sc_date_purchase, sc.date_service sc_date_service, sc.duration sc_duration, st.name sc_status, sc.report sc_report, sc.report_description sc_report_description, sc.completed sc_completed,
						us.nick_name us_nick_name, us.fone us_fone, us.description us_description, us.age us_age, us.gender us_gender, us.url_perfil us_url_perfil,
						sc.services_name sc_services_name, sc.services_value sc_services_value, sc.work_address sc_work_address,
						CASE WHEN cmu.by_user IS NOT NULL THEN cmu.by_user ELSE NULL END AS cmu_by_user,
						CASE WHEN cmu.title IS NOT NULL THEN cmu.title ELSE NULL END AS cmu_title,
						CASE WHEN cmu.description IS NOT NULL THEN cmu.description ELSE NULL END AS cmu_message,
						CASE WHEN cmu.ratting IS NOT NULL THEN cmu.ratting ELSE NULL END AS cmu_ratting,
						CASE WHEN cmu.date IS NOT NULL THEN cmu.date ELSE NULL END AS cmu_date,
						CASE WHEN cmp.by_user IS NOT NULL THEN cmp.by_user ELSE NULL END AS cmp_by_user,
						CASE WHEN cmp.title IS NOT NULL THEN cmp.title ELSE NULL END AS cmp_title,
						CASE WHEN cmp.description IS NOT NULL THEN cmp.description ELSE NULL END AS cmp_message,
						CASE WHEN cmp.ratting IS NOT NULL THEN cmp.ratting ELSE NULL END AS cmp_ratting,
						CASE WHEN cmp.date IS NOT NULL THEN cmp.date ELSE NULL END AS cmp_date
						FROM `ServiceCard` sc
						LEFT JOIN `Users` us ON us.id = sc.id_user
						LEFT JOIN `ServiceStatus` st ON st.id = sc.id_status
						LEFT JOIN `PartnerComments` cmp ON cmp.id_serviceCard = sc.id AND cmp.by_user = 0
						LEFT JOIN `PartnerComments` cmu ON cmu.id_serviceCard = sc.id AND cmu.by_user = 1
						WHERE sc.id = ?
					");
					//Execute Query
					$_query->execute($_parameters);
					$_data = null;
					//Adjust recive info
					while($_row = $_query->fetch(PDO::FETCH_ASSOC))
					{
						$_data = array();

						$_data['id'] = $_row['id'];
						$_data['id_user'] = $_row['id_user'];
						$_data['id_partner'] = $_row['id_partner'];

						//TODO: Terminar de aninhar os comentarios
						$_comment_user = null;
						$_comment_partner = null;
						if ($_row['cmu_date'] !== null) {
							$_comment_user = array(
								'nick'=>$_row['us_nick_name'],
								'by_user'=>$_row['cmu_by_user'] == 0 ? false : true,
								'title'=>$_row['cmu_title'],
								'message'=>$_row['cmu_message'],
								'ratting'=>$_row['cmu_ratting'],
								'date'=>DateTime::createFromFormat('Y-m-d H:i:s',$_row['cmu_date'])->format('d-m-Y H:i')
							);
						}

						if ($_row['cmp_date'] !== null) {
      				$_comment_partner = array(
								'by_user'=>$_row['cmp_by_user'] == 0 ? false : true,
								'title'=>$_row['cmp_title'],
								'message'=>$_row['cmp_message'],
								'ratting'=>$_row['cmp_ratting'],
								'date'=>DateTime::createFromFormat('Y-m-d H:i:s',$_row['cmp_date'])->format('d-m-Y H:i')
							);
						}

						$_data['data'] = array(
							'date_purchase'=>DateTime::createFromFormat('Y-m-d H:i:s',$_row['sc_date_purchase'])->format('d-m-Y'),
							'date_service'=>DateTime::createFromFormat('Y-m-d H:i:s',$_row['sc_date_service'])->format('d-m-Y H:i'),
							'duration'=>DateTime::createFromFormat('H:i:s',$_row['sc_duration'])->format('H:i'),
							'status'=>$_row['sc_status'],
							'report'=>$_row['sc_report'] == 0 ? false : true,
							'report_description'=>$_row['sc_report_description'],
							'completed'=> $_row['sc_completed'] == 0 ? false : true,
							'comments' => array(
								'user'=>$_comment_user,
								'partner'=>$_comment_partner
							)
						);

						$_data['client'] = array(
							'nick_name'=>$_row['us_nick_name'],
							'fone'=>$_row['us_fone'],
							'description'=>$_row['us_description'],
							'age'=>$_row['us_age'],
							'gender'=>$_row['us_gender'] == 0 ? 'male' : 'woman',
							'url'=>strlen($_row['us_url_perfil']) > 1 ? "http://database.redwindow.com.br/intranet/images/client/" . $_row['us_url_perfil'] : ""
						);

						$_services = $this->CreateServices($_row['sc_services_name'], $_row['sc_services_value']);
						$_data['services'] = $_services;
						$_data['address'] = $_row['sc_work_address'];

					}
					//Tratamento da resposta
					if(is_null($_data))
					{
						$this->_response["status"] = false;
						if ($_query->errorInfo()[0] !== "00000")
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
	 * GetExist on System User in database
	 * recive id
	 */
	public function GetExist($_id)
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
					$_query = $_pdo->prepare("SELECT id
						FROM PartnerDiary
						WHERE id_partner = ?
					");
					//Execute Query
					$_query->execute($_parameters);
					$_data = null;
					//Adjust recive info
					while($_row = $_query->fetch(PDO::FETCH_ASSOC))
					{
						$_data = $_row;
					}
					//Tratamento da resposta
					if(is_null($_data))
					{
						$this->_response["status"] = false;
					}
					else
					{
						$this->_response["status"] = true;
					}
					return $this->_response;
				}
		);
	}

	public function UpdateTimeAddress($id, $hour) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($hour, $id);
        //Call and return Database Function
        return $this->CallDatabase(
            $_database, $_data, function($_pdo, $_parameters) 
            {
				$set = "max_time = ?";
				
                //Create Query
                $_query = $_pdo->prepare("UPDATE `PartnerLocation`
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
	
	function getLongestSeq($list, $size) 
	{ 
		$maxLen = 0; 
		$currLen = 0; 
		$lastNumber = $list[0];
	
		for ($i = 0; $i < $size; $i++) {
			$currLen ++; 
			$number = $list[$i];
			
			if ($lastNumber + 1 >= $number && $currLen > $maxLen) { 
				$maxLen = $currLen; 
			} 
			if ($lastNumber + 1 < $number) {
				$currLen = 0; 
			}
			$lastNumber = $list[$i];
		} 

		return $maxLen;
	} 
}

$ClassSchendule = ClassSchendule::GetInstance();

?>
