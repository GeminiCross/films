<?php

namespace Films\Controllers;

use Films\Modules\Controller;
use Films\Models\Film;
use Films\Modules\View;

class FilmController extends Controller
{
    public function index() {
        $films_count = Film::count('films');
        if ($films_count > 0) {
            $total_pages = Film::getTotalPages($films_count);
            if ($total_pages < $this->page) {
                static::redirectTo(View::linkToPage($total_pages));
            }elseif($this->page < 1){
                static::redirectTo(View::linkToPage(1));
            }
            $films = Film::loadFilms('', [], 'name', $this->page);
            $view = new View('film/index', ['films' => $films]);
            if ($films_count > PAGINATION_PARAMS['films']['per_page'])
                $view->makePagination($films_count, $this->page, 'films');
        }else{
            $view = new View('film/zero_result');
        }
        $view->render();
    }

    public function create() {
        $view = new View('film/create');
        if(static::isPost()) {
            if (isset($this->files['films'])){
                $result = Film::saveFilmsFromJson($this->files['films']);
                Controller::redirectTo(View::makeUrl(['controller' => 'film', 'action' => 'create']), ['message' => $result['message']]);
            }else {
                $validation = Film::validate($this->post);
                if ($validation['success']) {
                    $film = new Film($this->post);
                    if ($film->save())
                        Controller::redirectTo(View::makeUrl(['controller' => 'film', 'action' => 'view', 'id' => $film->id]));
                } else {
                    Controller::redirectTo(View::makeUrl(['controller' => 'film', 'action' => 'create']), ['message' => $validation['incorrect_fields'], 'form_data' => (array)$this->post]);
                    $view->message .= $validation['incorrect_fields'];
                }
            }
        }
        $formats = Film::getAllFormats();
        $view->data['formats'] = $formats;
        $view->render();
    }

    public function view() {
        if (isset($this->query->id)){
            $film = Film::loadOne('id = :id' ,[':id' => $this->query->id]);
            if(!is_null($film)){
                $view = new View('film/view', ['film' => $film]);
                $view->render();
            }
            else {
                static::redirectTo('/');
            }
        }
    }

    public function getExampleFile() {
        $formats = Film::getAllFormats();
        $films_quantity = $_GET['q'] == '' ? 1 : $_GET['q'];
        header('Content-disposition: attachment; filename=films.txt');
        header('Content-type: text/plain');
        echo file_get_contents('downloads/example.txt') . "\r\n#Доступные форматы: " . implode(', ', $formats) . "\r\n";
        for ($i = 1; $i <= $films_quantity; $i++) {
            echo $i . '->' . file_get_contents('downloads/example.json');
        }
    }

    public function delete() {
        if (isset($this->query->id)) {
            if(Film::delete($this->query->id)) {
                static::redirectTo($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function search() {
        if(isset($this->get->by) && isset($this->get->name)) {
            switch ($this->get->by) {
                case 'name' :
                    $films_count = Film::count('films', ' name LIKE :name ', [':name' => '%' . $this->get->name . '%']);
                    if ($films_count > 0) {
                        $total_pages = Film::getTotalPages($films_count);
                        if ($total_pages < $this->page) {
                            static::redirectTo(View::linkToPage($total_pages));
                        }elseif($this->page < 1){
                            static::redirectTo(View::linkToPage(1));
                        }
                        $films = Film::loadFilms(' name LIKE :name ', [':name' => '%' . $this->get->name . '%'], 'name', $this->page);
                        $view = new View('film/index');
                        if ($films_count > PAGINATION_PARAMS['films']['per_page'])
                            $view->makePagination($films_count, $this->page, 'films');
                    }else{
                        $view = new View('film/zero_result');
                    }
                    break;

                case 'actor' :
                    $films_count = Film::countByActor($this->get->name);
                    if ($films_count > 0) {
                        $total_pages = Film::getTotalPages($films_count);
                        if ($total_pages < $this->page) {
                            static::redirectTo(View::linkToPage($total_pages));
                        }elseif($this->page < 1){
                            static::redirectTo(View::linkToPage(1));
                        }
                        $films = Film::findByActor($this->get->name, 'name',  $this->page);
                        $view = new View('film/index');
                        if ($films_count > PAGINATION_PARAMS['films']['per_page'])
                            $view->makePagination($films_count, $this->page, 'films');
                    }else{
                        $view = new View('film/zero_result');
                    }
                    break;
            }
            $view->data['films'] = $films;
            $view->render();
        }
    }

    public function afterAction() {
        static::sessionClean(['form_data', 'message']);
    }
}
