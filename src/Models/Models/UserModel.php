<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Models\Models;

use TCC\Core\App;
use TCC\Models\DAO\UserDAO;
use TCC\Models\Entities\GenericEntity;

class UserModel{

    const SESSION = "user";

    private $userDAO;


    public function __construct()
    {

        $this->userDAO = new UserDAO();

    }


    public function update(int $id, string $name, string $email):void
    {

        if(self::find_by_id($id, ["email"])->getEmail() != $email && self::email_exists($email))
            App::code_json_response("100");


        $this->userDAO->update($id, $name, $email);
        App::code_json_response("000");

    }

    public function update_password(int $id, string $oldPassword, string $newPassword):void
    {

        if(self::verify_password($id, $oldPassword)){

            $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->userDAO->update_password($id, $newPassword);

            App::code_json_response("000");

        }else{

            App::code_json_response("100");

        }

    }


    public static function register(string $name, string $email, string $password):void
    {

        if(!self::email_exists($email)){

            $password = password_hash($password, PASSWORD_DEFAULT);

            $userDAO = new UserDAO();
            $user = $userDAO->create($name, $email, $password);

            self::regenerate_session($user);

            App::code_json_response("000");

        }else{

            App::code_json_response("100");

        }


    }

    public static function login(string $email, string $password):void
    {

        $userDAO = new UserDAO();
        $user = $userDAO->find_by_email($email, ["id", "email", "password"]);

        if(!$user->isEmpty() && password_verify($password, $user->getPassword())){

            self::regenerate_session($user->getId());

            App::code_json_response("000");

        }else{

            App::code_json_response("100");

        }

    }

    public static function logout():void
    {

        session_destroy();
        setcookie("token_1", "", 0, "/");
        setcookie("token_2", "", 0, "/");

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
            return password_verify($password, $user->getPassword());

        else
            return false;

    }


    public static function find_by_id(int $id, array $columns = ["*"]):GenericEntity
    {

        $userDAO = new UserDAO();
        return $userDAO->find_by_id($id, $columns);

    }

    public static function find_all_by_name_like(int $id, string $name, bool $returnWithoutEntity = false):void
    {

        $userDAO = new UserDAO();
        echo json_encode($userDAO->find_all_by_name_like($id, $name, $returnWithoutEntity));

    }

    public static function find_by_tokens(string $token1, string $token2, array $columns = ["*"]):GenericEntity
    {

        $userDAO = new UserDAO();
        return $userDAO->find_by_tokens($token1, $token2, $columns);

    }


    public static function regenerate_session(int $userID):void
    {

        $userDAO = new UserDAO();
        $token1 = bin2hex(random_bytes(32));
        $token2 = bin2hex(random_bytes(32));

        session_regenerate_id(true);

        $_SESSION[UserModel::SESSION] = [
            "id" => $userID,
            "token_1" => $token1,
            "token_2" => $token2,
        ];

        setcookie("token_1", $token1, time() + 60 * 60 * 24 * 365, "/");
        setcookie("token_2", $token2, time() + 60 * 60 * 24 * 365, "/");

        $userDAO->update_tokens($userID, $token1, $token2);


    }

}