<?php


/**
 * Description of HSAProductGatewayTest
 *
 * @author de1mos <de1m0s242@gmail.com>
 */

require_once 'PHPUnit/Autoload.php';
require_once 'DataLayer/HSAItemGateway.php';
require_once 'DataLayer/HSAProductGateway.php';
require_once 'DataProviders/HSAItemsTestGenerator.php';

class HSAItemGatewayTest extends PHPUnit_Framework_TestCase {
    
    private $fixture = NULL;
    
    private $db = NULL;
    
    protected function setUp() {
        $this->db = DBMySql::Create();
        $this->db->CreateDatabase();
        $this->db->StartTransaction();
        $this->fixture = HSAItemGateway::Create($this->db);
        $this->fixture->CreateTable();
    }
    
    protected function tearDown() {
        $this->db->RollbackTransaction();
    }
    
    public function testCRUD() {
    	$markName = 'TOYOTA';
        $modelName = "PASSO";
        $year = "04/06∼";
        $body = "KGC15(4WD)";
        $brandNumber = '332120';
        $oemNumbers = array('48510B1040');
        $handDirection = 'RIGHT';
        $lineDirection = 'FRONT';
        $type = 'GAS';
        $mark = Mark::Create($markName);
		$model = Model::Create($mark, $modelName);
		$item = HSAItem::Create($model, $year, $body, 
                    $brandNumber, $oemNumbers,
                    $handDirection, $lineDirection, $type, "KYB");
    	
    	$id = $this->fixture->SaveItem($item);
    	
    	$findedItem = $this->fixture->GetItemById($id);
        $this->AssertNotNull($findedItem);
        $this->AssertEquals($markName, $findedItem->ModelGet()->MarkGet()->NameGet());
        $this->AssertEquals($modelName, $findedItem->ModelGet()->NameGet());
        $this->AssertEquals($year, $findedItem->YearGet());
        $this->AssertEquals($body, $findedItem->BodyGet());
        $this->AssertEquals($brandNumber, $findedItem->BrandNumberGet());
        $oemItemNumbers = $findedItem->OEMNumbersGet();
        $this->AssertEquals($oemNumbers[0], $oemItemNumbers[0]);
        $this->AssertEquals($handDirection, $findedItem->HandDirectionGet());
        $this->AssertEquals($lineDirection, $findedItem->LineDirectionGet());
        $this->AssertEquals($type, $item->TypeGet());
        $this->AssertEquals($id, $item->IdGet());
        
        $newYear = "2011";
        $findedItem->YearSet($newYear);
        $newId = $this->fixture->SaveItem($findedItem);
        $this->AssertEquals($id, $newId);
        
        $findedItem = $this->fixture->GetItemById($id);
		$this->AssertNotNull($findedItem);
		$this->AssertEquals($markName, $findedItem->ModelGet()->MarkGet()->NameGet());
		$this->AssertEquals($modelName, $findedItem->ModelGet()->NameGet());
        $this->AssertEquals($newYear, $findedItem->YearGet());
        $this->AssertEquals($body, $findedItem->BodyGet());
        $this->AssertEquals($brandNumber, $findedItem->BrandNumberGet());
        $oemItemNumbers = $findedItem->OEMNumbersGet();
        $this->AssertEquals($oemNumbers[0], $oemItemNumbers[0]);
        $this->AssertEquals($handDirection, $findedItem->HandDirectionGet());
        $this->AssertEquals($lineDirection, $findedItem->LineDirectionGet());
        $this->AssertEquals($type, $item->TypeGet());
        $this->AssertEquals($id, $item->IdGet());
            
        $this->fixture->DeleteItem($findedItem);
        $findedModel = $this->fixture->GetItemById($newId);
        $this->AssertNull($findedModel);
    }
    
    public function testGenerator() {
        HSAItemsTestGenerator::generate($this->fixture);
        $item = HSAItem::Create(Model::Create(Mark::Create('TOYOTA'), "STARLET"), 
                    "73/04∼78/01", "KP40/42/45/47/51", 
                    '663028', array('4851010050'),
                    'RIGHT', 'FRONT', 'OIL', "KYB");
        $this->AssertNotNull($this->fixture->FindItem($item));
    }
    
    public function testSearchByMark() {
        HSAItemsTestGenerator::generate($this->fixture);
        $dbResult = $this->fixture->FindItemsByMarkName('TOYOTA', 0, 1000);
        $this->AssertNotNull($this->fixture->Fetch($dbResult));
    }
    
    public function testSearchByMarkAndModelNames() {
        HSAItemsTestGenerator::generate($this->fixture);
        $dbResult = $this->fixture->FindItemsByMarkAndModelNames('TOYOTA','STARLET', 0, 1000);
        $this->AssertNotNull($this->fixture->Fetch($dbResult));
    }

    public function testFindItemWithProduct() {
        $hsaId = '663028';
        $type = "KYB";
        $price = 100;
        $amount = 'little';
        HSAItemsTestGenerator::generate($this->fixture);
        $item = HSAItem::Create(Model::Create(Mark::Create('TOYOTA'), "STARLET"), 
                    "73/04∼78/01", "KP40/42/45/47/51", 
                    $hsaId, array('4851010050'),
                    'RIGHT', 'FRONT', 'OIL', "KYB");
        $product = HSAProduct::Create($hsaId, $type, $price, $amount, "testDescription");
        HSAProductGateway::Create($this->db)->SaveProduct($product);

        $findedItemId = $this->fixture->FindItem($item);
        $findedItem = $this->fixture->GetItemById($findedItemId);
        $this->AssertNotNull($findedItem);
        $this->AssertNotNull($findedItem->ProductGet());
        $this->AssertEquals($hsaId, $findedItem->ProductGet()->HSAIdGet());
        $this->AssertEquals($type, $findedItem->ProductGet()->TypeGet());
        $this->AssertEquals($price, $findedItem->ProductGet()->PriceGet());
        $this->AssertEquals($amount, $findedItem->ProductGet()->AmountGet());
    }
}

?>
