<?php

/**
 * Description of Product
 *
 * @author de1mos <de1m0s242@gmail.com>
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controller_Base.php';
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'Entities'. DIRECTORY_SEPARATOR .'HSAItem.php';
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR .'DataLayer'. DIRECTORY_SEPARATOR .'HSAItemGateway.php';
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR .'DataLayer'. DIRECTORY_SEPARATOR .'MarkGateway.php';
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR .'DataLayer'. DIRECTORY_SEPARATOR .'ModelGateway.php';
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR .'DataProviders'. DIRECTORY_SEPARATOR .'HSAItemsTestGenerator.php';
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR .'DataProviders'. DIRECTORY_SEPARATOR .'HSAKYBSiteItemsLoader.php';
require_once dirname(dirname(__FILE__)). DIRECTORY_SEPARATOR . "Helpers" . DIRECTORY_SEPARATOR . "Item" . DIRECTORY_SEPARATOR . "ItemsTable.php";

class Controller_Item extends Controller_Base {
    private $itemGateway;
    private $markGateway;
    function __construct($registry) {
        parent::__construct($registry);
        $this->itemGateway = $registry->get('itemGateway');
        $this->markGateway = MarkGateway::Create($registry->get('db'));
    }

    function index() {
        $items = array();
        try {
            $dbResult = $this->itemGateway->FindPageItems(0,10);
            while (($item = $this->itemGateway->Fetch($dbResult)) != NULL) {
                $items[] = $item;
            }
        }
        catch (Exception $ex) {
            $this->registry->set("page_error", $ex->getMessage());
        }
        
        $this->registry->set("content", $items);
        
        $this->registry->set("view", "index");
    }
    
    function generate() {
        HSAItemsTestGenerator::generateFromTestFile($this->itemGateway);
        
        $this->index();
    }
    
    function upload() {
        /*if(is_uploaded_file($_FILES["filename"]["tmp_name"]))
        {
            $this->itemGateway->CreateTable();
            $loader = HSAKYBSiteItemsLoader::Create($this->itemGateway);
            $loader->ParseFile($_FILES["filename"]["tmp_name"]);
        } else {
            $this->registry->set("upload_error", "Ошибка загрузки файла");
        }*/
        $this->index();
    }
    
    function search() {
        $marks = array();
        try {
            $dbResult = $this->markGateway->FindAllMarksOrderName();
            while (($mark = $this->markGateway->Fetch($dbResult)) != NULL) {
                $marks[] = $mark;
            }
        }
        catch (Exception $ex) {
            $this->registry->set("page_error", $ex->getMessage());
        }
        
        $items = array();
        try {
            $dbResult = $this->itemGateway->FindPageItems(0,10);
            while (($item = $this->itemGateway->Fetch($dbResult)) != NULL) {
                $items[] = $item;
            }
        }
        catch (Exception $ex) {
            $this->registry->set("page_error", $ex->getMessage());
        }
        
        $this->registry->set("content", $items);
        
        $this->registry->set("contentMark", $marks);
        $this->registry->set("view", "search");
    }
    
    function searchByMark() {
        $modelGateway = ModelGateway::Create($this->registry->get('db'));
        $models = array();
        $markName = $this->registry->get('REQUEST_selectedMark');
        if ($markName != 'empty') {
            $dbResult = $modelGateway->FindAllModelsByMarkNameOrderName($markName);
            while (($model = $modelGateway->Fetch($dbResult)) != NULL) {
                $models[] = $model->NameGet();
            }
        }
        
        $items = array();
        try {
            $dbResult = $this->itemGateway->FindItemsByMarkName($markName);
            while (($item = $this->itemGateway->Fetch($dbResult)) != NULL) {
                $items[] = $item;
            }
        }
        catch (Exception $ex) {
            $this->registry->set("page_error", $ex->getMessage());
        }
        
        $itemsTable = ItemsTable::GetTable($items);
        $result = array('models'=>$models,'items'=>$itemsTable);
        $this->registry->set("content", $result);
    }
    
    function searchByFields() {
        $markName = $this->registry->get('REQUEST_selectedMark');
        $modelName = $this->registry->get('REQUEST_selectedModel');
        $body = $this->registry->get('REQUEST_selectedBody');
        $year = $this->registry->get('REQUEST_selectedYear');
        $page = $this->registry->get('REQUEST_currentPage');
        $items = array();
        $dbResult = $this->itemGateway->FindByMarkModelBodyYear($markName,$modelName,$body,$year,$page-1,20);
        while (($item = $this->itemGateway->Fetch($dbResult)) != NULL) {
            $items[] = $item;
        }
        $itemsTable = ItemsTable::GetRows($items);
        
        $result = array('items'=>$itemsTable);
        $this->registry->set("content", $result);
    }

    function searchYearsAndBodies() {
        $markName = $this->registry->get('REQUEST_selectedMark');
        $modelName = $this->registry->get('REQUEST_selectedModel');
        $years = array();
        $dbResult = $this->itemGateway->FindYearsByMarkAndModelNames($markName,$modelName);
        while (($year = $this->itemGateway->FetchColumn($dbResult)) != NULL) {
            $years[] = $year;
        }

        $bodies = array();
        $dbResult = $this->itemGateway->FindBodiesByMarkAndModelNames($markName,$modelName);
        while (($body = $this->itemGateway->FetchColumn($dbResult)) != NULL) {
            $bodies[] = $body;
        }
        
        $result = array('years'=>$years, 'bodies'=>$bodies);
        $this->registry->set("content", $result);
    }
}

?>
