<?php

/**
 * Description of HSAKYBJapanItemsLoaderTest
 *
 * @author de1mos <de1m0s242@gmail.com>
 */

require_once 'PHPUnit/Autoload.php';
require_once 'DataProviders/HSATokikoSiteItemsLoader.php';

class HSATokikoSiteLoaderTest extends PHPUnit_Framework_TestCase {
    private $fixture;
    
    private $db = NULL;
    
    private $gateway;
    
    protected function setUp() {
        $this->db = DBMySql::Create();
        $this->db->CreateDatabase();
        $this->db->StartTransaction();
        $this->gateway = HSAItemGateway::Create($this->db);
        $this->gateway->CreateTable();
        $this->fixture = HSATokikoSiteItemsLoader::Create($this->gateway);
    }
    
    protected function tearDown() {
        $this->db->RollbackTransaction();
    }
    
    /** 
    * @dataProvider SimpleStreamContent 
    */
    public function testLoadSimpleStream($markName, $modelName,$body,$year,$brandNumber,$type,
            $lineDirection, $handDirection, $oems = array()) {
        $mark = Mark::Create($markName);
        $model = Model::Create($mark, $modelName);
        $item = HSAItem::Create($model, $year, $body, 
                    $brandNumber, array(),
                    $handDirection, $lineDirection, $type, "TOKICO");
        
        $this->fixture->ParseFile(dirname(__FILE__)."/TokikoSiteSmall.csv");
        
        $itemId = $this->gateway->FindItem($item);
        $this->AssertNotNull($itemId, "item no found");
        $findedItem = $this->gateway->GetItemById($itemId);

        $this->AssertNotNull($findedItem);
        $this->AssertEquals($markName, $findedItem->ModelGet()->MarkGet()->NameGet());
        $this->AssertEquals($modelName, $findedItem->ModelGet()->NameGet());
        $this->AssertEquals($year, $findedItem->YearGet());
        $this->AssertEquals($body, $findedItem->BodyGet());
        $this->AssertEquals($brandNumber, $findedItem->BrandNumberGet());
        $this->AssertEquals($handDirection, $findedItem->HandDirectionGet());
        $this->AssertEquals($lineDirection, $findedItem->LineDirectionGet());
        $this->AssertEquals($type, $item->TypeGet());

        $itemOems = $findedItem->OEMNumbersGet();
        foreach ($oems as $value) {
            $res = array_search($value, $itemOems);
            $this->AssertTrue($res !== FALSE, "oem not found $value");
            
        }
    }

    
    
    public function SimpleStreamContent()
    {
        return array(
            //DAIHATSU,CHARADE,"G11, V, G30, V(Incl. Van,Excl.TX, TS,CX,Turbo)",83/01~86/12,"R A1052","R B1052",48510-87715,1165,E1165,"48530-87710 48530-87712 48530-87713"
            array("DAIHATSU","CHARADE","G11, V, G30, V(Incl. Van,Excl.TX, TS,CX,Turbo)","83/01~86/12",'A1052',"OIL","FRONT","RIGHT", array('48510-87715')),
            array("DAIHATSU","CHARADE","G11, V, G30, V(Incl. Van,Excl.TX, TS,CX,Turbo)","83/01~86/12",'1165',"OIL","REAR","LEFT", array('48530-87710', '48530-87712', '48530-87713')),
            array("DAIHATSU","CHARADE","G11, V, G30, V(Incl. Van,Excl.TX, TS,CX,Turbo)","83/01~86/12",'1165',"OIL","REAR","RIGHT", array('48530-87710', '48530-87712', '48530-87713')),
            array("DAIHATSU","CHARADE","G11, V, G30, V(Incl. Van,Excl.TX, TS,CX,Turbo)","83/01~86/12",'E1165',"GAS","REAR","LEFT", array('48530-87710', '48530-87712', '48530-87713')),
            array("DAIHATSU","CHARADE","G11, V, G30, V(Incl. Van,Excl.TX, TS,CX,Turbo)","83/01~86/12",'E1165',"GAS","REAR","RIGHT", array('48530-87710', '48530-87712', '48530-87713')),

            //NISSAN,"SUNNY TRAVELLER / AD WAGON / VAN / WINGROAD","Y11 2WD ABS Type only",99/05~04/04,L,"L B2248","54303-WD285 54303-WD287",,,"56210-WD225 56210-WD226 56210-WD227"
            array('NISSAN','SUNNY TRAVELLER','Y11 2WD ABS Type only','99/05~04/04','B2248','GAS','FRONT','LEFT',array('54303-WD285', '54303-WD287')),
            array('NISSAN','AD WAGON','Y11 2WD ABS Type only','99/05~04/04','B2248','GAS','FRONT','LEFT',array('54303-WD285', '54303-WD287')),
            array('NISSAN','WINGROAD','Y11 2WD ABS Type only','99/05~04/04','B2248','GAS','FRONT','LEFT',array('54303-WD285', '54303-WD287')),

            //TOYOTA,"KIJANG INNOVA",,04~,R,"R U3773",48510-0K080,,E3796,48531-0K210
            array('TOYOTA','KIJANG INNOVA','','04~','U3773','GAS','FRONT','RIGHT',array('48510-0K080')),
            array('TOYOTA','KIJANG INNOVA','','04~','E3796','GAS','REAR','RIGHT',array('48531-0K210')),
            array('TOYOTA','KIJANG INNOVA','','04~','E3796','GAS','REAR','LEFT',array('48531-0K210')),

            //DAIHATSU,APPLAUSE,A101,89/06~97/09,"R A1070","R B1070","48510-87106 48510-87114 48510-87118 48510-87111 48510-87117",,,
            //DAIHATSU,APPLAUSE,A101,89/06~97/09,"R A1070","R B1070","48510-87104 48510-87108     48510-87105 48510-87109",,,
            array("DAIHATSU","APPLAUSE","A101","89/06~97/09",'A1070',"OIL","FRONT","RIGHT", array('48510-87106', '48510-87114', '48510-87118', '48510-87111', '48510-87117', '48510-87104', '48510-87108', '48510-87105', '48510-87109')),
        ); 
        
    }
}

?>
