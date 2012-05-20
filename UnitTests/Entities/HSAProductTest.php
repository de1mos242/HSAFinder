<?php

/**
 * Description of HSAProductTest
 *
 * @author de1mos <de1m0s242@gmail.com>
 */

require_once 'PHPUnit/Autoload.php';
require_once 'Entities/HSAProduct.php';

class HSAProductTest extends PHPUnit_Framework_TestCase {
    
    public function testCreateProduct() {
        $id = "5";
        $type = "type1";
        $price = 4.33;
        $amount = "little";
        $description = "trololo";
        
        $product = HSAProduct::Create($id, $type, $price, $amount, $description);
        $this->AssertEquals($id, $product->HSAIdGet());
        $this->AssertEquals($type, $product->TypeGet());
        $this->AssertEquals($price, $product->PriceGet());
        $this->AssertEquals($amount, $product->AmountGet());
        $this->AssertEquals($description, $product->DescriptionGet());
    }
}

?>
