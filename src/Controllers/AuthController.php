<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Controllers;

use TCC\Core\PageMaker;
use TCC\Models\Models\UserModel;

class AuthController{

    public function index():void
    {

        new PageMaker("user/auth");

    }


    public function postLogin():void
    {

        UserModel::login($_POST["email"], $_POST["password"]);

    }

    public function postRegister():void
    {

        UserModel::register($_POST["name"], $_POST["email"], $_POST["registerPassword"]);

    }


    public function getLogout():void
    {

        UserModel::logout();

    }

}