<?php

namespace Films\Modules;

class View
{
    public $data;

    public $view;

    public $template;

    public $content;

    public $pagination;

    public $message = '';

    public $form_data = [];

    public function __construct($view, $data = null)
    {
        $this->template = substr($view, 0, strpos($view, '/'));
        $this->view = $view;
        $this->data = $data;
        $this->form_data = $_SESSION['form_data'] ?? [];
        $this->message = $_SESSION['message'] ?? '';
    }

    /*создаёт ссылку на текущий запрос, меняя страницу либо добавляя её к запросу, если она не была задана*/

    public static function linkToPage($page) {
        $uri = $_SERVER['REQUEST_URI'];
        $query = parse_url($uri, PHP_URL_QUERY);
        $path = parse_url($uri, PHP_URL_PATH);
        parse_str($query, $params);
        unset($params['page']);
        $params['page'] = $page;
        return $path . '?' . http_build_query($params);
    }

    /*добавляет пагинацию в представление*/

    public function makePagination($total_items, $current_page, $for) {
        $params = PAGINATION_PARAMS[$for];
        $total_pages = ceil($total_items/$params['per_page']);
        $max_right = $params['max_right'];
        $max_left = $params['max_left'];
        if($current_page - 1 < $max_left){
            $first_page = 1;
        }else{
            $first_page = $current_page - $max_left;
        }
        if($current_page < $total_pages - $max_right) {
            $last_page = $current_page + $max_right;
        }else{
            $last_page = $total_pages;
        }
        ob_start();
        require 'views/templates/pagination.php';
        $this->pagination = ob_get_clean();
    }

    public function render() {
        if ($this->data !== null) {
            foreach ($this->data as $key => $value) {
                $$key = $value;
            }
        }
        ob_start();
        require 'views/' . $this->view . '.php';
        $this->content = ob_get_clean();
        require 'views/templates/' . $this->template . '.php';
    }

    /*
    создаёт ссылку.
    первые 2 параметра $params это контроллер и действие в формате /film-view/
    остальные - необходимые для выполнения действя в формате /id/
    $query - массив данных для создания запроса формата ?id=1
    */

    public static function makeUrl($params = null, $query = null) {
        if (is_null($params)) {
            return '/';
        }elseif (count($params) < 3) {
            $url = '/' . $params['controller'] . '-' . $params['action'];
            if(!is_null($query)) {
                $url .= '?' . http_build_query($query);
            }
            return $url;
        }else{
            $url = '/' . $params['controller'] . '-' . $params['action'] . '/';
            unset($params['controller']);
            unset($params['action']);
            $url.= implode('/' ,$params);
            if(!is_null($query)) {
                $url .= '?' . http_build_query($query);
            }
            return $url;
        }
    }
}