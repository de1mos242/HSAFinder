<?php

/**
 * Description of HSAProduct
 *
 * @author de1mos <de1m0s242@gmail.com>
 */

class Mark {
    private $name;
    
    public function NameGet() {
        return $this->name;
    }
    
    public static function Create($name) {
    	$mark = new Mark();
    	$mark->name = $name;
        return $mark;
    }
}

?>
