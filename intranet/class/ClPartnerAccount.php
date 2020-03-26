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

class ClassAccount extends DbConnect {

    private static $instance = NULL;
    private $_response = array(
        'status' => '',
        'error' => '',
        'data' => ''
    );

    function __construct() {

    }

    /**
     * Pega a instancia unica do objeto de classe
     */
    public static function GetInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Add System User in database
     * recive mail, password, first name
     */
    public function Add($token, $bank, $agency, $account, $account_type) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($token, $bank, $agency, $account, $account_type);
        //Call and return Database Function
        return $this->CallDatabase(
                $_database, $_data, function($_pdo, $_parameters) {
                //Create Query
                $_query = $_pdo->prepare("
                    INSERT INTO `PartnerBank`
                    ( 
                        `id_partner`, 
                        `bank`, 
                        `agency`, 
                        `account`, 
                        `type`
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

    public function Get($id) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($id);
        //Call and return Database Function
        return $this->CallDatabase(
            $_database, $_data, function($_pdo, $_parameters) {
                //Create Query
                $_query = $_pdo->prepare("SELECT *
                    FROM PartnerBank
                    WHERE id_partner = ?
                ");
                //Execute Query
                $_query->execute($_parameters);
                $_data = array();
                //Adjust recive info
                while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
                    $_row['active'] = $this->ConvertBoolclient($_row['active']);
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

    public function GetData($_id) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($_id);
        //Call and return Database Function
        return $this->CallDatabase(
            $_database, $_data, function($_pdo, $_parameters) {
                //Create Query
                $_query = $_pdo->prepare("SELECT *
                    FROM PartnerBank
                    WHERE id = ?
                ");
                //Execute Query
                $_query->execute($_parameters);
                $_data = null;
                //Adjust recive info
                while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
                    $_row['active'] = $this->ConvertBoolclient($_row['active']);
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
     * Update on System User in database
     * recive id
     */
    public function Update($token, $bank, $agency, $account, $account_type, $active) {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array($bank, $agency, $account, $account_type, $active, $token);
        //Call and return Database Function
        return $this->CallDatabase($_database, $_data, function($_pdo, $_parameters) {
                $set = "
                    bank = ?, 
                    agency = ?, 
                    account = ?, 
                    type = ?, 
                    active = ?
                ";
                
                //Create Query
                $_query = $_pdo->prepare("
                    UPDATE `PartnerBank`
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

$ClassAccount = ClassAccount::GetInstance();

// echo json_encode($ClassAccount->Get());
