<?php


/**
 * Description of HSAProductGatewayTest
 *
 * @author de1mos <de1m0s242@gmail.com>
 */

require_once 'PHPUnit/Autoload.php';
require_once 'DataLayer/HSAProductGateway.php';

class HSAProductGatewayTest extends PHPUnit_Framework_TestCase {
    
    private $fixture = NULL;
    
    private $db = NULL;
    
    protected function setUp() {
        $this->db = DBMySql::Create();
        $this->db->CreateDatabase();
        $this->db->StartTransaction();
        $this->fixture = HSAProductGateway::Create($this->db);
        $this->fixture->CreateTable();
    }
    
    protected function tearDown() {
        $this->db->RollbackTransaction();
    }
    
    public function testCRUD() {
        $hsaId = "5";
        $type = "type1";
        $price = 4.33;
        $amount = "little";
        $description = "trololo";
        $product = HSAProduct::Create($hsaId, $type, $price, $amount, $description);
        
        $this->fixture->SaveProduct($product);
        
        $findedProduct = $this->fixture->GetProductByHSAIdAndType($hsaId, $type);
        $this->AssertNotNull($findedProduct);
        $this->AssertEquals($hsaId, $findedProduct->HSAIdGet());
        $this->AssertEquals($type, $findedProduct->TypeGet());
        $this->AssertEquals($price, $findedProduct->PriceGet());
        $this->AssertEquals($amount, $findedProduct->AmountGet());
        $this->AssertEquals($description, $findedProduct->DescriptionGet());
        
        $newDescription = "checkDescription";
        $product->DescriptionSet($newDescription);
        
        $this->fixture->SaveProduct($product);
        
        $findedProduct = $this->fixture->GetProductByHSAIdAndType($hsaId, $type);
        $this->AssertNotNull($findedProduct);
        $this->AssertEquals($hsaId, $findedProduct->HSAIdGet());
        $this->AssertEquals($type, $findedProduct->TypeGet());
        $this->AssertEquals($price, $findedProduct->PriceGet());
        $this->AssertEquals($amount, $findedProduct->AmountGet());
        $this->AssertEquals($newDescription, $findedProduct->DescriptionGet());
        
        $this->fixture->DeleteProduct($findedProduct);
        $findedProduct = $this->fixture->GetProductByHSAIdAndType($hsaId, $type);
        $this->AssertNull($findedProduct);
        
    }
    
    public function testFindLimitProducts() {
        $count = 2;
        
        $hsaId1 = "5";
        $type1 = "type1";
        $price1 = 4.33;
        $amount1 = "little";
        $description1 = "trololo";
        $product = HSAProduct::Create($hsaId1, $type1, $price1, $amount1, $description1);
        
        $this->fixture->SaveProduct($product);
        
        $hsaId2 = "6";
        $type2 = "type2";
        $price2 = 5.33;
        $amount2 = "no";
        $description2 = "trololo2";
        $product = HSAProduct::Create($hsaId2, $type2, $price2, $amount2, $description2);
        
        $this->fixture->SaveProduct($product);
        
        $hsaId3 = "7";
        $type3 = "type3";
        $price3 = 5.32;
        $amount3 = "many";
        $description3 = "trololo3";
        $product = HSAProduct::Create($hsaId3, $type3, $price3, $amount3, $description3);
        
        $this->fixture->SaveProduct($product);
        
        $dbresult = $this->fixture->FindPageProducts(0, $count);
        for ($i=1;$i <= $count;$i++) {
            $product = $this->fixture->Fetch($dbresult);
            $this->AssertNotNull($product);
            $hsaIdName = "hsaId".$i;
            $this->AssertEquals($$hsaIdName, $product->HSAIdGet());
            $typeName = "type".$i;
            $this->AssertEquals($$typeName, $product->TypeGet());
            $priceName = "price".$i;
            $this->AssertEquals($$priceName, $product->PriceGet());
            $amountName = "amount".$i;
            $this->AssertEquals($$amountName, $product->AmountGet());
            $descriptionName = "description".$i;
            $this->AssertEquals($$descriptionName, $product->DescriptionGet());
        }
    }
}

?>
