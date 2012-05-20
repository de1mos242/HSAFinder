<?php

/**
 * Description of HSAKYBJapanItemsLoaderTest
 *
 * @author de1mos <de1m0s242@gmail.com>
 */

require_once 'PHPUnit/Autoload.php';
require_once 'DataProviders/HSAKYBSiteItemsLoader.php';

class HSAKYBSiteLoaderTest extends PHPUnit_Framework_TestCase {
    private $fixture;
    
    private $db = NULL;
    
    private $gateway;
    
    protected function setUp() {
        $this->db = DBMySql::Create();
        $this->db->CreateDatabase();
        //$this->db->StartTransaction();
        $this->gateway = HSAItemGateway::Create($this->db);
        $this->gateway->CreateTable();
        $this->fixture = HSAKYBSiteItemsLoader::Create($this->gateway);
    }
    
    protected function tearDown() {
        //$this->db->RollbackTransaction();
    }
    
    /** 
    * @dataProvider SimpleStreamContent 
    */
    public function testLoadSimpleStream($markName, $modelName,$body,$year,$brandNumber,$type,
            $lineDirection, $handDirection) {
        $mark = Mark::Create($markName);
        $model = Model::Create($mark, $modelName);
        $item = HSAItem::Create($model, $year, $body, 
                    $brandNumber, array(),
                    $handDirection, $lineDirection, $type, "KYB");
        
        $this->fixture->ParseFile(dirname(__FILE__)."/testLoadKYBPriceSimple.csv");
        
        $itemId = $this->gateway->FindItem($item);
        $this->AssertNotNull($itemId, "item no found");
        $findedItem = $this->gateway->GetItemById($itemId);

        $this->AssertNotNull($findedItem);
        $this->AssertEquals($markName, $findedItem->ModelGet()->MarkGet()->NameGet());
        $this->AssertEquals($modelName, $findedItem->ModelGet()->NameGet());
        $this->AssertEquals($year, $findedItem->YearGet());
        $this->AssertEquals($body, $findedItem->BodyGet());
        $this->AssertEquals($brandNumber, $findedItem->BrandNumberGet());
        $this->AssertEquals($handDirection, $findedItem->HandDirectionGet());
        $this->AssertEquals($lineDirection, $findedItem->LineDirectionGet());
        $this->AssertEquals($type, $item->TypeGet());
    }

    /** 
    * @dataProvider SimpleStreamContent 
    */
    public function testLoadZipFile($markName, $modelName,$body,$year,$brandNumber,$type,
            $lineDirection, $handDirection) {
        $mark = Mark::Create($markName);
        $model = Model::Create($mark, $modelName);
        $item = HSAItem::Create($model, $year, $body, 
                    $brandNumber, array(),
                    $handDirection, $lineDirection, $type, "KYB");
        
        $this->fixture->ParseZipFile(dirname(__FILE__)."/testLoadKYBPriceSimple.csv.zip");
        
        $itemId = $this->gateway->FindItem($item);
        $this->AssertNotNull($itemId, "item no found");
        $findedItem = $this->gateway->GetItemById($itemId);

        $this->AssertNotNull($findedItem);
        $this->AssertEquals($markName, $findedItem->ModelGet()->MarkGet()->NameGet());
        $this->AssertEquals($modelName, $findedItem->ModelGet()->NameGet());
        $this->AssertEquals($year, $findedItem->YearGet());
        $this->AssertEquals($body, $findedItem->BodyGet());
        $this->AssertEquals($brandNumber, $findedItem->BrandNumberGet());
        $this->AssertEquals($handDirection, $findedItem->HandDirectionGet());
        $this->AssertEquals($lineDirection, $findedItem->LineDirectionGet());
        $this->AssertEquals($type, $item->TypeGet());
    }
    
    public function SimpleStreamContent()
    {
        return array(
            array("ALFA ROMEO","145 (930)","1.4 i.e.","07/1994 - 12/1996",554077,"","REAR","LEFT"),
            array("MERCEDES-BENZ","E-CLASS (W211)","E 420 CDI (211.029)","01/2006 - 12/2008","SM5501","","FRONT", "RIGHT"),
            //VOLVO,"S40 I (VS)",1.6,03/1999,12/2003,334439,Excel-G,"Front Axle Left",Twin-Tube,,"Suspension Strut",
            array("VOLVO","S40 I (VS)","1.6","03/1999 - 12/2003","334439","","FRONT","LEFT")
        ); 
    }
}

?>
