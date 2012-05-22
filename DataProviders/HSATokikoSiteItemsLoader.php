<?php

/**
 * Description of HSATokikoSiteItemsLoader
 *
 * @author de1mos <de1m0s242@gmail.com>
 */

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR .'DataLayer'. DIRECTORY_SEPARATOR .'HSAItemGateway.php';
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR .'Helpers'. DIRECTORY_SEPARATOR .'CSVParser.php';

class HSATokikoSiteItemsLoader {
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
    private $models = '';
    private $year = '';
    private $body = '';
    
    private $handDirections = array();
    private $brandNumber = '';
    private $types = array();
    private $oems = array();
    private $lineDirections = array();

    private $resultArray = array();

    public static function Create($itemsGateway) {
        $loader = new HSATokikoSiteItemsLoader();
        $loader->gateway = $itemsGateway;
        return $loader;
    }

    public static function UploadFile($filename) {
        $start = date("r");
        $db = DBMySql::Create();
        $db->CreateDatabase();
        //$this->db->StartTransaction();
        $gateway = HSAItemGateway::Create($db);
        //$gateway->CreateTable();
        $fixture = HSATokikoSiteItemsLoader::Create($gateway);
        $fixture->ParseFile($filename);
        $end = date("r");
        
        //echo "started at $start\nended at $end\n";
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
        $line = fgets($this->file, 2048);
        if ($line == false) 
            return false;
        $this->rawLine = $this->cleanLine($line);
        
        //echo $this->rawLine;
        $this->line = $this->parser->ParseLine($this->rawLine);
        if (count($this->line) < 2)
            throw new Exception("Error parsing " . $this->rawLine . " orig = $line");
            
        return true;
    }
    
    private function prepareParse() {
        $this->parser = new CSVParser();
        //$this->gateway->CreateTable();
    }
    
    private function processLine() {
        $this->mark = $this->line[0];
        $this->models = $this->parseModels($this->line[1]);
        $this->body = $this->line[2];
        $this->year = $this->line[3];
        
        $this->parseItems();

        $this->generateItems();
    }

    private function parseModels($cell) {
    	return mb_split(" \/ ", $cell);
    }

    private function parseHandDirections($cell) {
        $result = array();
        if (mb_eregi("^L", $cell))
            $this->handDirections = array(self::LEFT);
        elseif (mb_eregi("^R", $cell))
            $this->handDirections = array(self::RIGHT);
        else
            $this->handDirections = array(self::RIGHT,self::LEFT);
    }

    private function parseOEMs($cell) {
        $cell = trim($cell);
    	if ($cell == '')
    		return array();
        $result = array();
    	$rawArray = mb_split(" ", $cell);
        foreach ($rawArray as $value) {
            if ($value != '' && array_search($value, $result) === FALSE)
                $result[] = $value;
        }
        return $result;
    }

    private function parseItems() {
        $result = array();
        $result["FRONT"] = array("data" => $this->parseTypes($this->line[4],$this->line[5]), 
        	"oems" => $this->parseOEMs($this->line[6]));
        $result["REAR"] = array("data" => $this->parseTypes($this->line[7],$this->line[8]), 
        	"oems" => $this->parseOEMs($this->line[9]));
        $this->resultArray = $result;
    }

    private function parseTypes($oil, $gas) {
    	$oils = array();
    	if ($this->getNumber($oil) != '') {
	    	$this->parseHandDirections($oil);
	    	foreach ($this->handDirections as $handDirection) {
	    		$oils[$handDirection] = $this->getNumber($oil);
	    	}
	    }
	    $gases = array();
    	if ($this->getNumber($gas) != '') {
	    	$this->parseHandDirections($gas);
	    	foreach ($this->handDirections as $handDirection) {
	    		$gases[$handDirection] = $this->getNumber($gas);
	    	}
	    }
	    return array("OIL" => $oils, "GAS" => $gases);
    }

    private function getNumber($cell) {
    	$value = mb_eregi_replace("^[LR][ ]?", "", $cell);
    	return $value;
    }
    
    
    private function generateItems() {
    	foreach ($this->models as $modelName) {
	    	$model = Model::Create(Mark::Create($this->mark), $modelName);
	        foreach ($this->resultArray as $lineDirection => $data) {
	        	foreach ($data['data'] as $type => $handDirections) {
	        		foreach ($handDirections as $handDirection => $brandNumber) {
	        			$item = HSAItem::Create($model, 
			                $this->year, $this->body, 
			                $brandNumber, $data['oems'],
			                $handDirection, $lineDirection, $type, "TOKIKO");
			            $this->gateway->SaveItem($item);
	        		}
	        	}
	        }

        }
    }
}

?>