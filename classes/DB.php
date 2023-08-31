<?php

class DB {

    private static $_instance;
    private $_pdo,
            $_query,
            $_error = false,
            $_results,
            $_count = 0;

    private function __construct()
    {

        try {


            $this->_pdo = new PDO('mysql:host=' . Config::get('xDB/host') . ';dbname=' . Config::get('xDB/db'), Config::get('xDB/user'), Config::get('xDB/password'));
        } catch (PDOException $e) {


            die($ex->getMessage());
        }
    }

    //Avoid creating a new database
    public static function getInstance()
    {
        if (!isset(self::$_instance))
        {
            self::$_instance = new DB();
        }
        return self::$_instance;
    }

    public function query($sqlStatement, $sqlParams = [])
    {
        //error reset to false
        $this->_error = false;

        //prepare the statement
        if ($this->_query = $this->_pdo->prepare($sqlStatement))
        {

            //check for any bindings
            if (count($sqlParams))
            {
                $x = 1;
                foreach ($sqlParams as $params)
                {
                    $this->_query->bindValue($x, $params);
                    $x++;
                }
            }
//execute the statement
            if ($this->_query->execute())
            {
                //retrieve results if execution was successful
                $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
                //update the count from a PDO method
                $this->_count = $this->_query->rowCount();
            }
            else
            {

                $this->_error = true;
            }
        }

        return $this;
    }

    public function action($action, $table, $where = [])
    {
        if (count($where) === 3)
        {
            $operators = ['=', '>', '<', '>=', '<='];

            $field = $where[0];
            $operator = $where[1];
            $value = $where[2];
        }
        if (in_array($operator, $operators))
        {

            $sqlStatement = "{$action} FROM {$table} WHERE {$field}  {$operator} ? ";
            if (!$this->query($sqlStatement, [$value])->error())
            {
                return $this;
            }
        }
        return false;
    }

    public function insert($table, $field = [])
    {
        if (count($field))
        {
            $x = 1;
            $keys = array_keys($field);
            $value = ' ';


            foreach ($field as $key)
            {
                $value .= "?";
                if (count($field) > $x)
                {
                    $value .= ", ";
                }
                $x++;
            }



            $sqlStatement = "INSERT INTO {$table} (`" . implode('`,`', $keys) . "`)  VALUES ({$value})";

            if (!$this->query($sqlStatement, $field)->error())
            {
                return $this;
            }
        }
    }

    public function update($table, $id, $field = [])
    {
        if (count($field))
        {
            $x = 1;
            $set = '';
            foreach ($field as $keys => $key)
            {
                $set .= "{$keys} = ?";
                if (count($field) > $x)
                {
                    $set .= ", ";
                }
                $x++;
            }
            $sqlStatement = "UPDATE {$table} SET {$set}  WHERE id = {$id} ";
            if (!$this->query($sqlStatement, $field)->error())
            {
                return $this;
            }
        }
        return false;
    }

    public function get($table, $where)
    {

        return $this->action('SELECT * ', $table, $where);
    }

    public function delete($table, $where)
    {
        return $this->action('DELETE', $table, $where);
    }

    public function count()
    {
        return $this->_count;
    }

    public function results()
    {
        return $this->_results;
    }

    public function result()
    {
        return $this->results()[0];
    }

    public function error()
    {
        return $this->_error;
    }

}
