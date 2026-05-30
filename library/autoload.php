<?php

error_reporting(E_ALL & ~E_WARNING);

spl_autoload_extensions('.php');
spl_autoload_register(function (string $class) {
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        error_log("Autoload failed: File not found at " . $path);
    }
});
