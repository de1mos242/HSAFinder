<?php

/**
 * Description of HSAItem
 *
 * @author de1mos <de1m0s242@gmail.com>
 */
 
require_once 'PHPUnit/Autoload.php';
require_once 'Entities/Mark.php';

class MarkTest  extends PHPUnit_Framework_TestCase {
    
    public function testCreateMark() {
        $name = 'TOYOTA';
        $mark = Mark::Create($name);
        $this->AssertEquals($name, $mark->NameGet());
    }
    
}

?>
