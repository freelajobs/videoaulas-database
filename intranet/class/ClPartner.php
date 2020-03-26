<?php
date_default_timezone_set('America/Sao_Paulo');

$LIST_INCLUDE = array(
    "../../server/DbImages.php",
    "../../server/DbConnect.php"
);
foreach ($LIST_INCLUDE as $value) {
    if (!file_exists($value)) {
        die('Classe não encontrada ' . $value);
    }
    require_once $value;
}

class ClassPartner extends DbConnect {

    private static $instance = NULL;
    private $image_path = "http://database.redwindow.com.br/images/partners/";
    private $image_app_path = "http://database.redwindow.com.br/images/partners_app/";
    private $image_client_path = "http://database.redwindow.com.br/images/client/";
    private $default_image = "https://database.redwindow.com.br/images/system_profile/default_user.png";  

    private $_response = array(
        'status' => '',
        'error' => '',
        'data' => ''
    );

    function __construct() {
    }

    public static function GetInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
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
						`first_name`
					FROM Partners
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

    public function GetPersonalData($_id) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($_id);
        //Call and return Database Function
        return $this->CallDatabase(
            $_database, $_data, function($_pdo, $_parameters) {
                //Create Query
                $_query = $_pdo->prepare("
                    SELECT 
						pt.*, 
						ct.name category
					FROM Partners pt
					LEFT JOIN Categories ct ON ct.id = pt.id_category
					WHERE pt.id = ?
                ");
                //Execute Query
                $_query->execute($_parameters);
                $_data = null;
                //Adjust recive info
                while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {

                    $_partner = array();
					$_partner['id'] = $_row['id'];
					$_partner['first_name'] = $_row['first_name'];
					$_partner['last_name'] = $_row['last_name'];
                    $_partner['mail'] = $_row['mail'];
                    $_partner['password'] = $_row['password'];
					$_partner['phone'] = $_row['fone'];
					$_partner['cel_phone'] = $_row['celfone'];
					$_partner['age'] = $_row['age'];
					$_partner['cpf'] = $_row['cpf'];
					$_partner['gender'] = $this->ConvertBoolClient($_row['gender']);
					$_partner['address'] = $_row['address'];
					
					$_partner['url_cpf'] = strlen($_row['url_cpf']) > 1 ?
						$this->image_path . $_row['url_cpf'] :
						null;
                    
                    $_partner['url_document'] = strlen($_row['url_document']) > 1 ?
						$this->image_path . $_row['url_document'] :
						null;
                    
                    $_partner['url_profile'] = strlen($_row['url_profile']) > 1 ?
						$this->image_path . $_row['url_profile'] :
						$this->default_image;

                    $_data = $_partner;
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

    public function GetProfissionalData($_id) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($_id);
        //Call and return Database Function
        return $this->CallDatabase(
            $_database, $_data, function($_pdo, $_parameters) {
                //Create Query
                $_query = $_pdo->prepare("
                    SELECT 
                        pt.*,  
						ct.name category,
						pd.id_partner `diary_id_partner`,
                        pd.open_seg `diary_open_seg`,
                        pd.open_ter `diary_open_ter`,
                        pd.open_qua `diary_open_qua`,
                        pd.open_qui `diary_open_qui`,
                        pd.open_sex `diary_open_sex`,
                        pd.open_sab `diary_open_sab`,
                        pd.open_dom `diary_open_dom`,
                        pd.seg_schendule `diary_seg_schendule`,
                        pd.ter_schendule `diary_ter_schendule`,
                        pd.qua_schendule `diary_qua_schendule`,
                        pd.qui_schendule `diary_qui_schendule`,
                        pd.sex_schendule `diary_sex_schendule`,
                        pd.sab_schendule `diary_sab_schendule`,
                        pd.dom_schendule `diary_dom_schendule`,
                        pd.today `diary_today`,
                        pd.completed `diary_completed`,
						ps.id_services services_ids,
						ps.prices services_prices,
						ps.actives services_actives
					FROM Partners pt
					LEFT JOIN Categories ct ON ct.id = pt.id_category
					LEFT JOIN PartnerDiary pd ON pd.id_partner = pt.id
					LEFT JOIN PartnerServices ps ON ps.id_partner = pt.id
					WHERE pt.id = ?
                ");
                //Execute Query
                $_query->execute($_parameters);
                $_data = null;
                //Adjust recive info
                while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
                    $_partner = array();
					$_partner['id'] = $_row['id'];
					$_partner['first_name'] = $_row['first_name'];
					$_partner['last_name'] = $_row['last_name'];
					$_partner['nick_name'] = $_row['nick_name'];
					$_partner['mail'] = $_row['mail'];
					$_partner['phone'] = $_row['fone'];
					$_partner['cel_phone'] = $_row['celfone'];
					$_partner['age'] = $_row['age'];
					$_partner['cpf'] = $_row['cpf'];
					$_partner['gender'] = $this->ConvertBoolClient($_row['gender']);
					$_partner['category'] = $_row['category'];
					$_partner['description'] = $_row['description'];
					$_partner['rate'] = $_row['rate'];
					$_partner['address'] = $_row['address'];
					$_partner['latitude'] = $_row['latitude'];
					$_partner['longitude'] = $_row['longitude'];
					$_partner['statusDescription'] = $_row['statusDescription'];
					$_partner['service_terms'] = $this->ConvertBoolClient($_row['service_terms']);
					$_partner['blocked'] = $this->ConvertBoolClient($_row['blocked']);
					$_partner['approved'] = $this->ConvertBoolClient($_row['approved']);
					$_partner['reproved'] = $this->ConvertBoolClient($_row['reproved']);
					
					$_partner['url_cpf'] = strlen($_row['url_cpf']) > 1 ?
						$this->image_path . $_row['url_cpf'] :
						null;
					$_partner['url_profile'] = strlen($_row['url_profile']) > 1 ?
						$this->image_path . $_row['url_profile'] :
						null;

                    $_partner['pictures'] = array();
                    
					if(strlen($_row['url_pic_1']) > 1) {
						array_push($_partner['pictures'], $this->image_app_path . $_row['url_pic_1']);
					}
					if(strlen($_row['url_pic_2']) > 1) {
						array_push($_partner['pictures'], $this->image_app_path . $_row['url_pic_2']);
					}
					if(strlen($_row['url_pic_3']) > 1) {
						array_push($_partner['pictures'], $this->image_app_path . $_row['url_pic_3']);
					}
					if(strlen($_row['url_pic_4']) > 1) {
						array_push($_partner['pictures'], $this->image_app_path . $_row['url_pic_4']);
					}
					if(strlen($_row['url_pic_5']) > 1) {
						array_push($_partner['pictures'], $this->image_app_path . $_row['url_pic_5']);
					}

                    $_locations = $this->GetLocations($_row['id']);
                    
                    $_diary = $this->GenerateSchendule($_row);

					$_tmp = array(
                        'diary' => $_diary,
						'partner' => $_partner,
						'locations' => $_locations['data'],
                        // 'services' => $_services['data'],
						// 'comments' => $_comments
					);
					$_data = $_tmp;
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
	 * Get Comments
	 * recive id
	 */
	public function GetCommentsData($_id, $_start, $_end)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_id, $_start, $_end);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database,
			$_data,
			function($_pdo, $_parameters)
			{
				//Create Query
				$_query = $_pdo->prepare("SELECT 
						cm.id id, 
						cm.description description, 
						cm.ratting rate, 
						cm.created date, 
						cm.type `type`,
						us.nick_name user_nick, 
						us.url_profile user_url_perfil
					FROM Comments cm
					LEFT JOIN Users us ON us.id = cm.id_user
					WHERE cm.id_partner = ? AND (cm.created BETWEEN ? AND ?) AND cm.type = 'ticket_comment'
                ");
				//Execute Query
				$_query->execute($_parameters);
                // echo $_query->debugDumpParams();
				$_data = array();
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC))
				{
					$_row['user_url_perfil'] = strlen($_row['user_url_perfil']) > 1 ? 
						$this->image_client_path . $_row['user_url_perfil'] : 
						null;
					$_row['date'] = $this->ConvertDataClient($_row['date']);
					array_push($_data, $_row);
				}
                
                $this->_response["status"] = true;
                $this->_response["error"] = null;
                $this->_response["data"] = $_data;
                
				return $this->_response;
			}
		);
	}

	private function GenerateSchendule($_row) 
	{
		$_data = array();
		$seg = $this->CreateSchendule(explode("|", $_row['diary_seg_schendule']), "seg");
		$ter = $this->CreateSchendule(explode("|", $_row['diary_ter_schendule']), "ter");
		$qua = $this->CreateSchendule(explode("|", $_row['diary_qua_schendule']), "qua");
		$qui = $this->CreateSchendule(explode("|", $_row['diary_qui_schendule']), "qui");
		$sex = $this->CreateSchendule(explode("|", $_row['diary_sex_schendule']), "sex");
		$sab = $this->CreateSchendule(explode("|", $_row['diary_sab_schendule']), "sab");
		$dom = $this->CreateSchendule(explode("|", $_row['diary_dom_schendule']), "dom");

		array_push($_data, Array(
			'name' => $seg['day'],
			'open' => $_row['diary_open_seg'] == 0 ? false : true,
			'max_time' => '',
			'hours' => $seg['hours']
		));
		array_push($_data, Array(
			'name' => $ter['day'],
			'open' => $_row['diary_open_ter'] == 0 ? false : true,
			'max_time' => '',
			'hours' => $ter['hours']
		));
		array_push($_data, Array(
			'name' => $qua['day'],
			'open' => $_row['diary_open_qua'] == 0 ? false : true,
			'max_time' => '',
			'hours' => $qua['hours']
		));
		array_push($_data, Array(
			'name' => $qui['day'],
			'open' => $_row['diary_open_qui'] == 0 ? false : true,
			'max_time' => '',
			'hours' => $qui['hours']
		));
		array_push($_data, Array(
			'name' => $sex['day'],
			'open' => $_row['diary_open_sex'] == 0 ? false : true,
			'max_time' => '',
			'hours' => $sex['hours']
		));
		array_push($_data, Array(
			'name' => $sab['day'],
			'open' => $_row['diary_open_sab'] == 0 ? false : true,
			'max_time' => '',
			'hours' => $sab['hours']
		));
		array_push($_data, Array(
			'name' => $dom['day'],
			'open' => $_row['diary_open_dom'] == 0 ? false : true,
			'max_time' => '',
			'hours' => $dom['hours']
		));

		return $_data;
    }

	/**
	 * Get on Service data
	 * recive id
	 */
	private function GetServices($_list)
	{
		//Get Database
		$_database = $this->ConnectDatabase();
		//Create Data
		$_data = array($_list);
		//Call and return Database Function
		return $this->CallDatabase(
			$_database,
			$_data,
			function($_pdo, $_parameters)
			{
				$_cond = '(' . $_parameters[0] . ')';

				//Create Query
				$_query = $_pdo->prepare("
					SELECT 
						id,
						name
					FROM `Services`
					WHERE `id` IN " . $_cond
				);
				//Execute Query
				$_query->execute($_parameters);

				$_data = array();
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
					array_push($_data, $_row);
				}
				return $_data;
			}
		);
    }

	/**
	* Busca os locais de serviço da modelo
	*/
	private function GetLocations($id) {
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
					`address`,
					`description`,
					`latitude`,
					`longitude`,
					`max_time`
					FROM PartnerLocation
					WHERE `id_partner` = ?
				");
				//Execute Query
				$_query->execute($_parameters);
				$_data = Array();
				//Adjust recive info
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
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
	 * Get Comments
	 * recive id
	 */
	private function GetComments($_id)
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
						cm.id id, 
						cm.description description, 
						cm.ratting rate, 
						cm.created date, 
						cm.type `type`,
						us.nick_name user_nick, 
						us.url_profile user_url_perfil
					FROM Comments cm
					LEFT JOIN Users us ON us.id = cm.id_user
					WHERE cm.id_partner = ?
				");
				//Execute Query
				$_query->execute($_parameters);

				$_data = null;
				while ($_row = $_query->fetch(PDO::FETCH_ASSOC))
				{
					$_row['user_url_perfil'] = strlen($_row['user_url_perfil']) > 1 ? 
						$this->image_client_path . $_row['user_url_perfil'] : 
						null;
					$_row['date'] = $this->ConvertDataClient($_row['date']);
					$_data = $_row;
				}
				return $_data;
			}
		);
	}
    

    public function Add($first_name, $last_name, $cpf, $fone, $celfone, $age, $gender, $address, $latitude, $longitute, $mail, $password) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($first_name, $last_name, $mail, $password, $cpf, $fone, $celfone, $age, $gender, $address, $latitude, $longitute);
        //Call and return Database Function
        return $this->CallDatabase(
                        $_database, $_data, function($_pdo, $_parameters) {
                    //Create Query
                    $_query = $_pdo->prepare("INSERT INTO `Partners`
                                                ( `first_name`, `last_name`, `mail`, `password`, `cpf`, `fone`, `celfone`, `age`, `gender`, `address`, `latitude`, `longitude`, service_terms)
						VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, true)
					");
                    //Execute Query
                    $_query->execute($_parameters);

                    $id = $_pdo->lastInsertId();
                    //Tratamento da resposta
                    if (is_null($_query->errorInfo())) {
                        $this->_response["status"] = false;
                        $this->_response["error"] = $_query->errorInfo();
                    } else {
                        $this->_response["status"] = true;
                        $this->_response["data"] = $id;
                    }
                    return $this->_response;
                }
        );
    }

    public function AddAddress($id, $address, $latitude, $longitude, $description = "") {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($id, $address, $latitude, $longitude, $description);
        //Call and return Database Function
        return $this->CallDatabase(
            $_database, $_data, function($_pdo, $_parameters) {
                //Create Query
                $_query = $_pdo->prepare("INSERT INTO `PartnerLocation`
                        ( 
                            `id_partner`, 
                            `address`, 
                            `latitude`, 
                            `longitude`,
                            `description`
                        )
                    VALUES 
                        (
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
                } else {
                    $this->_response["status"] = true;
                    $this->_response["data"] = $id;
                }
                return $this->_response;
            }
        );
    }


	private function PopulateArray($ids, $prices, $actives, $service) {
        $result = null;
        foreach ($ids as $key => $value) {
            if ((string) $service['id'] === (string) $value) {
                $result = array(
                    "id" => $service['id'],
                    "name" => $service['name'],
					"price" => $prices[$key],
					"active" => $actives[$key] === "1" ? true : false
                );
                break;
            }
        }
        if (is_null($result)) {
            $result = array(
                "id" => $service['id'],
				"name" => $service['name'],
				"price" => $service['name'] === "Hora" ? "100,00" : "0,00",
                "active" => $service['name'] === "Hora" ? true : false
            );
        }
        return $result;
	}

    private function SortServices($system_services, $partner_services) {

        $new_menu = false;
        if (is_null($partner_services)) {
			$this->_response["data"] = Array();
        	return $this->_response;
        } else {
            $ids = explode(",", $partner_services['ids']);
            $prices = explode(",", $partner_services['prices']);
			$actives = explode(",", $partner_services['actives']);
			
            $partner_services = array();
        }

        foreach ($system_services as $value) {
            if ($new_menu === true) {
                array_push($partner_services, array(
                    "id" => $value['id'],
                    "name" => $value['name'],
					"price" => $value['name'] === "Hora" ? "100,00" : "0,00",
					"active" => $value['name'] === "Hora" ? true : false
                ));
                continue;
            } else {
                $tmp = $this->PopulateArray($ids, $prices, $actives, $value);
                array_push($partner_services, $tmp);
            }
        }
        usort($partner_services, function($a, $b) {
            return $a['id'] - $b['id'];
        });
        $this->_response["data"] = $partner_services;
        return $this->_response;
    }

	/**
	 * Create Schendule Data
	 */
	private function CreateSchendule($hoursArray, $tag)
	{
        $hours = array();//starHours
		foreach ($hoursArray as $key => $value) {
            if(strlen($value) === 0) {
                for ($i=0; $i < 24; $i++) { 
                    array_push($hours, array( 
                        "id"		 => $i,
                        "idLocation" => -1,
                        "selected"	 => false
                    ));
                }
            } else {
                $data = str_replace("[","",$value);
                $data = str_replace("]","",$data);
                $data = explode(",", $data);
                foreach ($data as $_key => $_value) {
                    $_data = explode(".", $_value);
                    $hour = array(
                        "id" => (int)$_data[0],
                        "idLocation" => $_data[1] == "-" ? -1 : (int)$_data[1],
                        "selected"	 => $this->ConvertBoolClient($_data[2])
                    );
                    array_push($hours, $hour);
                }
            }
		}
		$day = array(
			"day"=>$tag,
			"hours"=>$hours
		);
		return $day;
	}

    public function GetAddress($_id) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($_id);
        //Call and return Database Function
        return $this->CallDatabase(
                        $_database, $_data, function($_pdo, $_parameters) {
                    //Create Query
                    $_query = $_pdo->prepare("SELECT * FROM `PartnerLocation` WHERE id_partner = ?");
                    //Execute Query
                    $_query->execute($_parameters);
                    $_data = array();
                    //Adjust recive info
                    while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
                        $_tmp = $_row;
                        array_push($_data, $_tmp);
                    }
                    //Tratamento da resposta
                    if (!$_query) {
                        $this->_response["status"] = false;
                        $this->_response["error"] = $this->_response["error"] = $_query->errorInfo();
                    } else {
                        $this->_response["status"] = true;
                        $this->_response["data"] = $_data;
                    }
                    return $this->_response;
                }
        );
    }

    public function GetAllServices() {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Call and return Database Function
        return $this->CallDatabase(
                $_database, $_data, function($_pdo) {
                    //Create Query
                    $_query = $_pdo->prepare("SELECT id, name, description FROM `Services` WHERE `blocked` = 0");
                    //Execute Query
                    $_query->execute();
                    $_data = array();
                    //Adjust recive info
                    while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
                        $_tmp = $_row;
                        array_push($_data, $_tmp);
                    }
                    //Tratamento da resposta
                    if (!$_query) {
                        $this->_response["status"] = false;
                        $this->_response["error"] = $this->_response["error"] = $_query->errorInfo();
                    } else {
                        $this->_response["status"] = true;
                        $this->_response["data"] = $_data;
                    }
                    return $this->_response;
                }
        );
    }

    public function GetRedWindowPercents() {
        $this->_response["status"] = true;
        $this->_response["data"] = array(
            array(
                "limit" => "50",
                "type" => "percent",
                "value" => "30"
            ),
            array(
                "limit" => "100",
                "type" => "percent",
                "value" => "20"
            ),
            array(
                "limit" => "150",
                "type" => "percent",
                "value" => "10"
            ),
            array(
                "limit" => "200",
                "type" => "fix",
                "value" => "20,00"
            )
        );

        return $this->_response;
    }

    public function GetPartnerServices($_id) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Call and return Database Function
        return $this->CallDatabase($_database, array($_id), function($_pdo, $_parameters) {
                    //Create Query
                    $_query = $_pdo->prepare("SELECT * FROM PartnerServices WHERE id_partner = ? ");
                    //Execute Query
                    $_query->execute($_parameters);
                    $_data = null;
                    //Adjust recive info
                    while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
                        $_data = $_row;
                    }
                    //Tratamento da resposta
                    if (!$_query) {
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

    public function GetSchendule($_id) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($_id);
        //Call and return Database Function
        return $this->CallDatabase(
                        $_database, $_data, function($_pdo, $_parameters) {
                    //Create Query
                    $_query = $_pdo->prepare("SELECT *
						FROM Partners
						WHERE id = ?
					");
                    //Execute Query
                    $_query->execute($_parameters);
                    $_data = null;
                    //Adjust recive info
                    while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
                        //booleans fields
                        $_row['gender'] = $_row['gender'] == 0 ? false : true;
                        $_row['service_terms'] = $_row['service_terms'] == 0 ? false : true;
                        $_row['blocked'] = $_row['blocked'] == 0 ? false : true;
                        $_row['approved'] = $_row['approved'] == 0 ? false : true;
                        //personal pics
                        $_row['url_cpf'] = strlen($_row['url_cpf']) > 1 ? "http://database.redwindow.com.br/intranet/images/partners/" . $_row['url_cpf'] : "";
                        $_row['url_profile'] = strlen($_row['url_profile']) > 1 ? "http://database.redwindow.com.br/intranet/images/partners/" . $_row['url_profile'] : "";
                        //professional pics
                        $_row['url_pic_1'] = strlen($_row['url_pic_1']) > 1 ? "http://database.redwindow.com.br/intranet/images/partners/" . $_row['url_pic_1'] : "";
                        $_row['url_pic_2'] = strlen($_row['url_pic_2']) > 1 ? "http://database.redwindow.com.br/intranet/images/partners/" . $_row['url_pic_2'] : "";
                        $_row['url_pic_3'] = strlen($_row['url_pic_3']) > 1 ? "http://database.redwindow.com.br/intranet/images/partners/" . $_row['url_pic_3'] : "";
                        $_row['url_pic_4'] = strlen($_row['url_pic_4']) > 1 ? "http://database.redwindow.com.br/intranet/images/partners/" . $_row['url_pic_4'] : "";
                        $_row['url_pic_5'] = strlen($_row['url_pic_5']) > 1 ? "http://database.redwindow.com.br/intranet/images/partners/" . $_row['url_pic_5'] : "";

                        $_row['service_address'] = array();
                        $_row['schendule'] = array();

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

    public function UpdatePersonalProfile(
        $id, $first_name, $last_name, $password, $fone, $celfone, $age, $cpf, $address) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($first_name, $last_name, $password, $fone, $celfone, $age, $cpf, $address, $id);
        //Call and return Database Function
        return $this->CallDatabase(
            $_database, 
            $_data, 
            function($_pdo, $_parameters) 
            {
                $set = "
                    first_name = ?, 
                    last_name = ?, 
                    password = ?, 
                    fone = ?, 
                    celfone = ?, 
                    age = ?,
                    cpf = ?,
                    address = ?
                ";

                //Create Query
                $_query = $_pdo->prepare("UPDATE `Partners`
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

    public function UpdateProfissionalProfile($id, $nick, $description) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($nick, $description, $id);
        //Call and return Database Function
        return $this->CallDatabase(
            $_database, 
            $_data, 
            function($_pdo, $_parameters) 
            {
                $set = "
                    nick_name = ?, 
                    description = ?
                ";

                //Create Query
                $_query = $_pdo->prepare("UPDATE `Partners`
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

    public function UpdateAddress($id, $address, $latitude, $longitude, $description = "") {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($address, $latitude, $longitude, $description, $id);
        //Call and return Database Function
        return $this->CallDatabase(
            $_database, $_data, function($_pdo, $_parameters) 
            {
                $set = "address = ?, latitude = ?, longitude = ?, description = ?";
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

    public function UpdateProfileDocument($id, $name) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($name, $id);
        //Call and return Database Function
        return $this->CallDatabase(
            $_database, 
            $_data, 
            function($_pdo, $_parameters) 
        {
            $set = "url_document = ?";

            //Create Query
            $_query = $_pdo->prepare("UPDATE `Partners`
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
        });
    }

    public function UpdateProfileImage($id, $name) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($name, $id);
        //Call and return Database Function
        return $this->CallDatabase(
            $_database, 
            $_data, 
            function($_pdo, $_parameters) 
        {
            $set = "url_profile = ?";

            //Create Query
            $_query = $_pdo->prepare("UPDATE `Partners`
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
        });
    }

    public function UpdateAppImage($id, $index, $name) {
        
        
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($id, $index, $name);
        //Call and return Database Function
        return $this->CallDatabase(
            $_database, 
            $_data, 
            function($_pdo, $_parameters) 
        {
            $set = "url_pic_" . $_parameters[1] . " = ?";

            $_parameters2 = array(
                $_parameters[2],
                $_parameters[0],
            );
            //Create Query
            $_query = $_pdo->prepare("UPDATE `Partners`
                SET " . $set . "
                WHERE id = ?"
            );

            //Execute Query
            $_query->execute($_parameters2);
            //Tratamento da resposta
            if (is_null($_query->errorInfo())) {
                $this->_response["status"] = false;
                $this->_response["error"] = $_query->errorInfo();
            } else {
                $this->_response["status"] = true;
            }
            return $this->_response;
        });
    }

    public function UpdateProfileCPF($id, $name) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($name, $id);
        //Call and return Database Function
        return $this->CallDatabase(
            $_database, 
            $_data, 
            function($_pdo, $_parameters) 
        {
            $set = "url_cpf = ?";

            //Create Query
            $_query = $_pdo->prepare("UPDATE `Partners`
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
        });
    }

    public function UpdateProfessionalProfile($id, $nick, $description) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($nick, $description, $id);
        //Call and return Database Function
        return $this->CallDatabase($_database, $_data, function($_pdo, $_parameters) {
                    $set = "nick_name = ?, description = ?";
                    //Create Query
                    $_query = $_pdo->prepare("UPDATE `Partners`
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

    public function UpdateService($id, $id_service, $value, $active, $services) {
        $result = $this->SortPartnerToDB(array($id_service, $value, $active), $services);
        $_database = $this->ConnectDatabase();

        if (is_null($services)) {
            $_data = array($id, $result["id_services"], $result["prices"], $result["actives"]);
            return $this->CreateServices($_database, $_data);
        } else {
            $_data = array($result["id_services"], $result["prices"], $result["actives"], $id);
            return $this->UpdateServices($_database, $_data);
        }
    }

    private function CreateServices($_database, $_data) {
        return $this->CallDatabase($_database, $_data, function($_pdo, $_parameters) {
                    //Create Query
                    $_query = $_pdo->prepare("INSERT INTO `PartnerServices` (`id_partner`, `id_services`, `prices`, `actives`)
						VALUES (?, ?, ?, ?)
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

    private function UpdateServices($_database, $_data) {
        //Call and return Database Function
        return $this->CallDatabase($_database, $_data, function($_pdo, $_parameters) {
                    $set = "id_services = ?, prices = ?, actives = ?";
                    //Create Query
                    $_query = $_pdo->prepare("UPDATE `PartnerServices`
						SET " . $set . "
						WHERE id_partner = ?
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

    public function RemoveAddress($id) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($id);
        //Call and return Database Function
        return $this->CallDatabase($_database, $_data, function($_pdo, $_parameters) {
                    //Create Query
                    $_query = $_pdo->prepare("DELETE FROM `PartnerLocation` WHERE id = ?");
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

    public function SortPartnerToWeb($system_services, $partner_services) {

        $new_menu = false;
        if (is_null($partner_services)) {
            $new_menu = true;
            $partner_services = array();
        } else {
            $ids = explode(",", $partner_services['id_services']);
            $prices = explode(",", $partner_services['prices']);
            $actives = explode(",", $partner_services['actives']);
            $partner_services = array();
        }

        foreach ($system_services as $value) {
            if ($new_menu === true) {
                array_push($partner_services, array(
                    "id" => $value['id'],
                    "title" => $value['name'],
                    "description" => $value['description'],
                    "active" => $value['name'] === "Hora" ? true : false,
                    "cost" => $value['name'] === "Hora" ? "100,00" : "0,00",
                ));
                continue;
            } else {
                $tmp = $this->PopulateArray($ids, $prices, $actives, $value);
                array_push($partner_services, $tmp);
            }
        }
        usort($partner_services, function($a, $b) {
            return $a['id'] - $b['id'];
        });
        $this->_response["data"] = $partner_services;
        return $this->_response;
    }

    private function SortPartnerToDB($service, $partner_services) {
        $response = array(
            "id_services" => "",
            "prices" => "",
            "actives" => ""
        );
        if (is_null($partner_services)) {
            $response['id_services'] = $service[0];
            $response['prices'] = $service[1];
            $response['actives'] = $service[2];
            return $response;
        } else {
            $ids = explode(",", $partner_services['id_services']);
            $prices = explode(",", $partner_services['prices']);
            $actives = explode(",", $partner_services['actives']);
            $exist = false;
            foreach ($ids as $key => $value) {
                if ((string) $service[0] === (string) $value) {
                    $prices[$key] = $service[1];
                    $actives[$key] = $service[2];
                    $exist = true;
                    break;
                }
            }
            if ($exist === false) {
                array_push($ids, $service[0]);
                array_push($prices, $service[1]);
                array_push($actives, $service[2]);
            }
            //Create String Id's
            foreach ($ids as $value) {
                if (is_null($response['id_services']) || $response['id_services'] === "") {
                    $response['id_services'] = (string) $value;
                } else {
                    $response['id_services'] = $response['id_services'] . "," . (string) $value;
                }
            }
            //Create String Prices
            foreach ($prices as $value) {
                if (is_null($response['id_services']) || $response['prices'] === "") {
                    $response['prices'] = (string) $value;
                } else {
                    $response['prices'] = $response['prices'] . "," . (string) $value;
                }
            }
            //Create String Active
            foreach ($actives as $value) {
                if (is_null($response['actives']) || $response['actives'] === "") {
                    $response['actives'] = (string) $value;
                } else {
                    $response['actives'] = $response['actives'] . "," . (string) $value;
                }
            }
            return $response;
        }
    }
}

$ClassPartner = ClassPartner::GetInstance();

// echo json_encode($ClassPartner->GetPersonalData(2));
// echo json_encode($ClassPartner->GetPassword("rodrigoazurex@gmail.com"));
// echo json_encode($ClassPartner->GetCommentsData(1, '2018-08-20', '2019-02-20'));
