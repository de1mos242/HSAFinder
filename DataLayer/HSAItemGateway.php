<?php

/**
 * Description of HSAProductGateway
 *
 * @author de1mos <de1m0s242@gmail.com>
 */
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR ."DB". DIRECTORY_SEPARATOR ."DBMySql.php";
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR ."Entities". DIRECTORY_SEPARATOR ."Mark.php";
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR ."Entities". DIRECTORY_SEPARATOR ."Model.php";
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR ."Entities". DIRECTORY_SEPARATOR ."HSAItem.php";
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR ."DataLayer". DIRECTORY_SEPARATOR ."ModelGateway.php";
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR ."DataLayer". DIRECTORY_SEPARATOR ."HSAProductGateway.php";

class HSAItemGateway {

    private $db = NULL;
    private $modelGateway = NULL;
    private $productGateway = NULL;

    const TABLE_NAME = "HSA_ITEM";
    const OEM_TABLE_NAME = "HSA_OEM_NUMBER";

    public static function Create($iConnection) {
        $gateway = new HSAItemGateway();
        $gateway->db = $iConnection;
        $gateway->modelGateway = ModelGateway::Create($iConnection);
        return $gateway;
    }

    private function productGatewayGet() {
        if ($this->productGateway == NULL)
            $this->productGateway = HSAProductGateway::Create($this->db);
        return $this->productGateway;
    }
    
    public function CreateTable() {
        $this->modelGateway->CreateTable();
        $this->db->ExecuteNonQuery("drop table if exists " . self::OEM_TABLE_NAME);
        $this->db->ExecuteNonQuery("drop table if exists " . self::TABLE_NAME);
        $this->db->ExecuteNonQuery("create table " .
            self::TABLE_NAME .
            "(id INT NOT NULL auto_increment PRIMARY KEY," .
            "YEAR VARCHAR(50) NOT NULL," .
            "BODY VARCHAR(255) NOT NULL," .
            "BRAND_NUMBER VARCHAR(50) NOT NULL," .
            //"OEM_NUMBER VARCHAR(50) NOT NULL," .
            "HAND_DIRECTION VARCHAR(50) NOT NULL," .
            "LINE_DIRECTION VARCHAR(50) NOT NULL," .
            "TYPE VARCHAR(50) NOT NULL," .
            "HSA_TYPE VARCHAR(20) NOT NULL,".
            "MODEL_ID INT NOT NULL" .
            ") ENGINE=innoDB");
        $this->db->ExecuteNonQuery("alter table ".self::TABLE_NAME." add index (YEAR)");
        $this->db->ExecuteNonQuery("alter table ".self::TABLE_NAME." add index (BODY)");
        $this->db->ExecuteNonQuery("alter table ".self::TABLE_NAME." add index (BRAND_NUMBER)");
        //$this->db->ExecuteNonQuery("alter table ".self::TABLE_NAME." add index (OEM_NUMBER)");
        $this->db->ExecuteNonQuery("alter table ".self::TABLE_NAME." add index (HAND_DIRECTION)");
        $this->db->ExecuteNonQuery("alter table ".self::TABLE_NAME." add index (LINE_DIRECTION)");
        $this->db->ExecuteNonQuery("alter table ".self::TABLE_NAME." add index (TYPE)");
        $this->db->ExecuteNonQuery("alter table ".self::TABLE_NAME." add index (MODEL_ID)");
        $this->db->ExecuteNonQuery("alter table ".self::TABLE_NAME.
            " ADD FOREIGN KEY (MODEL_ID) REFERENCES " . ModelGateway::TABLE_NAME . "(id)");
        
        $this->db->ExecuteNonQuery("create table " .
            self::OEM_TABLE_NAME .
            "(id INT NOT NULL auto_increment PRIMARY KEY," .
            "NUMBER VARCHAR(50) NOT NULL," .
            "ITEM_ID INT NOT NULL" .
            ") ENGINE=innoDB");
        $this->db->ExecuteNonQuery("alter table ".self::OEM_TABLE_NAME." add index (NUMBER)");
        $this->db->ExecuteNonQuery("alter table ".self::OEM_TABLE_NAME." add index (ITEM_ID)");
        $this->db->ExecuteNonQuery("alter table ".self::OEM_TABLE_NAME.
                " add unique (NUMBER,ITEM_ID)");
        $this->db->ExecuteNonQuery("alter table ".self::OEM_TABLE_NAME.
            " ADD FOREIGN KEY (ITEM_ID) REFERENCES " . self::TABLE_NAME . "(id)");
    }
    
    public function SaveItem($item) {
    	if ($item->IdGet() == NULL) {
            $this->insertNewItem($item);
        } else {
            $this->updateItem($item);
        }
        $itemId = $this->FindItem($item);
        if ($itemId == NULL)
            throw new Exception ('Item not found: '.$item->YearGet().'|'.$item->BodyGet().'|'.$item->BrandNumberGet().'|'.$item->ModelGet()->MarkGet()->NameGet().'|'.$item->ModelGet()->NameGet());
        if ($item->IdGet() == NULL) 
            $item->IdSet($itemId);
        $this->updateOEMNumbers($item);
        return $item->IdGet();
    }
    
    private function updateOEMNumbers($item) {
        $this->db->ExecuteNonQuery("delete from ".self::OEM_TABLE_NAME . " where ITEM_ID = " .$item->IdGet());
        $query = 'Insert into '.self::OEM_TABLE_NAME. " (NUMBER,ITEM_ID) values ('";
        foreach ($item->OEMNumbersGet() as $value) {
            $this->db->ExecuteNonQuery($query. $value."',".$item->IdGet().')');
        }
    }

    private function insertNewItem($item) {
	$modelId = $this->modelGateway->FindModelByMarkAndModelNames($item->ModelGet()->MarkGet()->NameGet(), $item->ModelGet()->NameGet());
    	if ($modelId==NULL) {
            $this->modelGateway->SaveModel($item->ModelGet());
            $modelId = $this->modelGateway->FindModelByMarkAndModelNames($item->ModelGet()->MarkGet()->NameGet(), $item->ModelGet()->NameGet());
	}
        $query = "INSERT INTO " . self::TABLE_NAME .
            "(YEAR, BODY, BRAND_NUMBER, HAND_DIRECTION, LINE_DIRECTION, TYPE, MODEL_ID, HSA_TYPE)" .
            " vALUES " .
            "(" .
            "'" . $item->YearGet() . "', " .
            "'" . $item->BodyGet() . "', " .
            "'" . $item->BrandNumberGet() . "', " .
            "'" . $item->HandDirectionGet() . "', " .
            "'" . $item->LineDirectionGet() . "', " .
            "'" . $item->TypeGet() . "', " .
            "'" . $modelId . "'," .
            "'" . $item->HSATypeGet() . "' " .
            ")";
        $this->db->ExecuteNonQuery($query);
    }

    private function updateItem($item) {
        $itemRow = $this->getItemRow($item->IdGet());
        $query = "UPDATE " . self::TABLE_NAME . " set ";
        $whereCondition = " where id = " . $item->IdGet();
        $updateFields = "";
		
        if ($itemRow["YEAR"] != $item->YearGet())
            $updateFields.= " YEAR = '" . $item->YearGet() . "'";
            
        if ($itemRow["BODY"] != $item->BodyGet()) {
            if ($updateFields != "")
                $updateFields.=",";
            $updateFields.= " BODY = '" . $item->BodyGet() . "'";
        }
        if ($itemRow["BRAND_NUMBER"] != $item->BrandNumberGet()) {
            if ($updateFields != "")
                $updateFields.=",";
            $updateFields.= " BRAND_NUMBER = '" . $item->BrandNumberGet() . "'";
        }
        if ($itemRow["HAND_DIRECTION"] != $item->HandDirectionGet()) {
            if ($updateFields != "")
                $updateFields.=",";
            $updateFields.= " HAND_DIRECTION = '" . $item->HandDirectionGet() . "'";
        }
        if ($itemRow["LINE_DIRECTION"] != $item->LineDirectionGet()) {
            if ($updateFields != "")
                $updateFields.=",";
            $updateFields.= " LINE_DIRECTION = '" . $item->LineDirectionGet() . "'";
        }
        if ($itemRow["TYPE"] != $item->TypeGet()) {
            if ($updateFields != "")
                $updateFields.=",";
            $updateFields.= " TYPE = '" . $item->TypeGet() . "'";
        }
        if ($itemRow['HSA_TYPE'] != $item->HSATypeGet()) {
            if ($updateFields != "")
                $updateFields.=",";
            $updateFields.= " HSA_TYPE = '" . $item->HSATypeGet() . "'";
        }
        
        $modelId = $this->modelGateway->FindModelByMarkAndModelNames($item->ModelGet()->MarkGet()->NameGet(), $item->ModelGet()->NameGet());
        if ($itemRow["MODEL_ID"] != $modelId) {
            if ($updateFields != "")
                $updateFields.=",";
            $updateFields.= " MODEL_ID = '" . $modelId . "'";
        }

        if ($updateFields == "") return;
        $this->db->ExecuteNonQuery($query . $updateFields . $whereCondition);
    }
    
    public function FindItem($item) {
    	$modelId = $this->modelGateway->FindModelByMarkAndModelNames($item->ModelGet()->MarkGet()->NameGet(), $item->ModelGet()->NameGet());
    	if ($modelId==NULL)
            return NULL;
    	$conditions = "";
    	$conditions.= " YEAR = '" . $item->YearGet() . "'";
        $conditions.= " and BODY = '" . $item->BodyGet() . "'";
        $conditions.= " and BRAND_NUMBER = '" . $item->BrandNumberGet() . "'";
        $conditions.= " and HAND_DIRECTION = '" . $item->HandDirectionGet() . "'";
        $conditions.= " and LINE_DIRECTION = '" . $item->LineDirectionGet() . "'";
        $conditions.= " and TYPE = '" . $item->TypeGet() . "'";
        $conditions.= " and MODEL_ID = '" . $modelId . "'";
        $conditions.= " and HSA_TYPE = '" . $item->HSATypeGet() . "'";
        
        $query = "select id from " . self::TABLE_NAME . " where " . $conditions;
        $dbResult = $this->db->ExecuteQuery($query);
        $row = $this->db->Fetch($dbResult);
        if (!$row)
            return NULL;
        return $row["id"];
    }
    
    public function GetItemById($id) {
    	$row = $this->getItemRow($id);
    	if ($row == NULL)
    		return NULL;
    	return $this->loadItemByRow($row);
    }
    
    private function getItemRow($id) {
    	$query = "select id, YEAR, BODY, BRAND_NUMBER, ".
    		"HAND_DIRECTION, LINE_DIRECTION, TYPE, MODEL_ID, HSA_TYPE ".
    		"from ". self::TABLE_NAME . " where id = ".$id;
    	$dbResult = $this->db->ExecuteQuery($query);
        return $this->db->Fetch($dbResult);
    }
    
    private function loadItemByRow($row) {
    	$model = $this->modelGateway->LoadModel($row['MODEL_ID']);
    	$item = HSAItem::Create($model, $row['YEAR'], $row['BODY'], 
                    $row['BRAND_NUMBER'], $this->loadOEMNumbersByItemId($row['id']),
                    $row['HAND_DIRECTION'], $row['LINE_DIRECTION'],
                    $row['TYPE'], $row['HSA_TYPE'], $row['id']);
        $product = $this->productGatewayGet()->GetProductByHSAIdAndType($row['BRAND_NUMBER'], $row['HSA_TYPE']);
        $item->ProductSet($product);
	return $item;
    }
    
    private function loadOEMNumbersByItemId($id) {
        $result = array();
        $query = "select NUMBER from ".self::OEM_TABLE_NAME. " where ITEM_ID = ".$id;
        $dbResult = $this->db->ExecuteQuery($query);
        $row = $this->db->Fetch($dbResult);
        while ($row != NULL) {
            $result[] = $row['NUMBER'];
            $row = $this->db->Fetch($dbResult);
        }
        return $result;
    }
    
    public function DeleteItem($item) {
        if ($item->IdGet() == NULL) return;
        $this->db->ExecuteNonQuery("delete from " . self::OEM_TABLE_NAME . " where ITEM_ID = " . $item->IdGet());
        $this->db->ExecuteNonQuery("delete from " . self::TABLE_NAME . " where id = " . $item->IdGet());
    }
    
    public function FindPageItems($offset, $pageSize) {
        $query = "select id from ".self::TABLE_NAME. " order by id asc limit ".$offset.",".$pageSize;
        return $this->db->ExecuteQuery($query);
    }
    
    public function Fetch($dbResult) {
        $itemIdRow = $this->db->Fetch($dbResult);
        if ($itemIdRow == NULL) return NULL;
        return $this->GetItemById($itemIdRow['id']);
    }

    public function FetchColumn($dbResult) {
        $itemIdRow = $this->db->Fetch($dbResult);
        if ($itemIdRow == NULL) return NULL;
        return $itemIdRow[0];
    }
    
    public function FindItemsByMarkName($name, $page, $pageSize) {
        $markGateway = MarkGateway::Create($this->db);
        $markId = $markGateway->FindMarkByName($name);
        $query = "select item.id from ".self::TABLE_NAME.' as item ' .
                    ' where item.MODEL_ID in (' .
                    ' select model.id from '.ModelGateway::TABLE_NAME . ' as model ';
                       // . ' on item.MODEL_ID = model.id ';
        if ($markId != NULL) 
            $query.= ' where model.MARK_ID = ' . $markId;
        else
            $query.= ' where model.MARK_ID is null';
        $query .= ')';
        $query.= $this->addPageLimits($page, $pageSize);
        return $this->db->ExecuteQuery($query);
    }
    
    public function FindItemsByMarkAndModelNames($markName, $modelName, $page, $pageSize) {
        $modelId = $this->modelGateway->FindModelByMarkAndModelNames($markName, $modelName);

        $query = "select item.id from ".self::TABLE_NAME.' as item ';
        if ($modelId != NULL) 
            $query.= ' where item.MODEL_ID = ' . $modelId;
        else
            $query.= ' where item.MODEL_ID is null';
        $query.= $this->addPageLimits($page, $pageSize);
        return $this->db->ExecuteQuery($query);
    }

    public function FindYearsByMarkAndModelNames($markName, $modelName) {
        $modelId = $this->modelGateway->FindModelByMarkAndModelNames($markName, $modelName);

        $query = "select YEAR from ".self::TABLE_NAME;
        if ($modelId == NULL) 
            return NULL;
            
        $query.= ' where MODEL_ID = ' . $modelId;
        $query.= ' group by YEAR';
        return $this->db->ExecuteQuery($query);
    }

    public function FindBodiesByMarkAndModelNames($markName, $modelName) {
        $modelId = $this->modelGateway->FindModelByMarkAndModelNames($markName, $modelName);

        $query = "select BODY from ".self::TABLE_NAME;
        if ($modelId == NULL) 
            return NULL;
            
        $query.= ' where MODEL_ID = ' . $modelId;
        $query.= ' group by BODY';
        return $this->db->ExecuteQuery($query);
    }

    public function FindByMarkModelBodyYear($markName,$modelName,$body,$year, $page, $pageSize) {
        $modelId = $this->modelGateway->FindModelByMarkAndModelNames($markName, $modelName);

        $query = "select id from ".self::TABLE_NAME;
        if ($modelId == NULL) 
            return NULL;

        $query.= ' where MODEL_ID = ' . $modelId;
        if ($this->isVarSet($body))
            $query.= " and BODY = '".$body."'";
        if ($this->isVarSet($year))
            $query.= " and YEAR = '".$year."'";
        $query.= $this->addPageLimits($page, $pageSize);
        return $this->db->ExecuteQuery($query);
    }

    private function isVarSet($var) {
        if (is_null($var))
            return false;
        if ($var == '')
            return false;
        if ($var == 'empty')
            return false;
        return true;
    }

    private function addPageLimits($page, $pageSize) {
        if (!isset($page)) 
            $page = 0;
        if (!isset($pageSize))
            $pageSize = 20;
        $start = $page*$pageSize+1;
        $end = $start + $pageSize-1;
        return " limit $start,$end";
    }
}

?>