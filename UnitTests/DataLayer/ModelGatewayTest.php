<?php


/**
 * Description of HSAProductGatewayTest
 *
 * @author de1mos <de1m0s242@gmail.com>
 */

require_once 'PHPUnit/Autoload.php';
require_once 'DataLayer/ModelGateway.php';

class ModelGatewayTest extends PHPUnit_Framework_TestCase {
    
    private $fixture = NULL;
    
    private $db = NULL;
    
    protected function setUp() {
        $this->db = DBMySql::Create();
        $this->db->CreateDatabase();
        $this->db->StartTransaction();
        $this->fixture = ModelGateway::Create($this->db);
        $this->fixture->CreateTable();
    }
    
    protected function tearDown() {
        $this->db->RollbackTransaction();
    }
    
    public function testCRUD() {
    	$markName = "TOYOTA";
    	$modelName = "CELICA";
    	$mark = Mark::Create($markName);
		$model = Model::Create($mark, $modelName);
    	
    	$this->fixture->SaveModel($model);
    	
    	$findedModel = $this->fixture->GetModelByMarkAndModelNames($markName, $modelName);
		$this->AssertNotNull($findedModel);
		$this->AssertEquals($modelName, $findedModel->NameGet());
		$this->AssertEquals($markName, $findedModel->MarkGet()->NameGet());
            
        $this->fixture->DeleteModel($findedModel);
        $findedModel = $this->fixture->GetModelByMarkAndModelNames($markName, $modelName);
        $this->AssertNull($findedModel);
    }
    
    public function testFindLimitModels() {
        $count = 2;
        
        $markName1 = "a1";
        $name1 = "b1";
        $mark = Mark::Create($markName1);
        $model = Model::Create($mark, $name1);
        $this->fixture->SaveModel($model);
        
        $markName2 = "a2";
        $name2 = "b2";
        $mark = Mark::Create($markName2);
        $model = Model::Create($mark, $name2);
        $this->fixture->SaveModel($model);
                
        $markName3 = "a3";
        $name3 = "b3";
        $mark = Mark::Create($markName3);
        $model = Model::Create($mark, $name3);
        $this->fixture->SaveModel($model);
                
        $dbresult = $this->fixture->FindPageModels(0, $count);
        for ($i=1;$i <= $count;$i++) {
            $model = $this->fixture->Fetch($dbresult);
            $this->AssertNotNull($model);
            $modelName = "name".$i;
            $markName = "markName".$i;
            $this->AssertEquals($$modelName, $model->NameGet());
            $this->AssertEquals($$markName, $model->MarkGet()->NameGet());
        }
    }
    
    public function testFindAllModelsByMarkNameOrderName() {
        $count = 2;
        
        $markName1 = "a1";
        $name1 = "b1";
        $mark = Mark::Create($markName1);
        $model = Model::Create($mark, $name1);
        $this->fixture->SaveModel($model);
        
        $markName2 = "a3";
        $name2 = "b2";
        $mark = Mark::Create($markName2);
        $model = Model::Create($mark, $name2);
        $this->fixture->SaveModel($model);
                
        $markName3 = "a3";
        $name3 = "b3";
        $mark = Mark::Create($markName3);
        $model = Model::Create($mark, $name3);
        $this->fixture->SaveModel($model);
                
        $dbresult = $this->fixture->FindAllModelsByMarkNameOrderName("a3");
        for ($i=2;$i <= 3;$i++) {
            $model = $this->fixture->Fetch($dbresult);
            $this->AssertNotNull($model);
            $modelName = "name".$i;
            $markName = "markName".$i;
            $this->AssertEquals($$modelName, $model->NameGet());
            $this->AssertEquals($$markName, $model->MarkGet()->NameGet());
        }
    }
}

?>
