<?php

/**
 * Description of HSAJapanItesLoader
 *
 * @author de1mos <de1m0s242@gmail.com>
 */

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR .'DataLayer'. DIRECTORY_SEPARATOR .'HSAItemGateway.php';
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR .'Helpers'. DIRECTORY_SEPARATOR .'CSVParser.php';

class HSAKYBJapanItemsLoader {
    const REAR = "REAR";
    const FRONT = "FRONT";
    const GAS = "GAS";
    const OIL = "OIL";
    const RIGHT = "RIGHT";
    const LEFT = "LEFT";
    
    private $gateway;
    private $parser;
    private $file;
    
    private $line;
    private $rawLine = '';
    
    private $mark = '';
    private $model = '';
    private $year = '';
    private $body = '';
    
    private $skipLines = 0;
    
    private $handDirections = array();
    private $brandNumbersGas = array();
    private $brandNumbersOil = array();
    private $oemNumbers = array();

    private $curLineNumber = 0;

    public static function UploadFile($filename) {
        $start = date("r");
        $db = DBMySql::Create();
        $db->CreateDatabase();
        //$this->db->StartTransaction();
        $gateway = HSAItemGateway::Create($db);
        //$gateway->CreateTable();
        $fixture = HSAKYBJapanItemsLoader::Create($gateway);
        $fixture->ParseFile($filename);
        $end = date("r");
        //echo "started at $start\nended at $end\n";
    }
    
    public static function Create($itemsGateway) {
        $loader = new HSAKYBJapanItemsLoader();
        $loader->gateway = $itemsGateway;
        return $loader;
    }
    
    public function ParseFile($filename) {
        $this->file = fopen($filename, "r");
        if (!$this->file) {
            throw new Exception ("Не удалось открыть файл: $filename");
        }
        $this->skipLines = 0;
        $this->prepareParse();
        while ($this->ReadLine()){
            $this->processLine();
        }
        fclose($this->file);
    }
    
    private function ReadLine() {
        while ($this->skipLines > 0) {
            if (fgets($this->file) == false )
                return false;
            $this->skipLines--;
        }
        $line = fgets($this->file);
        if ($line == false) 
            return false;
        $this->rawLine = $line;
        $this->line = $this->parser->ParseLine($line);
        return true;
    }
    
    private function prepareParse() {
        $this->parser = new CSVParser();
        //$this->gateway->CreateTable();
    }
    
    private function processLine() {
        if (!empty($this->line[0]))
            $this->mark = $this->line[0];
        if (!empty($this->line[1])) 
            $this->model = $this->line[1];
        if (!empty($this->line[2])) {
            $this->year = $this->line[2];
        }
        if (!empty($this->line[3])) {
            $this->body = $this->line[3];
        }
        
        $this->processFront();
        $this->generateItems(self::FRONT);
        $this->processRear();
        $this->generateItems(self::REAR);
    }
    
    private function processFront() {
        $this->handDirections = $this->getHandDirections($this->line[4]);
        $this->brandNumbersOil = $this->getNumbers($this->line[5]);
        $this->brandNumbersGas = $this->getNumbers($this->line[6]);
        $this->oemNumbers = $this->getNumbers($this->line[7]);
    }
    
    private function processRear() {
        $this->handDirections = $this->getHandDirections($this->line[8]);
        $this->brandNumbersOil = $this->getNumbers($this->line[9]);
        $this->brandNumbersGas = $this->getNumbers($this->line[10]);
        $this->oemNumbers = $this->getNumbers($this->line[11]);
    }
    
    private function cleanNumber($value) {
        return mb_ereg_replace('%[^a-z/\d]', '', $value, 'i'); 
    }
    
    private function getHandDirections($directions) {
        switch ($directions) {
            case 'R':
                return array(self::RIGHT);
            case 'L':
                return array(self::LEFT);
            default:
                return array(self::RIGHT,self::LEFT);
        }
    }
    
    private function getNumbers($numbers) {
        $raw = mb_split(" ", $numbers);
        $result = array();
        foreach ($raw as $value) {
            $cleanValue = $this->cleanNumber($value); 
            if ($cleanValue != '')
                $result[] = $cleanValue;
        }
        return $result;
    }
    
    private function generateItems($lineDirection) {
        $model = Model::Create(Mark::Create($this->mark), $this->model);
        foreach ($this->handDirections as $handDirection) {
            foreach ($this->brandNumbersGas as $brandNumber) {
                $item = HSAItem::Create($model, 
                        $this->year, $this->body, 
                        $brandNumber, $this->oemNumbers,
                        $handDirection, $lineDirection, self::GAS, "KYB");
                $this->gateway->SaveItem($item);
            }
            foreach ($this->brandNumbersOil as $brandNumber) {
                $item = HSAItem::Create($model, 
                        $this->year, $this->body, 
                        $brandNumber, $this->oemNumbers,
                        $handDirection, $lineDirection, self::OIL, "KYB");
                $this->gateway->SaveItem($item);
            }
        }
    }
}

?>