<?php

/**
 * Description of HSAProductsLoaderTest
 *
 * @author de1mos <de1m0s242@gmail.com>
 */

require_once 'PHPUnit/Autoload.php';
require_once 'DataProviders/HSAProductsLoader.php';

class HSAProductsLoaderTest extends PHPUnit_Framework_TestCase {
    private $fixture;
    
    private $db = NULL;
    
    private $gateway;
    
    protected function setUp() {
        $this->db = DBMySql::Create();
        $this->db->CreateDatabase();
        $this->db->StartTransaction();
        $this->gateway = HSAProductGateway::Create($this->db);
        $this->fixture = HSAProductsLoader::Create($this->gateway);
    }
    
    protected function tearDown() {
        $this->db->RollbackTransaction();
    }
    
    /** 
    * @dataProvider SimpleStreamContent 
    */
    public function testLoadSimpleStream($hsaId, $type, $description, $price, $amount) {
        $this->fixture->ParseFile(dirname(__FILE__)."/testLoadSimpleStream.csv");
        
        $product = $this->gateway->GetProductByHSAIdAndType($hsaId,$type);
        if ($hsaId != "0") {
            $this->AssertNotNull($product);
            $this->AssertEquals($hsaId, $product->HSAIdGet());
            $this->AssertEquals($type, $product->TypeGet());
            $this->AssertEquals($price, $product->PriceGet());
            $this->AssertEquals($amount, $product->AmountGet());
            $this->AssertEquals($description, $product->DescriptionGet());
        }
        else {
            $this->AssertNull($product);
        }
    }
    
    public function SimpleStreamContent()
    {
        return array(
            array("551113", "KYB", "KYB -SPORT(ESK9153L) 551113",6202,"one"), 
            array("551112", "KYB", "KYB -SPORT(ESK9153R) 551112",6202,"one"), 
            array("341176", "KYB", "KYB -SPORT(NSF9062) (341176)",2652,"little"),
            array("0", "KYB", "KYB -SPORT(NSF1027)",2664,"little"), 
            array("0", "KYB", "KYB -SPORT(NSF1028)",2664,"little"),
            array("344288", "TOKICO", "TOKICO  E 3740  TO LC90 rear (344288)",780,"one"),
            //50258,"АМОРТИЗАТОРЫ   ""  TOKICO ""","TOKICO  E 3594 (344346/444150/554105) MI L200/Forte/Triton K66T [3.0 V6 2WD],KT74/75 4WD 96",545,Нет
            array("344346", "TOKICO", "TOKICO  E 3594 (344346/444150/554105) MI L200/Forte/Triton K66T [3.0 V6 2WD],KT74/75 4WD 96",545,"no"),
            array("444150", "TOKICO", "TOKICO  E 3594 (344346/444150/554105) MI L200/Forte/Triton K66T [3.0 V6 2WD],KT74/75 4WD 96",545,"no"),
            array("554105", "TOKICO", "TOKICO  E 3594 (344346/444150/554105) MI L200/Forte/Triton K66T [3.0 V6 2WD],KT74/75 4WD 96",545,"no"),
            //43279,"АМОРТИЗАТОРЫ   ""  TOKICO """,TOKICO  E 3789  стойка MZ CX9  3.5  07- задн,1062,Мало
            array("0", "TOKICO", "TOKICO  E 3789  стойка MZ CX9  3.5  07- задн",1062,"little")
        ); 
    }
    
    /** 
    * @dataProvider RealPriceContent 
    */
    public function testRealPrice($hsaId, $type, $description, $price, $amount) {
	    //$this->markTestSkipped("too long");
        $this->fixture->ParseFile(dirname(__FILE__)."/testRealPrice.csv");
        
        $product = $this->gateway->GetProductByHSAIdAndType($hsaId,$type);
        if ($hsaId != "0") {
            $this->AssertNotNull($product);
            $this->AssertEquals($hsaId, $product->HSAIdGet());
            $this->AssertEquals($type, $product->TypeGet());
            $this->AssertEquals($price, $product->PriceGet());
            $this->AssertEquals($amount, $product->AmountGet());
            $this->AssertEquals($description, $product->DescriptionGet());
        }
        else {
            $this->AssertNull($product);
        }
    }
    
    public function RealPriceContent()
    {
        return array(
            // 17642,"АМОРТИЗАТОРЫ   ""  KYB """,KYB SM 1708,1097,Нет
            array("1708", "KYB", "KYB SM 1708",1097,"no"), 
            // 50263,"АМОРТИЗАТОРЫ   ""  TOKICO """,TOKICO  51605-SDG-H01    KYB 341330,865,Один
            array("0", "TOKICO", "TOKICO  51605-SDG-H01    KYB 341330",865,"one"), 
            // 43312,"АМОРТИЗАТОРЫ   ""  TOKICO ""","TOKICO JD344363   KYB 344363  (TC230-28-700E) Mazda 6/Atenza GG3S,GY3W (2wd) rear)",1226,Мало
            array("JD344363", "TOKICO", "TOKICO JD344363   KYB 344363  (TC230-28-700E) Mazda 6/Atenza GG3S,GY3W (2wd) rear)",1226,"little"),
            //51202,"АМОРТИЗАТОРЫ   ""  TOKICO """,TOKICO  E 20012,1503,Мало
            array("20012", "TOKICO", "TOKICO  E 20012",1503,"little"),
        ); 
    }

}


?>
