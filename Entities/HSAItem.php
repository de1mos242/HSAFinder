<?php

/**
 * Description of HSAProduct
 *
 * @author de1mos <de1m0s242@gmail.com>
 */

require_once dirname(__FILE__). DIRECTORY_SEPARATOR ."Model.php";

class HSAItem {
    private $model;
    private $year;
    private $body;
    private $brandNumber;
    private $oemNumbers = array();
    private $handDirection;
    private $lineDirection;
    private $type;
    private $id = NULL;
    private $product;
    private $hsaType;
    
    public function ModelGet() {
        return $this->model;
    }
    public function ModelSet($value) {
    	$this->model = $value;
    }
    public function BodyGet() {
    	return $this->body;
    }
    public function BodySet($value) {
    	$this->body = mysql_real_escape_string($value);
    }    
    public function YearGet() {
    	return $this->year;
    }
    public function YearSet($value) {
    	$this->year = mysql_real_escape_string($value);
    }    
    public function HSATypeGet() {
        return $this->hsaType;
    }
    public function HSATypeSet($value) {
        $this->hsaType = mysql_real_escape_string($value);
    }
    public function BrandNumberGet() {
    	return $this->brandNumber;
    }
    public function BrandNumberSet($value) {
    	$this->brandNumber = mysql_real_escape_string($value);
    }
    
    public function OEMNumbersGet() {
    	return $this->oemNumbers;
    }
    public function OEMNumbersStringGet() {
        $result = '';
        foreach ($this->oemNumbers as $number) {
            if ($result != '')
                $result.=' ';
            $result.=$number;
        }
        return $result;
    }
    public function OEMNumbersSet($value) {
    	$this->oemNumbers = $value;
    }
    public function OEMNumbersAppend($value) {
        $this->oemNumbers = array_unique(array_merge($this->oemNumbers, $value));
    }
    
    public function HandDirectionGet() {
    	return $this->handDirection;
    }
    public function HandDirectionSet($value) {
    	$this->handDirection = mysql_real_escape_string($value);
    }
    public function LineDirectionGet() {
    	return $this->lineDirection;
    }
    public function LineDirectionSet($value) {
    	$this->lineDirection = mysql_real_escape_string($value);
    }
    public function TypeGet() {
    	return $this->type;
    }
    public function TypeSet($value) {
    	$this->type = mysql_real_escape_string($value);
    }
    public function IdGet() {
    	return $this->id;
    }
    public function IdSet($value) {
        if ($value == NULL)
            $this->id = NULL;
    	if ($this->id != NULL)
    		throw new Exception ('Id already was setted');
    	$this->id = mysql_real_escape_string($value);
    }
    public function ProductGet() {
        return $this->product;
    }
    public function ProductSet($value) {
        if ($this->product != NULL)
            throw new Exception ('product already was setted');
        $this->product = $value;
    }
    
    public static function Create($model, $year, $body, 
                    $brandNumber, $oemNumbersArray,
                    $handDirection, $lineDirection, $type, $hsaType, $id=NULL, $product=NULL) {
        $item = new HSAItem();
        $item->model = $model;
        $item->year = $year;
        $item->body = $body;
        $item->brandNumber = $brandNumber;
        $item->oemNumbers = $oemNumbersArray;
        $item->handDirection = $handDirection;
        $item->lineDirection = $lineDirection;
        $item->type = $type;
        $item->hsaType = $hsaType;
        $item->id = $id;
        $item->product = $product;
    	return $item;
    }
}

?>
