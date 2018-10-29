<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Controllers;

use TCC\Core\App;
use TCC\Core\PageMaker;
use TCC\Models\Models\UserModel;

class UserController{

    public function getUpdate($request, $response)
    {

        App::response_type([
            "html" => function(){

                // HTML Response
                new PageMaker("user/update");

            },
            "json" => function(){

                // JSON Response
                $userData = UserModel::find_by_id($_SESSION[UserModel::SESSION]["id"], ["id", "name", "email"])->getData();
                App::json_response($userData);

        }]);

    }


    public function postUpdate():void
    {

        $user = new UserModel();
        $user->update($_SESSION[UserModel::SESSION]["id"], $_POST["name"], $_POST["email"]);

    }

    public function postUpdatePassword():void
    {

        $user = new UserModel();
        $user->update_password($_SESSION[UserModel::SESSION]["id"], $_POST["oldPassword"], $_POST["newPassword"]);

    }


    public function getSearchUser($req, $res, $args):void
    {

        UserModel::find_all_by_name_like($_SESSION[UserModel::SESSION]["id"], $args["name"], true);

    }

}