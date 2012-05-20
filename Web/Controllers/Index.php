<?php

/**
 * Description of Index
 *
 * @author de1mos <de1m0s242@gmail.com>
 */

require_once 'Controller_Base.php';

class Controller_Index extends Controller_Base {
    function index() {
        echo 'Hello from my MVC system';
        
        $this->registry->set("content", "<h3>INCLUDE!!!!</h3>");
        
        $this->registry->set("view", "index");
    }
}

?>
