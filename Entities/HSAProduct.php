<?php

/**
 * Description of HSAProduct
 *
 * @author de1mos <de1m0s242@gmail.com>
 */

class HSAProduct {
    private $hsaId;
    private $description;
    private $price;
    private $amount;
    private $type;

    const AMOUNTONE = "one";
    const AMOUNTLITTLE = "little";
    const AMOUNTNO = "no";
    const AMOUNTMEDIUM = "medium";
    const AMOUNTMANY = "many";
    const AMOUNTMEGA = "mega";
        
    
    public function HSAIdGet() {
        return $this->hsaId;
    }
    
    public function HSAIdSet($value) {
        $this->hsaId = $value;
    }
    
    public function DescriptionGet() {
        return $this->description;
    }
    
    public function DescriptionSet($value) {
        $this->description = $value;
    }
    
    public function PriceGet() {
        return $this->price;
    }
    
    public function PriceSet($value) {
        $this->price = $value;
    }
    
    public function AmountGet() {
        return $this->amount;
    }

    public function AmountLocaleGet() {
        return $this->translateAmount($this->amount);
    }
    
    public function AmountSet($value) {
        $this->amount = $value;
    }
    
    public function TypeGet() {
        return $this->type;
    }
    
    public function TypeSet($value) {
        $this->type = $value;
    }

    public function translateAmount($amount) {
        if ($amount == self::AMOUNTONE)
            return "Один";
        elseif ($amount == self::AMOUNTLITTLE)
            return "Мало";
        elseif ($amount == self::AMOUNTNO)
            return "Нет";
        elseif ($amount == self::AMOUNTMEDIUM)
            return "Норм";
        elseif ($amount == self::AMOUNTMANY)
            return "Много";
        elseif ($amount == self::AMOUNTMEGA)
            return "Завал";
        else
            return "";
    }
    
    public static function Create($hsaId, $type, $price, $amount, $description) {
        $product = new HSAProduct();
        $product->hsaId = $hsaId;
        $product->type = $type;
        $product->price = $price;
        $product->amount = $amount;
        $product->description = $description;
        return $product;
    }
}

?>
