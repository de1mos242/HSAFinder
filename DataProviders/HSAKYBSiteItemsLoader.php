<?php

/**
 * Description of HSAJapanItesLoader
 *
 * @author de1mos <de1m0s242@gmail.com>
 */

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR .'DataLayer'. DIRECTORY_SEPARATOR .'HSAItemGateway.php';
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR .'Helpers'. DIRECTORY_SEPARATOR .'CSVParser.php';

class HSAKYBSiteItemsLoader {
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
    
    private $handDirections = array();
    private $brandNumber = '';
    private $type = '';
    
    public static function Create($itemsGateway) {
        $loader = new HSAKYBSiteItemsLoader();
        $loader->gateway = $itemsGateway;
        return $loader;
    }

    public static function UploadFile($filename) {
        $start = date("r");
        $db = DBMySql::Create();
        $db->CreateDatabase();
        //$this->db->StartTransaction();
        $gateway = HSAItemGateway::Create($db);
        $gateway->CreateTable();
        $fixture = HSAKYBSiteItemsLoader::Create($gateway);
        $fixture->ParseFile($filename);
        $end = date("r");
        echo "started at $start\nended at $end\n";
    }
    
    public function ParseFile($filename) {
        $this->file = fopen($filename, "r");
        if (!$this->file) {
            throw new Exception ("Не удалось открыть файл: $filename");
        }
        $this->prepareParse();
        while ($this->ReadLine()){
            $this->processLine();
        }
        fclose($this->file);
    }

    public function ParseZipFile($filename) {
        $this->prepareParse();
        $lines = $this->getStringsFromZipFile($filename);
        foreach ($lines as $line) {
            $this->rawLine = $this->cleanLine($line);
            $this->line = $this->parser->ParseLine($this->rawLine);
            $this->processLine();
        }
    }

    private function cleanLine($line) {
        return mb_ereg_replace("'", "", $line);
    }

    private function getStringsFromZipFile($filename) {
        $zipOpen = zip_open($filename);
        $zipRead = zip_read($zipOpen);
        if (!zip_entry_open($zipOpen,$zipRead))
            throw new Exception("Не удалось открыть zip архив");
        $fullString = zip_entry_read($zipRead,zip_entry_filesize($zipRead));
        //echo $fullString;
        $lines = mb_split("\n", $fullString);
        return $lines;
    }
    
    private function ReadLine() {
        $line = fgets($this->file, 512);
        if ($line == false) 
            return false;
        $this->rawLine = $this->cleanLine($line);
        
        echo $this->rawLine;
        $this->line = $this->parser->ParseLine($this->rawLine);
        return true;
    }
    
    private function prepareParse() {
        $this->parser = new CSVParser();
        //$this->gateway->CreateTable();
    }
    
    private function processLine() {
        $this->mark = $this->line[0];
        $this->model = $this->line[1];
        $this->body = $this->line[2];
        $this->brandNumber = $this->line[5];
        
        $this->parseYear();
        $this->parseHandDirections();
        $this->parseLineDirection();
        $this->parseType();

        $this->generateItems();
    }

    private function parseYear() {
        $this->year = $this->line[3] . " - " . $this->line[4];
    }
    
    private function parseHandDirections() {
        $result = array();
        if (mb_eregi("left", $this->line[7]))
            $this->handDirections = array(self::LEFT);
        elseif (mb_eregi("right", $this->line[7]))
            $this->handDirections = array(self::RIGHT);
        else
            $this->handDirections = array(self::RIGHT,self::LEFT);
    }

    private function parseLineDirection() {
        if (mb_eregi("front", $this->line[7]))
            $this->lineDirection = self::FRONT;
        else
            $this->lineDirection = self::REAR;
    }

    private function parseType() {
        $this->type = '';
    }
    
    
    private function generateItems() {
        $model = Model::Create(Mark::Create($this->mark), $this->model);
        foreach ($this->handDirections as $handDirection) {
            $item = HSAItem::Create($model, 
                $this->year, $this->body, 
                $this->brandNumber, array(),
                $handDirection, $this->lineDirection, $this->type, "KYB");
            $this->gateway->SaveItem($item);
        }
    }
}

?>