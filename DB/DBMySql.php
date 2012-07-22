<?php

/**
 * Description of DBMySql
 *
 * @author de1mos <de1m0s242@gmail.com>
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .'IDBConnection.php';

class DBMySql implements IDBConnection {
    
    private static $instance = NULL;
    private $dbname = "HSADB";
    private $login = "root";
    private $password = "1234";
    private $host = "localhost";
    private $connection = NULL;
    
    public static function Create($dbname="", $login="", $password="") {
        if (is_null(DBMySql::$instance)) {
            DBMySql::$instance = new DBMySql();
            if ($dbname!="") DBMySql::$instance->dbname = $dbname;
            if ($login!="") DBMySql::$instance->login = $login;
            if ($password!="") DBMySql::$instance->password = $password;
            DBMySql::$instance->connect();
        }
        return DBMySql::$instance;
    }
    
    private function connect() {
        $this->connection = mysql_connect($this->host, $this->login, $this->password);
    }
    
    public function InitTables() {
        ;
    }
    
    public function ExecuteQuery($query) {
        if (!mysql_selectdb($this->dbname, $this->connection)) {
            $this->CreateDatabase();
            mysql_selectdb($this->dbname, $this->connection);
        }

        $result = mysql_query($query.";", $this->connection);
        if (!$result) {
            $text = "DB error. MySQL error:" . mysql_error($this->connection) . ". Query: " . $query . ".";
            throw new Exception($text);
        }
        return $result;
    }
    
    public function ExecuteNonQuery($execution_string) {
        if (!mysql_selectdb($this->dbname, $this->connection)) {
            $this->CreateDatabase();
            mysql_selectdb($this->dbname, $this->connection);
        }

        if (!mysql_query($execution_string.";", $this->connection)) {
            $text = "DB error. MySQL error:" . mysql_error($this->connection) . ". Query: " . $execution_string . ".";
            throw new Exception($text);
        }
    }
    
    public function StartTransaction() {
        $this->ExecuteNonQuery("SET AUTOCOMMIT=0");
        $this->ExecuteNonQuery("START TRANSACTION");
    }
    
    public function CommitTransaction() {
        $this->ExecuteNonQuery("COMMIT");
        $this->ExecuteNonQuery("SET AUTOCOMMIT=1");
    }
    
    public function RollbackTransaction() {
        $this->ExecuteNonQuery("ROLLBACK");
        $this->ExecuteNonQuery("SET AUTOCOMMIT=1");
    }
    
    public function CreateDatabase($dbname="") {
        if ($dbname!="") $dbname = $this->dbname;
        $execution_string = "Create database " . $this->dbname.";";
        mysql_query($execution_string, $this->connection);
    }
    
    public function Fetch($result) {
        return mysql_fetch_array($result);
    }
    
    public function RowsCount($result) {
        return mysql_num_rows($result);
    }

    public function IsTableExists($tableName) {
        $result = $this->ExecuteQuery("Show tables like '$tableName'");
        return ($this->RowsCount($result) > 0);
    }
}

?>
