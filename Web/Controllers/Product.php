<?php

/**
 * Description of Product
 *
 * @author de1mos <de1m0s242@gmail.com>
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controller_Base.php';
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'Entities'. DIRECTORY_SEPARATOR .'HSAProduct.php';
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR .'DataLayer'. DIRECTORY_SEPARATOR .'HSAProductGateway.php';
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR .'DataProviders'. DIRECTORY_SEPARATOR .'HSAProductsLoader.php';
class Controller_Product extends Controller_Base {
    private $productGateway;
    function __construct($registry) {
        parent::__construct($registry);
        $this->productGateway = $registry->get('productGateway');
    }

    function index() {
        $products = array();
        try {
            $dbResult = $this->productGateway->FindPageProducts(0,10);
            while (($product = $this->productGateway->Fetch($dbResult)) != NULL) {
                $products[] = $product;
            }
        }
        catch (Exception $ex) {
            $this->registry->set("page_error", $ex->getMessage());
        }
        
        $this->registry->set("content", $products);
        
        $this->registry->set("view", "index");
    }
    
    function upload() {
        //$this->registry->set("upload_error", $_FILES['userfile']['name'].' '.$_FILES['userfile']['tmp_name']);
        if(is_uploaded_file($_FILES["filename"]["tmp_name"]))
        {
            $path = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "upload" . DIRECTORY_SEPARATOR;
            //if (!move_uploaded_file($_FILES["filename"]["tmp_name"], $path.$_FILES["filename"]["name"]))
              //  $this->registry->set("upload_error", "Ошибка загрузки файла");
            set_time_limit(3*60*60);//3 hours to parse
            $loader = HSAProductsLoader::Create($this->productGateway);
            $loader->ParseFile($_FILES["filename"]["tmp_name"]);
        } else {
            $this->registry->set("upload_error", "Ошибка загрузки файла");
        }
        $this->index();
    }
    
    function generate() {
        $this->productGateway->CreateTable();
        $hsaId1 = "5";
        $type1 = "type1";
        $price1 = 4.33;
        $amount1 = "little";
        $description1 = "trololo";
        echo "product create";
        $product = HSAProduct::Create($hsaId1, $type1, $price1, $amount1, $description1);
        echo "save";
        $this->productGateway->SaveProduct($product);
        echo "after save";
        $hsaId2 = "6";
        $type2 = "type2";
        $price2 = 5.33;
        $amount2 = "no";
        $description2 = "trololo2";
        $product = HSAProduct::Create($hsaId2, $type2, $price2, $amount2, $description2);
        
        $this->productGateway->SaveProduct($product);
        
        $hsaId3 = "7";
        $type3 = "type3";
        $price3 = 5.32;
        $amount3 = "many";
        $description3 = "trololo3";
        $product = HSAProduct::Create($hsaId3, $type3, $price3, $amount3, $description3);
        
        $this->productGateway->SaveProduct($product);
        
        $this->index();
    }
    
}

?>
