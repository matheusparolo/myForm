<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Middleware\web;

use TCC\Core\App;
use TCC\Models\Models\UserModel;

class IsAuth{

    public function __invoke($request, $response, $next)
    {

        $redirect = function(){

            App::response_type([
                "html" => function(){

                    App::location_replace("/usuario");

                },
                "json" => function(){

                    App::code_json_response(300);

                }
            ]);

        };

        if(!isset($_SESSION[UserModel::SESSION]) || empty($_SESSION[UserModel::SESSION]))
            if(!isset($_COOKIE["token_1"]) || empty($_COOKIE["token_1"]) || !isset($_COOKIE["token_2"]) || empty($_COOKIE["token_2"]))
                $redirect();

            else{

                $userData = UserModel::find_by_tokens($_COOKIE["token_1"], $_COOKIE["token_2"], ["id", "token_1", "token_2"]);
                if(!$userData->isEmpty())
                    UserModel::regenerate_session($userData->getId());

                else
                    $redirect();

            }

        else
            if(!isset($_COOKIE["token_1"]) || empty($_COOKIE["token_1"]) || !isset($_COOKIE["token_2"]) || empty($_COOKIE["token_2"]))
                $redirect();

            else
                if($_SESSION[UserModel::SESSION]["token_1"] != $_COOKIE["token_1"] || $_SESSION[UserModel::SESSION]["token_2"] != $_COOKIE["token_2"])
                    $redirect();


        $response = $next($request, $response);
        return $response;

    }

}