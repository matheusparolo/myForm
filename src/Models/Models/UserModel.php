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


    public function update(int $id, string $name, string $email):void
    {

        if(self::email_exists($email)){

            $this->userDAO->update($id, $name, $email);

            App::action_response("000");

        }else{

            App::action_response("100");

        }

    }

    public function update_password(int $id, string $oldPassword, string $newPassword):void
    {

        if(self::verify_password($id, $oldPassword)){

            $this->userDAO->update_password($id, $newPassword);

            App::action_response("000");

        }else{

            App::action_response("100");

        }

    }


    public static function register(string $name, string $email, string $password):void
    {

        if(!self::email_exists($email)){

            $userDAO = new UserDAO();
            $_SESSION[self::SESSION] = $userDAO->create($name, $email, $password);

            App::action_response("000");

        }else{

            App::action_response("100");

        }


    }

    public static function login(string $email, string $password):void
    {

        $userDAO = new UserDAO();
        $user = $userDAO->find_by_email($email, ["id", "email", "password"]);

        if(!$user->isEmpty()){

            if($password == $user->getPassword()){

                $_SESSION[self::SESSION] = $user->getId();
                App::action_response("000");

            }else{

                App::action_response("100");

            }


        }else{

            App::action_response("100");

        }

    }

    public static function logout():void
    {

        session_destroy();

        header("location: /");
        exit;

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