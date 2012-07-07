<?php

/**
 *
 * @author de1mos
 */
interface IDBConnection {
    function InitTables();
    static function Create($dbname="", $login="", $password="");
    function ExecuteNonQuery($execution_string);
    function ExecuteQuery($query);
    function StartTransaction();
    function CommitTransaction();
    function RollbackTransaction();
    function Fetch($result);
    function RowsCount($result);
}

?>
