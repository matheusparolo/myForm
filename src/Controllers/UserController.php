<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Controllers;

use TCC\Core\PageMaker;
use TCC\Models\Models\UserModel;

class UserController{

    public function getUpdate():void
    {

        $user = UserModel::find_by_id($_SESSION[UserModel::SESSION], ["id", "name", "email"]);
        new PageMaker("user", ["update" => ["user" => $user]]);

    }

    public function postUpdate():void
    {

        $_POST["id"] = $_SESSION[UserModel::SESSION];

        $user = new UserModel();
        $user->update($_POST);

    }


    public function getUpdatePass():void
    {

        new PageMaker("user", ["update-pass"]);

    }

    public function postUpdatePass():void
    {

        $user = new UserModel();
        $user->update_pass($_SESSION[UserModel::SESSION], $_POST["oldPassword"], $_POST["newPassword"]);

    }


    public function getSearchUser($req, $res, $args):void
    {

        UserModel::find_all_by_name_like($_SESSION[UserModel::SESSION], $args["name"]);

    }

}