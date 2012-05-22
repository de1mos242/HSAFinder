<?php

/**
 * Description of HSAProductGateway
 *
 * @author de1mos <de1m0s242@gmail.com>
 */
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR ."Entities". DIRECTORY_SEPARATOR ."Mark.php";
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR ."Entities". DIRECTORY_SEPARATOR ."Model.php";
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR ."DataLayer". DIRECTORY_SEPARATOR ."MarkGateway.php";

class ModelGateway {

    private $db = NULL;
    private $markGateway = NULL;

    const TABLE_NAME = "HSA_MODEL";

    public static function Create($iConnection) {
        $gateway = new ModelGateway();
        $gateway->db = $iConnection;
        $gateway->markGateway = MarkGateway::Create($iConnection);
        return $gateway;
    }

    public function CreateTable() {
	$this->markGateway->CreateTable();
        $this->db->ExecuteNonQuery("drop table if exists " . self::TABLE_NAME);
        $this->db->ExecuteNonQuery("create table " .
                self::TABLE_NAME .
                "(id INT NOT NULL auto_increment PRIMARY KEY," .
                "NAME VARCHAR(100) NOT NULL," .
                "MARK_ID INT NOT NULL" .
                ") ENGINE=innoDB");
        $this->db->ExecuteNonQuery("alter table ".self::TABLE_NAME.
                " add unique (NAME,MARK_ID)");
		$this->db->ExecuteNonQuery("alter table ".self::TABLE_NAME." add index (NAME)");
		$this->db->ExecuteNonQuery("alter table ".self::TABLE_NAME." add index (MARK_ID)");
		$this->db->ExecuteNonQuery("alter table ".self::TABLE_NAME.
			" ADD FOREIGN KEY (MARK_ID) REFERENCES " . MarkGateway::TABLE_NAME . "(id)");
    }

    public function SaveModel($model) {
        $modelId = $this->findModel($model);
        if ($modelId == NULL) {
            $this->insertNewModel($model);
        } else {
            $this->updateModel($model, $modelId);
        }
    }

    private function insertNewModel($model) {
	    $mark = $this->markGateway->FindMarkByName($model->MarkGet()->NameGet());
    	if ($mark==NULL)
    		$this->markGateway->SaveMark($model->MarkGet());
        $query = "INSERT INTO " . self::TABLE_NAME .
                "(NAME, MARK_ID)" .
                " vALUES " .
                "(" .
                "'" . $model->NameGet() . "', " .
                "'" . $this->markGateway->FindMarkByName($model->MarkGet()->NameGet()) . "'" .
                ")";
        $this->db->ExecuteNonQuery($query);
    }

    private function updateModel($model, $id) {
        $modelRow = $this->loadModelRow($id);
        $query = "UPDATE " . self::TABLE_NAME . " set ";
        $whereCondition = " where id = " . $id;
        $updateFields = "";

        if ($markRow["NAME"] != $mark->NameGet())
            $updateFields.= " NAME = '" . $mark->NameGet() . "'";
            
        $mark_id = $this->MarkGateway->FindMarkByName($model->MarkGet()->NameGet());
        if ($productRow["MARK_ID"] != $mark_id) {
            if ($updateFields != "")
                $updateFields.=",";
            $updateFields.= " MARK_ID = '" . $mark_id . "'";
        }

        if ($updateFields == "") return;
        $this->db->ExecuteNonQuery($query . $updateFields . $whereCondition);
    }

    private function loadModelRow($id) {
        $query = "select NAME, MARK_ID" .
                " from " . self::TABLE_NAME . " where id = " . $id;
        $dbResult = $this->db->ExecuteQuery($query);
        return $this->db->Fetch($dbResult);
    }

    public function GetModelByMarkAndModelNames($markName, $modelName) {
        $id = $this->FindModelByMarkAndModelNames($markName, $modelName);
        if ($id != NULL) {
            return $this->LoadModel($id);
        }
        return NULL;
    }
    
    public function FindModelByMarkAndModelNames($markName, $modelName) {
    	$mark = $this->markGateway->FindMarkByName($markName);
    	if ($mark==NULL)
    		return NULL;
        $query = "select id from " . self::TABLE_NAME . " where " .
                " NAME = '" . $modelName . "' ".
                " and MARK_ID = " . $this->markGateway->FindMarkByName($markName);
        $dbResult = $this->db->ExecuteQuery($query);
        $row = $this->db->Fetch($dbResult);
        if (!$row)
            return NULL;
        return $row["id"];
    }
    
    private function findModel($model) {
        return $this->FindModelByMarkAndModelNames($model->MarkGet()->NameGet(), $model->NameGet());
    }

    public function LoadModel($id) {
        $modelRow = $this->loadModelRow($id);
        $mark = $this->markGateway->LoadMark($modelRow['MARK_ID']);
        $model = Model::Create($mark, $modelRow["NAME"]);
        return $model;
    }
    
    public function DeleteModel($model) {
        $id = $this->findModel($model);
        if ($id == NULL) return;
        $this->db->ExecuteNonQuery("delete from " . self::TABLE_NAME . " where id = " . $id);
    }
    
    public function FindPageModels($offset, $pageSize) {
        $query = "select id from ".self::TABLE_NAME. " limit ".$offset.",".$pageSize;
        return $this->db->ExecuteQuery($query);
    }
    
    public function Fetch($dbResult) {
        $modelIdRow = $this->db->Fetch($dbResult);
        if ($modelIdRow == NULL) return NULL;
        return $this->LoadModel($modelIdRow['id']);
    }

    public function FindAllModelsByMarkNameOrderName($markName) {
        $query = "select model.id from ".self::TABLE_NAME. ' as model ' .
                ' inner join '.MarkGateway::TABLE_NAME. ' as mark on model.MARK_ID = mark.id '.
                " where mark.NAME = '".$markName."' order by model.NAME";
        return $this->db->ExecuteQuery($query);
    }
}

?>
