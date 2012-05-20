<?php

/**
 * Description of MySqlTest
 *
 * @author de1mos
 */

require_once 'PHPUnit/Autoload.php';
require_once 'DB/IDBConnection.php';
require_once 'DB/DBMySql.php';

class SqlDBTest extends PHPUnit_Framework_TestCase {
    private $fixture = NULL;
    
    protected function setUp() {
        $this->fixture = DBMySql::Create();
        $this->fixture->CreateDatabase();
        $this->fixture->StartTransaction();
    }
    
    protected function tearDown() {
        $this->fixture->RollbackTransaction();
    }


    public function testConnection() {
        $this->assertNotNull($this->fixture);
    }
    
    public function testCreateDB() {
        $this->fixture->CreateDatabase("testCreateDB");
    }
    
    public function testCRUD() {
        $table = "CRUDTable";
        $field = "CRUDField";
        $value = "'CRUDValue'";
        $newvalue = "'CRUDUpdatedValue'";
        // Подготавливаем таблицу
        $this->fixture->ExecuteNonQuery("drop table if exists " . $table);
        $this->fixture->ExecuteNonQuery("create table " . $table . " (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY)");
        $this->fixture->ExecuteNonQuery("alter table " . $table . " add " . $field . " varchar(255)");
        
        // Добавляем запись
        $this->fixture->ExecuteNonQuery("insert into " . $table . " (".$field.") values (" . $value . ")");
        // Должна быть найдена одна запись
        $first_query = $this->fixture->ExecuteQuery("select id, " . $field . " from " . $table . " where " . $field . " = " . $value);
        $this->AssertEquals(1, $this->fixture->RowsCount($first_query), "first query fail");
        $row = $this->fixture->Fetch($first_query);
        // Обновляем поле
        $this->fixture->ExecuteNonQuery("update " . $table . " set " . $field . " = " . $newvalue . " where id = " . $row["id"]);
        // С новым полем должна быть найдена одна запись
        $second_query = $this->fixture->ExecuteQuery("select id, " . $field . " from " . $table . " where " . $field . " = " . $newvalue);
        $this->AssertEquals(1, $this->fixture->RowsCount($second_query), "second query fail");
        // Удаляем запись
        $this->fixture->ExecuteNonQuery("delete from " . $table . " where id = " . $row["id"]);
        // Записей найдено не должно быть
        $third_query = $this->fixture->ExecuteQuery("select id, " . $field . " from " . $table . " where id = " . $row["id"]);
        $this->AssertEquals(0, $this->fixture->RowsCount($third_query), "third query fail");
    }
}

?>
