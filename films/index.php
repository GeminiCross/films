<?php
session_start();
require 'config/app.php';
require 'autoload.php';

use Films\Modules\Controller;
$routes = require 'routes.php';
$params = Controller::getRequestParams($routes);
$className = Controller::getClassName($params['controller'] ?? '');
$controller = new $className($params['query'] ?? []);
$action = $params['action'] ?? DEFAULT_ACTION;
$controller->$action();
$controller->afterAction();