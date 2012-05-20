<?php


/**
 * Description of HSAProductGatewayTest
 *
 * @author de1mos <de1m0s242@gmail.com>
 */

require_once 'PHPUnit/Autoload.php';
require_once 'DataLayer/MarkGateway.php';

class MarkGatewayTest extends PHPUnit_Framework_TestCase {
    
    private $fixture = NULL;
    
    private $db = NULL;
    
    protected function setUp() {
        $this->db = DBMySql::Create();
        $this->db->CreateDatabase();
        $this->db->StartTransaction();
        $this->fixture = MarkGateway::Create($this->db);
        $this->fixture->CreateTable();
    }
    
    protected function tearDown() {
        $this->db->RollbackTransaction();
    }
    
    public function testCRUD() {
    	$name = "TOYOTA";
    	$mark = Mark::Create($name);
    	
    	$this->fixture->SaveMark($mark);
    	
    	$findedMark = $this->fixture->GetMarkByName($name);
		$this->AssertNotNull($findedMark);
		$this->AssertEquals($name, $findedMark->NameGet());
            
        $this->fixture->DeleteMark($findedMark);
        $findedMark = $this->fixture->GetMarkByName($name);
        $this->AssertNull($findedMark);
    }
    
    public function testFindLimitMarks() {
        $count = 2;
        
        $name1 = "5";
        $mark = Mark::Create($name1);
        $this->fixture->SaveMark($mark);
        
        $name2 = "6";
        $mark = Mark::Create($name2);
        $this->fixture->SaveMark($mark);
                
        $name3 = "7";
        $mark = Mark::Create($name3);
        $this->fixture->SaveMark($mark);
                
        $dbresult = $this->fixture->FindPageMarks(0, $count);
        for ($i=1;$i <= $count;$i++) {
            $mark = $this->fixture->Fetch($dbresult);
            $this->AssertNotNull($mark);
            $nameName = "name".$i;
            $this->AssertEquals($$nameName, $mark->NameGet());
        }
    }
    
    public function testFindAllMarks() {
        $count = 3;
        
        $name1 = "c";
        $mark = Mark::Create($name1);
        $this->fixture->SaveMark($mark);
        
        $name2 = "b";
        $mark = Mark::Create($name2);
        $this->fixture->SaveMark($mark);
                
        $name3 = "a";
        $mark = Mark::Create($name3);
        $this->fixture->SaveMark($mark);
                
        $dbresult = $this->fixture->FindAllMarksOrderName();
        for ($i=$count;$i >0 ;$i--) {
            $mark = $this->fixture->Fetch($dbresult);
            $this->AssertNotNull($mark);
            $nameName = "name".$i;
            $this->AssertEquals($$nameName, $mark->NameGet());
        }
    }
}

?>
