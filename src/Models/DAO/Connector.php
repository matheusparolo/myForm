<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Models\DAO;

use TCC\Core\App;

class Connector{

    private $conn, $stmt, $inTransaction;


    public function __construct()
    {

        try {

            $dsn = App::env("db") . ":host=" . App::env("dbHost") . ";dbname=" . App::env("dbName") . ";charset=utf8";
            $this->conn = new \PDO($dsn, App::env("dbUser"), App::env("dbPass"), [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC]);
            $this->inTransaction = false;

        }
        catch(\PDOException $e){

            $this->casePDOException($e);

        }

    }


    public function prepare(string $query):void
    {

        try{

            $this->stmt = $this->conn->prepare($query);

        }
        catch(\PDOException $e){

            $this->casePDOException($e);

        }


    }

    public function bind(array $params = []):void
    {

        try {

            foreach ($params as $key => $value) {

                $this->stmt->bindValue($key, $value);

            }

        }catch(\PDOException $e){

            $this->casePDOException($e);

        }

    }

    public function execute():array
    {

        try {

            $this->stmt->execute();
            return $this->stmt->fetchAll();

        }
        catch(\PDOException $e){

            $this->casePDOException($e);

        }

    }


    public function query(string $query, array $params = []):array
    {

        $this->prepare($query);
        $this->bind($params);
        return $this->execute();

    }


    public function last_insert_id():string
    {

        try{

            return $this->conn->lastInsertId();

        }
        catch(\PDOException $e){

            $this->casePDOException($e);

        }


    }

    public function begin_transaction():void
    {

        $this->conn->beginTransaction();
        $this->inTransaction = true;

    }

    public function commit():void
    {

        $this->conn->commit();
        $this->inTransaction = false;

    }

    public function rollBack():void
    {

        $this->conn->rollBack();
        $this->inTransaction = false;

    }

    public function casePDOException(\PDOException $e):void
    {

        if($this->inTransaction){

            $this->conn->rollBack();
            $this->inTransaction = false;

        }

        if(App::env("dbDebug", false)) exit(json_encode($e));
        else App::action_response("200");

    }

}