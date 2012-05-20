<?php

/**
 * Description of Controller_Base
 *
 * @author de1mos <de1m0s242@gmail.com>
 */
abstract class Controller_Base {
    protected $registry;

    function __construct($registry) {
        $this->registry = $registry;
    }

    abstract function index();
}

?>
