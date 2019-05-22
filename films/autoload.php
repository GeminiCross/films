<?php

/*скрипт, регистрирующий классы исходя из неймспейсов*/

spl_autoload_register(function ($class) {

    $prefix = 'Films\\';

    $base_dir = __DIR__ . '/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = lcfirst(substr($class, $len));

    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    echo $file;die;
    if (file_exists($file)) {
        require $file;
    }
});