<?php

/**
 * Description of HSAItemsTestGenerator
 *
 * @author de1mos <de1m0s242@gmail.com>
 */

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR ."DataLayer". DIRECTORY_SEPARATOR ."HSAItemGateway.php";
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR ."DataProviders". DIRECTORY_SEPARATOR ."HSAKYBJapanItemsLoader.php";

class HSAItemsTestGenerator {
    public static function generateFromTestFile($gateway) {
        $loader = HSAKYBJapanItemsLoader::Create($gateway);
        $loader->ParseFile(dirname(dirname(__FILE__))."UnitTests/DataProviders/testLoadKYBJapanOnePage.csv");
    }
    
    public static function generate($gateway) {
        $gateway->CreateTable();
        $item = HSAItem::Create(Model::Create(Mark::Create('HONDA'), "CITY"), 
                    "03∼", "GD6", 
                    '343381', array('52610SAA023'),
                    'RIGHT', 'REAR', 'GAS', "KYB");
    	$gateway->SaveItem($item);
        
        $item = HSAItem::Create(Model::Create(Mark::Create('TOYOTA'), "PASSO"), 
                    "04/06∼", "KGC15(2WD),KGC15(4WD)", 
                    '332120', array('48510B1040'),
                    'RIGHT', 'FRONT', 'GAS', "KYB");
    	$gateway->SaveItem($item);
        
        $item = HSAItem::Create(Model::Create(Mark::Create('TOYOTA'), "STARLET"), 
                    "73/04∼78/01", "KP40/42/45/47/51", 
                    '663028', array('4851010040'),
                    'RIGHT', 'FRONT', 'OIL', "KYB");
        $gateway->SaveItem($item);
        
        $item = HSAItem::Create(Model::Create(Mark::Create('TOYOTA'), "STARLET"), 
                    "73/04∼78/01", "KP40/42/45/47/51", 
                    '663028', array('4851010050'),
                    'RIGHT', 'FRONT', 'OIL', "KYB");
        $gateway->SaveItem($item);
        
        $item = HSAItem::Create(Model::Create(Mark::Create('TOYOTA'), "STARLET"), 
                    "73/04∼78/01", "KP40/42/45/47/51", 
                    '663028', array('4851010040'),
                    'LEFT', 'FRONT', 'OIL', "KYB");
        $gateway->SaveItem($item);
        
        $item = HSAItem::Create(Model::Create(Mark::Create('TOYOTA'), "STARLET"), 
                    "73/04∼78/01", "KP40/42/45/47/51", 
                    '663028', array('4851010050'),
                    'LEFT', 'FRONT', 'OIL', "KYB");
        $gateway->SaveItem($item);
        
        $item = HSAItem::Create(Model::Create(Mark::Create('TOYOTA'), "STARLET"), 
                    "73/04∼78/01", "KP40/42/45/47/51", 
                    '443097', array('4853119106'),
                    'RIGHT', 'REAR', 'OIL', "KYB");
        $gateway->SaveItem($item);
        
        $item = HSAItem::Create(Model::Create(Mark::Create('TOYOTA'), "STARLET"), 
                    "73/04∼78/01", "KP40/42/45/47/51", 
                    '443097', array('4853119108'),
                    'RIGHT', 'REAR', 'OIL', "KYB");
        $gateway->SaveItem($item);
        
        $item = HSAItem::Create(Model::Create(Mark::Create('TOYOTA'), "STARLET"), 
                    "73/04∼78/01", "KP40/42/45/47/51", 
                    '443097', array('4853119106'),
                    'LEFT', 'REAR', 'OIL', "KYB");
        $gateway->SaveItem($item);
        
        $item = HSAItem::Create(Model::Create(Mark::Create('TOYOTA'), "STARLET"), 
                    "73/04∼78/01", "KP40/42/45/47/51", 
                    '443097', array('4853119108'),
                    'LEFT', 'REAR', 'OIL', "KYB");
        $gateway->SaveItem($item);
        
        
        
        $item = HSAItem::Create(Model::Create(Mark::Create('TOYOTA'), "STARLET"), 
                    "73/04∼78/01", "KP40/42/45/47/51", 
                    '553054', array('4853119106'),
                    'RIGHT', 'REAR', 'GAS', "KYB");
        $gateway->SaveItem($item);
        
        $item = HSAItem::Create(Model::Create(Mark::Create('TOYOTA'), "STARLET"), 
                    "73/04∼78/01", "KP40/42/45/47/51", 
                    '553054', array('4853119108'),
                    'RIGHT', 'REAR', 'GAS', "KYB");
        $gateway->SaveItem($item);
        
        $item = HSAItem::Create(Model::Create(Mark::Create('TOYOTA'), "STARLET"), 
                    "73/04∼78/01", "KP40/42/45/47/51", 
                    '553054', array('4853119106'),
                    'LEFT', 'REAR', 'GAS', "KYB");
        $gateway->SaveItem($item);
        
        $item = HSAItem::Create(Model::Create(Mark::Create('TOYOTA'), "STARLET"), 
                    "73/04∼78/01", "KP40/42/45/47/51", 
                    '553054', array('4853119108'),
                    'LEFT', 'REAR', 'GAS', "KYB");
        $gateway->SaveItem($item);
    }
}

?>
