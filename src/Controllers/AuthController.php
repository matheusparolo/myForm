<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Controllers;

use TCC\Core\PageMaker;
use TCC\Models\Models\UserModel;

class AuthController{

    public function getLogin():void
    {

        new PageMaker("auth", ["login"]);

    }

    public function postLogin():void
    {

        UserModel::login($_POST["email"], $_POST["password"]);

    }


    public function getRegister():void
    {

        new PageMaker("auth", ["register"]);

    }

    public function postRegister():void
    {

        UserModel::register($_POST["name"], $_POST["email"], $_POST["password"]);

    }


    public function getLogout():void
    {

        UserModel::logout();

    }

}