<?php

error_reporting (E_ALL);

if (version_compare(phpversion(), '5.1.0', '<') == true) { throw new Exception ('PHP5.1 Only'); }


// Константы:

define ('DIRSEP', DIRECTORY_SEPARATOR);


// Узнаём путь до файлов сайта

$site_path = realpath(dirname(__FILE__) . DIRSEP . '..' . DIRSEP . '..' . DIRSEP) . DIRSEP;

define ('site_path', $site_path);

function __autoload($class_name) {
    $filename = $class_name . '.php';
    $file = site_path . 'Web' . DIRSEP . 'Helpers' . DIRSEP . $filename;

    if (file_exists($file) == false) {
            return false;
    }
    include ($file);
}

?>