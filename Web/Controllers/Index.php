<?php

/**
 * Description of Index
 *
 * @author de1mos <de1m0s242@gmail.com>
 */

require_once 'Controller_Base.php';

class Controller_Index extends Controller_Base {
    function index() {
        $this->registry->set("content", "Выберите в меню нужный пункт");
        
        $this->registry->set("view", "index");
    }
}

?>
