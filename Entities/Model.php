<?php

/**
 * Description of HSAProduct
 *
 * @author de1mos <de1m0s242@gmail.com>
 */

require_once dirname(__FILE__). DIRECTORY_SEPARATOR ."Mark.php";

class Model {
    private $name;
    private $mark;
    
    public function NameGet() {
        return $this->name;
    }
    
    public function MarkGet() {
    	return $this->mark;
    }
    
    public static function Create($mark, $name) {
    	$model = new Model();
    	$model->mark = $mark;
    	$model->name = $name;
    	return $model;
    }
}

?>
