<?php

defined('PROTECTED_PATH') || define('PROTECTED_PATH', realpath(__DIR__ . '/../'));

//set include path and setup default autoload
set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, [
        PROTECTED_PATH . '/libs',
        PROTECTED_PATH . '/project',
    ]));

//fix autoload for unit tests:
spl_autoload_register(function ($className) {
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    $filePath = $className . '.php';

    @include_once $filePath;
});
