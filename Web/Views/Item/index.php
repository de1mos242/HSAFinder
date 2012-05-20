<?php
require_once dirname(dirname(dirname(__FILE__))). DIRECTORY_SEPARATOR . "Helpers" .
            DIRECTORY_SEPARATOR . "Item" . DIRECTORY_SEPARATOR . "ItemsTable.php";
echo ItemsTable::GetTable($registry->get("content"));
?>