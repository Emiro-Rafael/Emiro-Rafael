<?php
spl_autoload_register(
    function($className) {
        $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
        $file = __DIR__ . DIRECTORY_SEPARATOR . $className . '.php';
        if (file_exists($file)) {
            include_once $file;
        }
    }
);