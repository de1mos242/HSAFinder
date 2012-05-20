<?php

/**
 * Description of HSAProductGateway
 *
 * @author de1mos <de1m0s242@gmail.com>
 */
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR ."Entities/Mark.php";
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR ."DataLayer". DIRECTORY_SEPARATOR ."ModelGateway.php";

class MarkGateway {

    private $db = NULL;

    const TABLE_NAME = "HSA_MARK";
    
    public static function Create($iConnection) {
        $gateway = new MarkGateway();
        $gateway->db = $iConnection;
        return $gateway;
    }

    public function CreateTable() {
        $this->db->ExecuteNonQuery("drop table if exists " . HSAItemGateway::OEM_TABLE_NAME);
	$this->db->ExecuteNonQuery("drop table if exists " . HSAItemGateway::TABLE_NAME);
    	$this->db->ExecuteNonQuery("drop table if exists " . ModelGateway::TABLE_NAME);
        $this->db->ExecuteNonQuery("drop table if exists " . self::TABLE_NAME);
        $this->db->ExecuteNonQuery("create table " .
                self::TABLE_NAME .
                "(id INT NOT NULL auto_increment PRIMARY KEY," .
                "NAME VARCHAR(100) NOT NULL" .
                ") ENGINE=innoDB");
        $this->db->ExecuteNonQuery("alter table ".self::TABLE_NAME.
                " add unique (NAME)");
    }

    public function SaveMark($mark) {
        $storedmarkId = $this->findMark($mark);
        if ($storedmarkId == NULL) {
            $this->insertNewMark($mark);
        } else {
            $this->updateMark($mark, $storedmarkId);
        }
    }

    private function insertNewMark($mark) {
        $query = "INSERT INTO " . self::TABLE_NAME .
                "(NAME)" .
                " vALUES " .
                "(" .
                "'" . $mark->NameGet() . "'" .
                ")";
        $this->db->ExecuteNonQuery($query);
    }

    private function updateMark($mark, $id) {
        $markRow = $this->loadMarkRow($id);
        $query = "UPDATE " . self::TABLE_NAME . " set ";
        $whereCondition = " where id = " . $id;
        $updateFields = "";

        if ($markRow["NAME"] != $mark->NameGet())
            $updateFields.= " NAME = '" . $mark->NameGet() . "'";

        if ($updateFields == "") return;
        $this->db->ExecuteNonQuery($query . $updateFields . $whereCondition);
    }

    private function loadMarkRow($id) {
        $query = "select NAME" .
                " from " . self::TABLE_NAME . " where id = " . $id;
        $dbResult = $this->db->ExecuteQuery($query);
        return $this->db->Fetch($dbResult);
    }

    public function GetMarkByName($name) {
        $id = $this->FindMarkByName($name);
        if ($id != NULL) {
            return $this->LoadMark($id);
        }
        return NULL;
    }
    
    public function FindMarkByName($name) {
        $query = "select id from " . self::TABLE_NAME . " where " .
                " NAME = '" . $name . "' ";
        $dbResult = $this->db->ExecuteQuery($query);
        $row = $this->db->Fetch($dbResult);
        if (!$row)
            return NULL;
        return $row["id"];
    }
    
    private function findMark($mark) {
        return $this->FindMarkByName($mark->NameGet());
    }

    public function LoadMark($id) {
        $markRow = $this->loadMarkRow($id);
        $mark = Mark::Create($markRow["NAME"]);
        return $mark;
    }
    
    public function DeleteMark($mark) {
        $id = $this->findMark($mark);
        if ($id == NULL) return;
        $this->db->ExecuteNonQuery("delete from " . self::TABLE_NAME . " where id = " . $id);
    }
    
    public function FindPageMarks($offset, $pageSize) {
        $query = "select id from ".self::TABLE_NAME. " limit ".$offset.",".$pageSize;
        return $this->db->ExecuteQuery($query);
    }
    
    public function FindAllMarksOrderName() {
        $query = "select id from ".self::TABLE_NAME. " order by Name asc";
        return $this->db->ExecuteQuery($query);
    }
    
    public function Fetch($dbResult) {
        $markIdRow = $this->db->Fetch($dbResult);
        if ($markIdRow == NULL) return NULL;
        return $this->LoadMark($markIdRow['id']);
    }

}

?>
