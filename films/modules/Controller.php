<?php

namespace Films\Modules;

class Controller
{

    public $query;

    public $page;

    public $uri;

    public $post;
    public $get;

    public $files;

    public function __construct($query = null)
    {
        $this->query = $query;
        if (static::isPost())
        $this->post = (object)$_POST;
        if (count($_GET) > 0)
        $this->get = (object)$_GET;
        $this->files = $_FILES;
        $this->page = $_GET['page'] ?? 1;
    }

    /*метод принимает на вход массив-содержимое файла routes.php
    возвращает массив параметров запроса, конвертировав их в ЧПУ исходя из
    названия контроллера, действия и необходимых им параметров*/

    public static function getRequestParams($routes) {
        $url = parse_url("http://".$_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI']);

        $request = preg_split("/[\/]+/", $url['path']);
        unset($request[0]);

        for ($i=1; $i<(count($request)); $i++) {
            $request[$i]=urldecode($request[$i]);
        }

        preg_match("/([a-zA-Z]+)-([a-zA-Z]+)/", $request[1], $controller_action);

        $controller_request = $controller_action[1] ?? '';
        $action_request = $controller_action[2] ?? '';
        $request_params = [];

        isset($url['query']) && parse_str($url['query'], $_GET);

        if(array_key_exists($controller_request, $routes)) {
            if(array_key_exists($action_request, $routes[$controller_request])) {
                if(count($routes[$controller_request][$action_request]) == count($request) - 1){
                    $request_params['controller'] = $controller_action[1];
                    $request_params['action'] = $controller_action[2];
                    $i = 2;
                    foreach ($routes[$controller_request][$action_request] as $param) {
                        $request_params['query'][$param] = $request[$i];
                        $i++;
                    }
                    if(count($request) > 1)
                    $request_params['query'] = (object)$request_params['query'] ?? null;
                }
            }
        }
        return $request_params;
    }

    public static function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function isGet() {
        return 'GET' === $_SERVER['REQUEST_METHOD'];
    }

    public static function isPost() {
        return 'POST' === $_SERVER['REQUEST_METHOD'];
    }

    /*преобразует айди контроллера в название класса (film->FilmController)*/

    public static function getClassName($controller_id) {
        if ($controller_id === '')
            return 'Films\\Controllers\\' . DEFAULT_CONTROLLER;
        return 'Films\\Controllers\\' . strtoupper($controller_id[0]) . substr($controller_id, 1) . 'Controller';
    }

    public static function getPost() {
        if(!empty($_POST)) {
            return (object)$_POST;
        }
        return null;
    }

    public static function showMessage($msg) {
        $_SESSION['msg'] = $msg;
    }

    public static function redirectTo($url, $params = []) {
        foreach ($params as $key => $value) {
            $_SESSION[$key] = $value;
        }
        $_SESSION['redirect'] = true;
        header('Location: ' . $url);
    }

    /*очищает сессию от ненужных значений после перенаправления*/

    public static function sessionClean($keys) {
        if(@$_SESSION['redirect']) {
            unset($_SESSION['redirect']);
        }else {
            foreach ($keys as $key) {
                unset($_SESSION[$key]);
            }
        }
    }
}