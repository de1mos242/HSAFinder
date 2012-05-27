<?php

/**
 * Description of HSAProductsLoader
 *
 * @author de1mos <de1m0s242@gmail.com>
 */

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR .'DataLayer'. DIRECTORY_SEPARATOR .'HSAProductGateway.php';
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR .'Helpers'. DIRECTORY_SEPARATOR .'CSVParser.php';

class HSAProductsLoader {
    private $gateway;
    private $parser;
    public static function Create($productsGateway) {
        $loader = new HSAProductsLoader();
        $loader->gateway = $productsGateway;
        return $loader;
    }
    
    public function ParseFile($filename) {
        $file = fopen($filename, "r");
        if (!$file) {
            throw new Exception ("Не удалось открыть файл: $filename");
        }
        $this->prepareParse();
        while (($line = fgets($file, 2048)) !== false) {
            $this->readLine($line);
        }
        fclose($file);
    }
    
    private function prepareParse() {
        $this->parser = new CSVParser();
        $this->gateway->CreateTable();
    }
    
    private function readLine($line) {
        $csv = $this->parser->ParseLine($line);
        if ($csv[0] == "") return;
        
        $this->convertLine($csv);
    }
    
    private function convertLine($row) {
        $type = "";
        if (mb_eregi("KYB", $row[1]) == 1) {
            $type = "KYB";
        }
        elseif (mb_eregi("TOKICO", $row[1]) == 1) {
            $type = "TOKICO";
        }
        // not supported
        //elseif (mb_eregi("MONROE", $row[1]) == 1) {
        //    $type = "MONROE";
        //}
        else {
            //throw new Exception("Unknown type: ".$row[1]);
            return; // skip unknown data
        }
        $amount = "";
        if (mb_eregi("Один", $row[4]) == 1) {
            $amount = "one";
        }
        elseif (mb_eregi("Мало", $row[4]) == 1) {
            $amount = "little";
        }
        elseif (mb_eregi("Нет", $row[4]) == 1) {
            $amount = "no";
        }
        elseif (mb_eregi("Норм", $row[4]) == 1) {
            $amount = "medium";
        }
        elseif (mb_eregi("Много", $row[4]) == 1) {
            $amount = "many";
        }
        elseif (mb_eregi("Завал", $row[4]) == 1) {
            $amount = "mega";
        }
        else {
            throw new Exception("Unknown amount: ".$row[4]);
        }
        
        $description = mb_eregi_replace("'", " ", $row[2]);
        $hsaIds = $this->getHSAId($description);
        foreach ($hsaIds as $value) {
            //echo "value = $value\n";
            $product = HSAProduct::Create($value, $type, $row[3], $amount, $description);
            $this->gateway->SaveProduct($product);
        }
    }

    function getHSAId($description) {
        /*if (mb_eregi("[\w\d/]{3,}[\d]$|[^\w\d]\([\w\d/]{4,}\)", $description, $regs)) {*/
        /*if (mb_eregi("[^\d][\d]{6,6}[^d]", $description)) {
            //echo "find! results. first: $regs[0]\n";
            $clean1 = mb_ereg_replace("[^\d]", "", string);
            //$clean1 = mb_eregi_replace("\(|\)| ","", $regs[0]);
            //$clean2 = mb_ereg_replace(" ", "", $clean1);
            return mb_split("/", $clean1);
        }*/
        $result = array();
        if (mb_eregi("KYB", $description)) {
            if (preg_match_all("/[^\d][\d]{6,6}([^\d]|$)/", $description, $results) ) {
                foreach ($results[0] as $value) {
                    $result[] = mb_ereg_replace("[^\d]", "", $value);
                }
            }
        }
        elseif (mb_eregi("TOKICO", $description)) {
            $description = $this->translitIt($description);
            if (mb_eregi("[^a-z][a-z]{1,2}[ ]?[\d]{4,5}([^\d]|$)", $description, $results)) {
                foreach ($results as $value) {
                    $clean = mb_eregi_replace("[^\da-z]", "", $value);
                    if ($clean == '')
                        continue;
                    $result[] = $clean;
                }
            }
        }
        return $result;
    }

    function translitIt($str) 
    {
        $tr = array(
            "А"=>"A","В"=>"B","Е"=>"E",
            "К"=>"K","М"=>"M","Н"=>"H",
            "О"=>"O","Р"=>"P","С"=>"C","Т"=>"T",
            "У"=>"Y","Х"=>"X"
        );
        return strtr($str,$tr);
    }

}

?>
