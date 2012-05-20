<?php

/**
 * Description of HSAItem
 *
 * @author de1mos <de1m0s242@gmail.com>
 */
 
require_once 'PHPUnit/Autoload.php';
require_once 'Entities/Model.php';

class ModelTest  extends PHPUnit_Framework_TestCase {
    
    public function testCreateModel() {
       	//$this->markTestIncomplete();
        $markName = 'TOYOTA';
        $modelName = "PASSO";
        $mark = Mark::Create($markName);
        $model = Model::Create($mark,$modelName);
        $this->AssertEquals($markName, $model->MarkGet()->NameGet());
        $this->AssertEquals($modelName, $model->NameGet());
    }
    
}

?>
