<?php

// Automatically load classes from the /classes directory
spl_autoload_register(function ($className) {
    $baseDir = __DIR__ . '/../classes/';
    $file = $baseDir . $className . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
