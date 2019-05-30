<?php

namespace Films\Models;

use PDO;
use Films\Modules\Model;
use Films\Modules\Db;

class Film extends Model
{
    public $id;
    public $name;
    public $year;
    public $format;
    public $actor_list;
    public $incorrect_fields;
    public static $table = 'films';

    public function __construct($film_data = null)
    {
        if(!is_null($film_data)) {
            $this->name = $film_data->name;
            $this->year = $film_data->year;
            $this->format = $film_data->format;
            if(property_exists($film_data, 'actor_list')) {
                if(is_string($film_data->actor_list)) {
                    $this->actor_list['names'] = explode(', ', $film_data->actors);
                }else{
                    $this->actor_list['names'] = $film_data->actor_list;
                }
            }
        }
    }

    /*загружает фильмы, исходя из текущей страницы, максимального количества фильмов на странице,
    условия поиска фильмов и сортирует их*/

    public static function loadFilms($condition = '', $prepared = [], $order_by = '', $page = 1) {
        $limit = PAGINATION_PARAMS['films']['per_page'];
        $offset = ($page - 1) * $limit;
        return static::load($condition, $prepared, $order_by, $limit, $offset);
    }

    /*создаёт экземпляр класса Film, дополнительно загружая снявшихся в нём актёров*/

    public static function loadOne($condition = '', $prepared = [])
    {
        $film = parent::loadOne($condition, $prepared);
        if(!is_null($film)) {
            $film->getActors();
            return $film;
        }
        return null;
    }

    /*
    валидирует данные, из файла, полученного от пользователя
    принимает файл, возвращает объект со свойствами:
    ->films - перечень валидированных фильмов,
    ->message - сообщение об ошибках и колличестве валидированных фильмов
    */

    public static function validateFile($file) {
        $films_string = preg_split('/\\n\\n/', file_get_contents($file), -1, PREG_SPLIT_NO_EMPTY);   //разбивает весь текст файла по 1 фильму
        $films_array = [];
        $validation['message'] = '';
        $tolower = function ($item) {
            return mb_strtolower($item);
        };
        $existing_names = array_map($tolower, static::getExistingNames());
        $existing_formats = array_map($tolower, static::getAllFormats());
        $registred_names = [];
        foreach ($films_string as $key => $film_string) {
            $film = [];
            preg_match('/Title: (.+)\\nRelease Year: (.+)\\nFormat: (.+)\\nStars: (.+)/', $film_string,$film);
            unset($film[0]);
            if(count($film) < 4) { //удаляет фильм, если заданы не все поля
                $validation['message'] .= 'Синтаксическая ошибка в фильме под номером ' . $key . '<br>';
                continue;
            }
            if(in_array(mb_strtolower($film[1]), $existing_names)) { // проверяет, нет ли такого фильма в базе данных
                $validation['message'] .= 'Имя ' . $film[1] . ' уже занято.<br>';
                continue;
            }
            if(isset($registred_names[mb_strtolower($film[1])])) { //проверяет, дублируются ли имена у фильмов в файле. Если да, удаляет все одинаковые -1
                $validation['message'] .= 'В вашем файле дублируется имя ' . $film[1] . '.<br>';
                unset($validation['films'][$registred_names[$film[1]]]);
                continue;
            }
            if(strlen(trim($film[2])) !== 4 || !is_numeric(trim($film[2]))){ // валидация года выхода
                $validation['message'] .= 'У фильма под номером ' . $key . ' год выхода указан некорректно.<br>';
                continue;
            }
            if(!in_array(mb_strtolower($film[3]), $existing_formats)) { // валидация формата
                $validation['message'] .= 'Некорректный формат у фильма под номером ' . $key . '.<br>';
                continue;
            }
            $registred_names[mb_strtolower($film[1])] = $key;
            $validation['films'][$key] = (object)['name' => trim($film[1]), 'year' => trim($film[2]), 'format' => trim($film[3]), 'actor_list' => preg_split('/, /', $film[4], -1, PREG_SPLIT_NO_EMPTY)];
        }
        $validation['message'] .= 'Сохранено ' . count($validation['films']) . ' фильмов.';
        return (object)$validation;
    }


    public static function getExistingNames() {
        $query = 'SELECT name FROM films';
        $statement = Db::getConnect()->query($query);
        return $statement->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    public static function getAllFormats() {
        $query = 'SELECT format FROM formats';
        $statement = Db::getConnect()->query($query);
        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    /*
    принимает список актёров, отсеивает несуществующих
    */

    public static function getExistingActorsByNames($actor_names) {
        $query = 'SELECT * FROM actors WHERE ';
        $prepared = [];
        for ($i = 0; $i <= count($actor_names) - 1; $i++) {
            $query .= 'actor_name = :name' . $i . ' OR ';
            $prepared[':name' . $i] = $actor_names[$i];
        }
        $query = substr($query, 0, strlen($query) - 4);
        $statement = Db::getConnect()->prepare($query);
        $statement->execute($prepared);
        $actors = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $actors;
    }

    /*валидирует данные, введённые пользователем перед созданием экземпляра класса*/

    public static function validate($obj) {
        $existing_names = static::getExistingNames();
        $existing_formats = static::getAllFormats();
        $result = [];
        $result['success'] = true;
        $result['incorrect_fields'] = '';
        if(!property_exists($obj, 'name') || $obj->name == '') {
            $result['incorrect_fields'] .= 'Incorrect name.<br>';
            $result['success'] = false;
        }
        if(in_array($obj->name, $existing_names)) {
            $result['incorrect_fields'] .= 'Name already taken.<br>';
            $result['success'] = false;
        }
        if(!property_exists($obj, 'year') || !is_numeric($obj->year)) {
            $result['incorrect_fields'] .= 'Incorrect year.<br>';
            $result['success'] = false;
        }
        if(!property_exists($obj, 'format') || !in_array($obj->format, $existing_formats)) {
            $result['incorrect_fields'] .= 'Incorrect format.<br>';
            $result['success'] = false;
        }
        return $result;
    }

    public function save() {
        $query = 'INSERT INTO films (name, year, format) VALUES (:name, :year, :format)';
        $statement = Db::getConnect()->prepare($query);
        $film_saved = $statement->execute(['name' => $this->name, 'year' => $this->year, 'format' => $this->format]);
        $this->id = Db::getConnect()->lastInsertId();
        $this->saveActors();
        return $film_saved;
    }

    /*принимает json файл, запускает валидацию и сохраняет валидированные фильмы в базу данных*/

    public static function saveFilmsFromFile($films) {
        $films_file = $films['tmp_name'];
        if(file_exists($films_file) && is_uploaded_file($films_file)) {
            $validation = Film::validateFile($films_file);
            $result['message'] = $validation->message;
            $result['success'] = true;
            foreach ($validation->films as $film) {
                $film_model = new Film($film);
                $film_model->save();
            }
        }else{
            $result['message'] = 'Ошибка загрузки файла';
            $result['success'] = false;
        }
        return $result;
    }

    /*
    исходя из опции NEW_ACTORS добавляет (save_auto) или отсеивает (save_not) новых актёров,
    после чего создаёт записи в связующей таблице
    */

    public function saveActors() {
        $existing_actors = static::getExistingActorsByNames($this->actor_list['names']);
        $this->actor_list['ids'] = array_column($existing_actors, 'id');
        $existing_actors_names = array_column($existing_actors, 'actor_name');
        $new_actors = array_diff($this->actor_list['names'], $existing_actors_names);
        if (count($new_actors) > 0) {
            switch (NEW_ACTORS) {
                case ('save_auto'):
                    $query = 'INSERT INTO actors (actor_name) VALUES (:name)';
                    $statement = Db::getConnect()->prepare($query);
                    Db::getConnect()->beginTransaction();
                    foreach ($new_actors as $name) {
                        $statement->execute([':name' => $name]);
                        $this->actor_list['ids'][] = Db::getConnect()->lastInsertId();
                    }
                    Db::getConnect()->commit();
                    break;
                case ('save_not'):
                    $this->actor_list['names'] = $existing_actors['names'];
                    break;
            }
        }
        $this->linkActors();
    }

    /*создаёт связи фильмов с актёрами, в которых они снялись*/

    public function linkActors() {
        $query = 'INSERT INTO films_actors (film_id, actor_id) VALUES (:film_id, :actor_id)';
        $statement = Db::getConnect()->prepare($query);
        foreach ($this->actor_list['ids'] as $actor_id) {
            if($statement->execute([':film_id' => $this->id, ':actor_id' => $actor_id])) {
                $success = true;
            }else{
                return false;
            }
        }
        return $success;
    }

    /*удаляет связи актёров с фильмом, который удаляется*/

    public static function deleteActorsLinks($film_id) {
        $query = 'DELETE FROM films_actors WHERE film_id = :film_id';
        $statement = Db::getConnect()->prepare($query);
        return $statement->execute([':film_id' => $film_id]);
    }

    /*удаляет актёров, которые не связаны ни с 1 из существующих фильмов*/

    public static function deleteActors() {
        $query = 'DELETE FROM actors WHERE actors.id NOT IN (SELECT actor_id FROM films_actors)';
        return Db::getConnect()->query($query);
    }

    /*возвращает список всех актёров, снявшихся в данном фильме*/

    public function getActors() {
        $query = 'SELECT actors.id, actors.actor_name
            FROM films_actors
            INNER JOIN actors on films_actors.actor_id = actors.id
            WHERE films_actors.film_id = :id';
        $statement = Db::getConnect()->prepare($query);
        $statement->execute(['id' => $this->id]);
        $this->actor_list = $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    высчитывает количество страниц исходя из общего количества фильмов, переданном при вызове функции
    и параметра пагинации
    */

    public static function getTotalPages($total_count) {
        return ceil($total_count/PAGINATION_PARAMS['films']['per_page']);
    }

    /*осуществляет поиск фильмов по заданному актёру, исходя из текущей страницы*/

    public static function findByActor($actor_name, $order_by = '', $page = 1) {
        $query = 'SELECT films.id, films.name, films.format, films.year FROM films
            INNER JOIN films_actors on films_actors.film_id = films.id
            INNER JOIN actors on actors.id = films_actors.actor_id
            WHERE actors.actor_name LIKE :actor_name';
        if ($order_by !== ''){
            $query .= ' ORDER BY ' . $order_by;
        }
        $query .= ' LIMIT ' . PAGINATION_PARAMS['films']['per_page'] . ' OFFSET ' . ($page - 1) * PAGINATION_PARAMS['films']['per_page'];
        $statement = Db::getConnect()->prepare($query);
        $statement->execute([':actor_name' => '%' . $actor_name . '%']);
        $film_list = [];
        while ($film = $statement->fetchObject(static::class))
            $film_list[] = $film;
        return $film_list;
    }

    /*считает количество фильмов, в которых снялся актёр, переданный в параметрах (функция, в основном, для работы с пагинацией)*/

    public static function countByActor($actor_name) {
        $query = 'SELECT COUNT(*) FROM films
            INNER JOIN films_actors on films_actors.film_id = films.id
            INNER JOIN actors on actors.id = films_actors.actor_id
            WHERE actors.actor_name LIKE :actor_name';
        $statement = Db::getConnect()->prepare($query);
        $statement->execute([':actor_name' => '%' . $actor_name . '%']);
        return $statement->fetch()[0];
    }

    public static function delete($id) {
        $deleted_actors_links = static::deleteActorsLinks($id);
        $deleted_film = static::deleteById($id);
        return $deleted_actors_links && $deleted_film;
    }
}