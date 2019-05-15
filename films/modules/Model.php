<?php

namespace Films\Modules;

use PDO;

class Model
{
    private static $table;

    public static function validate($obj) {

    }

    /*считает количество записей в переданной таблице исходя из переданного условия*/

    public static function count($table, $condition = '', $bindings = []) {
        $query = 'SELECT COUNT(*) FROM '. $table;
        if ($condition !== '')
            $query .= ' WHERE ' . $condition;
        $statement = Db::getConnect()->prepare($query);
        $statement->execute($bindings);
        return $statement->fetch()[0];
    }

    /*загружает массив элементов из базы данных исходя из условия, сортировки, лимита и отступа*/

    public static function load($condition = '', $prepared = [], $order_by = '', $limit = 9, $offset = 0){
        $query = '
            SELECT * FROM ' . static::$table;
        if ($condition !== '') {
            $query .= ' WHERE ' . $condition;
        }
        if ($order_by !== '') {
            $query .= ' ORDER BY ' . $order_by;
        }
        if ($limit !== 0) {
            $query .= ' LIMIT ' . $limit;
        }
        if ($offset !== 0) {
            $query .= ' OFFSET ' . $offset;
        }
        $statement = Db::getConnect()->prepare($query);
        $statement->execute($prepared);
        $data = [];
        while ($result = $statement->fetchObject(get_called_class()))
            $data[] = $result;
        return $data;
    }

    /*выгружает 1 запись из базы данных исходя из условия, переданного при вызове функции*/

    public static function loadOne($condition = '', $prepared = []){
        $query = '
            SELECT * FROM ' . static::$table;
        if ($condition !== '') {
            $query .= ' 
            WHERE ' . $condition;
        }
        $statement = Db::getConnect()->prepare($query);
        $statement->execute($prepared);
        if ($statement->rowCount() === 1) {
            $className = get_called_class();
            $model = new $className;
            $statement->setFetchMode(PDO::FETCH_INTO, $model);
            $statement->fetch();
            return $model;
        }
    }

    public static function deleteById($id) {
        $query = 'DELETE FROM ' . static::$table . ' WHERE id = :id';
        $statement = Db::getConnect()->prepare($query);
        return $statement->execute(['id' => $id]);
    }
}