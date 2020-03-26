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

class ClassRecordTicket extends DbConnect {

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

    public function GetToday() {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array();
        //Call and return Database Function
        return $this->CallDatabase(
            $_database, $_data, function($_pdo, $_parameters) {
                //Create Query
                $_query = $_pdo->prepare("SELECT 
                    SUM(value) `count`
                    FROM `ServiceCard` 
                    WHERE date = CURRENT_DATE && status = 'Completo'
                ");
                //Execute Query
                $_query->execute($_parameters);
                $_data = number_format((float)0.00, 2, '.', '');
                //Adjust recive info
                while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
                    $_data = $_row['count'] === null ? 
                        number_format((float)0.00, 2, '.', '') :
                        number_format((float)$_row['count'], 2, '.', '');
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

    public function GetWeek() {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array();
        //Call and return Database Function
        return $this->CallDatabase(
            $_database, $_data, function($_pdo, $_parameters) {
                //Create Query
                $_query = $_pdo->prepare("SELECT 
                    SUM(value) `count`
                    FROM `ServiceCard` 
                    WHERE status = 'Completo' && WEEK(date) = WEEK(CURRENT_DATE) && MONTH(date) = MONTH(CURRENT_DATE) && YEAR(date) = YEAR(CURRENT_DATE)
                ");
                //Execute Query
                $_query->execute($_parameters);
                $_data = number_format(0, 2, '.', '');
                //Adjust recive info
                while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
                    $_data = $_row['count'] === null ? 
                        number_format((float)0.00, 2, '.', '') :
                        number_format((float)$_row['count'], 2, '.', '');
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

    public function GetMonth() {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array();
        //Call and return Database Function
        return $this->CallDatabase(
            $_database, $_data, function($_pdo, $_parameters) {
                //Create Query
                $_query = $_pdo->prepare("SELECT 
                    SUM(value) `count`
                    FROM `ServiceCard` 
                    WHERE status = 'Completo' && MONTH(date) = MONTH(CURRENT_DATE) && YEAR(date) = YEAR(CURRENT_DATE)
                ");
                //Execute Query
                $_query->execute($_parameters);
                $_data = number_format(0, 2, '.', '');
                //Adjust recive info
                while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
                    $_data = $_row['count'] === null ? 
                        number_format((float)0.00, 2, '.', '') :
                        number_format((float)$_row['count'], 2, '.', '');
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

    public function GetYear() {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array();
        //Call and return Database Function
        return $this->CallDatabase(
            $_database, $_data, function($_pdo, $_parameters) {
                //Create Query
                $_query = $_pdo->prepare("SELECT 
                    SUM(value) `count`
                    FROM `ServiceCard` 
                    WHERE status = 'Completo' && YEAR(date) = YEAR(CURRENT_DATE)
                ");
                //Execute Query
                $_query->execute($_parameters);
                $_data = number_format(0, 2, '.', '');
                //Adjust recive info
                while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
                    $_data = $_row['count'] === null ? 
                        number_format((float)0.00, 2, '.', '') :
                        number_format((float)$_row['count'], 2, '.', '');
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

    public function GetTotal() {
        //Get Database
        $_database = $this->ConnectDatabase();
        //Create Data
        $_data = array();
        //Call and return Database Function
        return $this->CallDatabase(
            $_database, $_data, function($_pdo, $_parameters) {
                //Create Query
                $_query = $_pdo->prepare("SELECT 
                    SUM(value) `count`
                    FROM `ServiceCard`
                    WHERE status = 'Completo' 
                ");
                //Execute Query
                $_query->execute($_parameters);
                $_data = number_format((float)0.00, 2, '.', '');
                //Adjust recive info
                while ($_row = $_query->fetch(PDO::FETCH_ASSOC)) {
                    $_data = $_row['count'] === null ? 
                        number_format((float)0.00, 2, '.', '') :
                        number_format((float)$_row['count'], 2, '.', '');
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

}

$ClassRecordTicket = ClassRecordTicket::GetInstance();

// echo json_encode($ClassBankAccount->Get());
