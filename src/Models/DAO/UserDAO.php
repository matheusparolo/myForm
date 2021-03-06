<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Models\DAO;

use TCC\Models\Entities\GenericEntity;

class UserDAO{

    private $conn;

    public function __construct()
    {

        $this->conn = new Connector();

    }


    public function create(string $name, string $email, string $password):int
    {

        $this->conn->query("insert into myForm.user(name, email, password) values(:name, :email, :password)",[
            "name" => $name,
            "email" => $email,
            "password" => $password
        ]);

        return $this->conn->last_insert_id();

    }

    public function update(int $id, string $name, string $email):void
    {

        $this->conn->query("update myForm.user set name = :name, email = :email where id = :id", [
            "id" => $id,
            "name" => $name,
            "email" => $email
        ]);

    }

    public function update_password(int $id, String $password):void
    {

        $this->conn->query("update myForm.user set password = :password where id = :id", [
            "id" => $id,
            "password" => $password
        ]);

    }

    public function update_tokens(int $userID, string $token1, string $token2):void
    {

        $this->conn->query("update user set token_1 = :token1, token_2 = :token2 where id = :userID",[
            "userID" => $userID,
            "token1" => $token1,
            "token2" => $token2
        ]);

    }


    public function find_by_id(int $id, array $columns = ["*"]):GenericEntity
    {

        $data = $this->conn->select("select " . join(",", $columns) . " from myForm.user where id = :id",
            [
                "id" => $id
            ]);

        return new GenericEntity(!empty($data) ? $data[0] : []);

    }

    public function find_by_tokens(string $token1, string $token2, array $columns = ["*"]):GenericEntity
    {

        $data = $this->conn->select("select " . join(",", $columns) . " from myForm.user where token_1 = :token1 and token_2 = :token2",
            [
                "token1" => $token1,
                "token2" => $token2
            ]);

        return new GenericEntity(!empty($data) ? $data[0] : []);

    }

    public function find_by_email(String $email, array $columns = ["*"]):GenericEntity
    {

        $data = $this->conn->select("select " . join(",", $columns) . " from myForm.user where email = :email",
            [
                "email" => $email
            ]);

        return new GenericEntity(!empty($data) ? $data[0] : []);

    }

    public function find_all_by_name_like(int $id, string $name, bool $returnWithoutEntity):array
    {

        $data =  $this->conn->select("select id, name, email from myForm.user where name like CONCAT('%',:name,'%') and id != :id", [
            "id" => $id,
            "name" => $name
        ]);

        if(!$returnWithoutEntity)
            foreach($data as &$line)
                $line = new GenericEntity($line);

        return $data;

    }

}