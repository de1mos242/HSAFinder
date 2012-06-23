<?php

/**
 * Description of HSAKYBJapanItemsLoaderTest
 *
 * @author de1mos <de1m0s242@gmail.com>
 */

require_once 'PHPUnit/Autoload.php';
require_once 'DataProviders/HSAKYBJapanItemsLoader.php';

class HSAKYBJapanItemsLoaderTest extends PHPUnit_Framework_TestCase {
    private $fixture;
    
    private $db = NULL;
    
    private $gateway;
    
    protected function setUp() {
        $this->db = DBMySql::Create();
        $this->db->CreateDatabase();
        $this->db->StartTransaction();
        $this->gateway = HSAItemGateway::Create($this->db);
        $this->gateway->CreateTable();
        $this->fixture = HSAKYBJapanItemsLoader::Create($this->gateway);
    }
    
    protected function tearDown() {
        $this->db->RollbackTransaction();
    }
    
    /** 
    * @dataProvider SimpleStreamContent 
    */
    public function testLoadSimpleStream($markName, $modelName) {
        $this->fixture->ParseFile(dirname(__FILE__)."/newCleaned4.csv");
        
        $dbResult = $this->gateway->FindItemsByMarkAndModelNames($markName,$modelName, 0, 1000);
        $this->AssertNotNull($this->gateway->Fetch($dbResult));
    }
    
    public function SimpleStreamContent()
    {
        return array(
           // array('ASIA','ROKSTA'), 
            //array('DAEWOO','TICO'), 
            //array('DAEWOO','MATIZ/MATIS'), 
            //array('DAEWOO','MATIZ/Chevrolet MATIZ/Chevrolet SPARK/Pontiaic MATIZ G1'), 
            array('DAEWOO','ROYAL RECORD')
        ); 
    }
}

?>
