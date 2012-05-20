<?php

/**
 * Description of Router
 *
 * @author de1mos <de1m0s242@gmail.com>
 */
class Router {
    private $registry;

    private $controllers_path;
    
    private $views_path;

    private $js_path;

    private $args = array();


    function __construct($registry) {
            $this->registry = $registry;
    }
    
    function setControllersPath($path) {
        //$path = trim($path, '/\\');
        $path .= DIRSEP;
        if (is_dir($path) == false) {
                throw new Exception ('Invalid controller path: `' . $path . '`');
        }
        $this->controllers_path = $path;
    }
    
    function setViewsPath($path) {
        //$path = trim($path, '/\\');
        $path .= DIRSEP;
        if (is_dir($path) == false) {
                throw new Exception ('Invalid views path: `' . $path . '`');
        }
        $this->views_path = $path;
    }

    function setJSPath($path) {
        $path .= DIRSEP;
        if (is_dir($path) == false) {
                throw new Exception ('Invalid js path: `' . $path . '`');
        }
        $this->js_path = $path;
    }
    
    function delegate() {
        $this->getController($file, $controller, $action, $args);
        if (is_readable($file) == false) {
            throw new Exception ('404 Not Found');
        }
        
        include ($file);
        
        $class = 'Controller_' . $controller;
        $controllerInstance = new $class($this->registry);

        if (is_callable(array($controllerInstance, $action)) == false) {
            throw new Exception ('404 Not Found: '.$action.' in '.$class);
        }
        $controllerInstance->$action();
        
        $view = $this->views_path.$controller.DIRSEP.$this->registry->get("view").".php";
        $this->registry->set("showFile", $view);

        $js_file = $this->js_path.$controller.DIRSEP.$this->registry->get("view").".js";
        if (is_file($js_file))
            $this->registry->set("scriptFile", '/js/'.$controller.'/'.$this->registry->get("view").".js");

        $registry = $this->registry;
        include $this->views_path.DIRSEP."baseView.php";
    }
    
    private function getController(&$file, &$controller, &$action, &$args) {
        $route = (empty($_REQUEST['route'])) ? '' : $_REQUEST['route'];
        if (empty($route)) { $route = 'index'; }
        
        $route = trim($route, '/\\');
        $parts = explode('/', $route);
        
        $cmd_path = $this->controllers_path;
        foreach ($parts as $part) {
            $fullpath = $cmd_path . $part;
        
            if (is_dir($fullpath)) {
                $cmd_path .= $part . DIRSEP;
                array_shift($parts);
                continue;
            }

            if (is_file($fullpath . '.php')) {
                $controller = $part;
                array_shift($parts);
                break;
            }
        }

        if (empty($controller)) { $controller = 'Product'; };

        $action = array_shift($parts);
        if (empty($action)) { $action = 'index'; }

        $file = $cmd_path . $controller . '.php';
        $args = $parts;
    }
}

?>
