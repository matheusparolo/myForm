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
        new PageMaker("user/update", ["user" => $user]);

    }


    public function postUpdate():void
    {

        $user = new UserModel();
        $user->update($_SESSION[UserModel::SESSION], $_POST["name"], $_POST["email"]);

    }

    public function postUpdatePassword():void
    {

        $user = new UserModel();
        $user->update_password($_SESSION[UserModel::SESSION], $_POST["old-password"], $_POST["new-password"]);

    }


    public function getSearchUser($req, $res, $args):void
    {

        UserModel::find_all_by_name_like($_SESSION[UserModel::SESSION], $args["name"], true);

    }

}