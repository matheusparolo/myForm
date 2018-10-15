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


    public function create(array $data):void
    {

        $this->conn->query("insert into tcc.user(email, name, password) values(:email, :name, :password)", $data);

    }

    public function update(array $data):void
    {

        $this->conn->query("update tcc.user set email = :email, name = :name where id = :id", $data);

    }

    public function update_pass(int $userID, String $pass):void
    {

        $this->conn->query("update tcc.user set password = :password where id = :id", ["password" => $pass, "id" => $userID]);

    }


    public function find_by_id(int $id, array $columns = ["*"]):GenericEntity
    {

        $data = $this->conn->query("select " . join(",", $columns) . " from user where id = :id",
            [
                "id" => $id
            ]);

        return new GenericEntity(!empty($data) ? $data[0] : []);

    }

    public function find_by_email(String $email, array $columns = ["*"]):GenericEntity
    {

        $data = $this->conn->query("select " . join(",", $columns) . " from user where email = :email",
            [
                "email" => $email
            ]);

        return new GenericEntity(!empty($data) ? $data[0] : []);

    }

    public function find_all_by_name_like(int $id, string $name):array
    {

        return $this->conn->query("select id, name, email from tcc.user where name like CONCAT('%',:name,'%') and id != :id",
            [
                "id" => $id,
                "name" => $name
            ]);

    }

}