<?php

/**
 * Description of HSAProductGateway
 *
 * @author de1mos <de1m0s242@gmail.com>
 */
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR ."Entities/HSAProduct.php";
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR ."DB/DBMySql.php";

class HSAProductGateway {

    private $db = NULL;

    const TABLE_NAME = "SHA_PRODUCT";

    public static function Create($iConnection) {
        $gateway = new HSAProductGateway();
        $gateway->db = $iConnection;
        return $gateway;
    }

    public function CreateTable() {
        $this->db->ExecuteNonQuery("drop table if exists " . self::TABLE_NAME);
        $this->db->ExecuteNonQuery("create table " .
                self::TABLE_NAME .
                "(id INT NOT NULL auto_increment PRIMARY KEY," .
                "HSA_ID VARCHAR(50) NOT NULL, " .
                "HSA_TYPE VARCHAR(50) NOT NULL, " .
                "HSA_DESCRIPTION VARCHAR(255), " .
                "HSA_PRICE VARCHAR(20) NOT NULL, " .
                "HSA_AMOUNT VARCHAR(50) NOT NULL " .
                ") ENGINE=innoDB");
        $this->db->ExecuteNonQuery("alter table ".self::TABLE_NAME." add index (HSA_ID)");
        $this->db->ExecuteNonQuery("alter table ".self::TABLE_NAME." add index (HSA_TYPE)");
        $this->db->ExecuteNonQuery("alter table ".self::TABLE_NAME.
                " add unique (HSA_ID,HSA_TYPE)");
    }

    public function SaveProduct($product) {
        $storedProductId = $this->findProduct($product);
        if ($storedProductId == NULL) {
            $this->insertNewProduct($product);
        } else {
            $this->updateProduct($product, $storedProductId);
        }
    }

    private function insertNewProduct($product) {
        $query = "INSERT INTO " . self::TABLE_NAME .
                "(HSA_ID, HSA_TYPE, HSA_DESCRIPTION, HSA_PRICE,HSA_AMOUNT)" .
                " vALUES " .
                "(" .
                "'" . $product->HSAIdGet() . "'," .
                "'" . $product->TypeGet() . "'," .
                "'" . $product->DescriptionGet() . "'," .
                "'" . $product->PriceGet() . "'," .
                "'" . $product->AmountGet() . "'" .
                ")";
        $this->db->ExecuteNonQuery($query);
    }

    private function updateProduct($product, $id) {
        $productRow = $this->loadProductRow($id);
        $query = "UPDATE " . self::TABLE_NAME . " set ";
        $whereCondition = " where id = " . $id;
        $updateFields = "";

        if ($productRow["HSA_ID"] != $product->HSAIdGet())
            $updateFields.= " HSA_ID = '" . $product->HSAIdGet() . "'";

        if ($productRow["HSA_TYPE"] != $product->TypeGet()) {
            if ($updateFields != "")
                $updateFields.=",";
            $updateFields.= " HSA_TYPE = '" . $product->TypeGet() . "'";
        }

        if ($productRow["HSA_DESCRIPTION"] != $product->DescriptionGet()) {
            if ($updateFields != "")
                $updateFields.=",";
            $updateFields.= " HSA_DESCRIPTION = '" . $product->DescriptionGet() . "'";
        }

        if ($productRow["HSA_PRICE"] != $product->PriceGet()) {
            if ($updateFields != "")
                $updateFields.=",";
            $updateFields.= " HSA_PRICE = '" . $product->PriceGet() . "'";
        }

        if ($productRow["HSA_AMOUNT"] != $product->AmountGet()) {
            if ($updateFields != "")
                $updateFields.=",";
            $updateFields.= " HSA_AMOUNT = '" . $product->AmountGet() . "'";
        }
        if ($updateFields == "") return;
        $this->db->ExecuteNonQuery($query . $updateFields . $whereCondition);
    }

    private function loadProductRow($id) {
        $query = "select HSA_ID, HSA_TYPE, HSA_DESCRIPTION, HSA_PRICE,HSA_AMOUNT" .
                " from " . self::TABLE_NAME . " where id = " . $id;
        $dbResult = $this->db->ExecuteQuery($query);
        return $this->db->Fetch($dbResult);
    }

    public function GetProductByHSAIdAndType($hsaId, $type) {
        $id = $this->FindProductByHSAIdAndType($hsaId, $type);
        if ($id != NULL) {
            return $this->loadProduct($id);
        }
        return NULL;
    }
    
    private function FindProductByHSAIdAndType($hsaId, $type) {
        $query = "select id from " . self::TABLE_NAME . " where " .
                " HSA_ID = '" . $hsaId . "' and HSA_TYPE = '" . $type . "'";
        $dbResult = $this->db->ExecuteQuery($query);
        $row = $this->db->Fetch($dbResult);
        if (!$row)
            return NULL;
        return $row["id"];
    }
    
    private function findProduct($product) {
        return $this->FindProductByHSAIdAndType(
                $product->HSAIdGet(), $product->TypeGet());
    }

    private function loadProduct($id) {
        $productRow = $this->loadProductRow($id);
        $product = HSAProduct::Create(
                        $productRow["HSA_ID"], $productRow["HSA_TYPE"], $productRow["HSA_PRICE"], $productRow["HSA_AMOUNT"], $productRow["HSA_DESCRIPTION"]);
        return $product;
    }
    
    public function DeleteProduct($product) {
        $id = $this->findProduct($product);
        if ($id == NULL) return;
        $this->db->ExecuteNonQuery("delete from " . self::TABLE_NAME . " where id = " . $id);
    }
    
    public function FindPageProducts($offset, $pageSize) {
        $query = "select id from ".self::TABLE_NAME. " limit ".$offset.",".$pageSize;
        return $this->db->ExecuteQuery($query);
    }
    
    public function Fetch($dbResult) {
        $productIdRow = $this->db->Fetch($dbResult);
        if ($productIdRow == NULL) return NULL;
        return $this->loadProduct($productIdRow['id']);
    }

}

?>
