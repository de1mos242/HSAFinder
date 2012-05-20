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
        while (($line = fgets($file, 512)) !== false) {
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
        $hsaIds = $this->getHSAId($row[2]);
        foreach ($hsaIds as $value) {
            //echo "value = $value\n";
            $product = HSAProduct::Create($value, $type, $row[3], $amount, $description);
            $this->gateway->SaveProduct($product);
        }
    }

    function getHSAId($description) {
        if (mb_eregi("[\w\d/]{3,}[\d]$|[^\w\d]\([\w\d/]{4,}\)", $description, $regs)) {
            //echo "find! results. first: $regs[0]\n";
            $clean1 = mb_eregi_replace("\(|\)| ","", $regs[0]);
            //$clean2 = mb_ereg_replace(" ", "", $clean1);
            return mb_split("/", $clean1);
        }

        return array();
    }
}

?>
