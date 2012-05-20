<?php

/**
 * Description of HSAItem
 *
 * @author de1mos <de1m0s242@gmail.com>
 */
 
require_once 'PHPUnit/Autoload.php';
require_once 'Entities/HSAItem.php';

class HSAItemTest extends PHPUnit_Framework_TestCase {
    
    public function testCreateItem() {
    	//$this->markTestIncomplete();
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
        $model = Model::Create($mark,$modelName);
        $item = HSAItem::Create($model, $year, $body, 
                    $brandNumber, $oemNumbers,
                    $handDirection, $lineDirection, $type, "KYB");
        $this->AssertEquals($markName, $item->ModelGet()->MarkGet()->NameGet());
        $this->AssertEquals($modelName, $item->ModelGet()->NameGet());
        $this->AssertEquals($year, $item->YearGet());
        $this->AssertEquals($body, $item->BodyGet());
        $this->AssertEquals($brandNumber, $item->BrandNumberGet());
        $oemItemNumbers = $item->OEMNumbersGet();
        $this->AssertEquals($oemNumbers[0], $oemItemNumbers[0]);
        $this->AssertEquals($handDirection, $item->HandDirectionGet());
        $this->AssertEquals($lineDirection, $item->LineDirectionGet());
        $this->AssertEquals($type, $item->TypeGet());
        $this->AssertEquals(NULL, $item->IdGet());
    }
    
}

?>
