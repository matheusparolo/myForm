<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Models\Models;

use TCC\Core\App;
use TCC\Models\DAO\UserDAO;
use TCC\Models\Entities\GenericEntity;

class UserModel{

    const SESSION = "userID";

    private $userDAO;

    public function __construct()
    {

        $this->userDAO = new UserDAO();

    }


    public function update(array $data):void
    {

        if(self::email_exists($data["email"])){

            $this->userDAO->update($data);

            App::action_response("000");

        }else{

            App::action_response("100");

        }

    }

    public function update_pass(int $userID, string $oldPass, string $newPass):void
    {

        if(self::verify_password($userID, $oldPass)){

            $this->userDAO->update_pass($userID, $newPass);

            App::action_response("000");

        }else{

            App::action_response("100");

        }

    }


    public static function register(array $data):void
    {

        if(!self::email_exists($data["email"])){

            $userDAO = new UserDAO();
            $userDAO->create($data);

            $_SESSION[self::SESSION] = $userDAO->find_by_email($data["email"], ["id"])->getId();

            App::action_response("000");

        }else{

            App::action_response("100");

        }


    }

    public static function login(array $data):void
    {

        $userDAO = new UserDAO();
        $user = $userDAO->find_by_email($data["email"], ["id", "email", "password"]);

        if(!$user->isEmpty()){

            if($data["password"] == $user->getPassword()){

                $_SESSION[self::SESSION] = $user->getId();
                App::action_response("000");

            }else{

                App::action_response("100");

            }


        }else{

            App::action_response("100");

        }

    }

    public static function logout(bool $redirect = true):void
    {

        session_destroy();

        if($redirect){

            header("location: /");
            exit;

        }

    }


    public static function email_exists(string $email):bool
    {

        $userDAO = new UserDAO();
        return $userDAO->find_by_email($email, ["id"])->isEmpty() ? false : true;

    }

    public static function verify_password(int $id, string $password):bool
    {

        $userDAO = new UserDAO();
        $user = $userDAO->find_by_id($id, ["password"]);

        if(!$user->isEmpty())
        {

            $userPassword = $user->getPassword();
            return $userPassword == $password ? true : false;

        }else{
            return false;
        }

    }


    public static function find_by_id(int $id, $columns = ["*"]):GenericEntity
    {

        $userDAO = new UserDAO();
        return $userDAO->find_by_id($id, $columns);

    }

    public static function find_all_by_name_like(int $id, string $name):void
    {

        $userDAO = new UserDAO();
        echo json_encode($userDAO->find_all_by_name_like($id, $name));

    }

}