<?php 
require_once "authorization.php";
if (!Authorization::IsAuthorized()) {
    Authorization::ShowAuthorization();
}
else {
    try {
        //echo phpinfo();
        include(dirname(__FILE__). DIRECTORY_SEPARATOR . "Helpers" . DIRECTORY_SEPARATOR . "startup.php"); 
        require_once site_path."DB".DIRSEP."DBMySql.php";
        require_once site_path."DataLayer".DIRSEP."HSAProductGateway.php";
        require_once site_path."DataLayer".DIRSEP."HSAItemGateway.php";
        $registry = new Registry;
        
        $template = new Template($registry);
        $registry->set ('template', $template);
        $db = DBMySql::Create();
        $registry->set ('db', $db);
        $productGateway = HSAProductGateway::Create($db);
        $itemGateway = HSAItemGateway::Create($db);
        $registry->set('productGateway', $productGateway);
        $registry->set('itemGateway', $itemGateway);
        
        foreach ($_REQUEST as $key => $value) {
            $registry->set ('REQUEST_'.$key, $value);
            //echo $registry->get('REQUEST_'.$key)."|$key|$value--- \r\n";
        }

        $router = new Router($registry);
        $registry->set ('router', $router);
        $requestType = "HTML";
        if (isset($_REQUEST['requestType']))
            $requestType = $_REQUEST['requestType'];
        $registry->set ('requestType', $requestType);
        $router->setControllersPath (site_path . 'Web' . DIRSEP . 'Controllers');
        $router->setViewsPath (site_path . 'Web' . DIRSEP . 'Views');
        $router->setJSPath (site_path . 'Web' . DIRSEP . 'js');
        $router->delegate();
    }
    catch(Exception $ex) {
        echo $ex->getMessage();
    }
}
?>