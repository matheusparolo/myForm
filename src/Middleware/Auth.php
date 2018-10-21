<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Middleware;

use TCC\Core\App;
use TCC\Models\Models\UserModel;

class Auth{

    public function __invoke($request, $response, $next)
    {

        if(!isset($_SESSION[UserModel::SESSION]) || empty($_SESSION[UserModel::SESSION]))
        {

            $method = $request->getMethod();
            if($method == "GET"){

                header("location: /usuario");
                exit;

            }else App::action_response(300);

        }

        $response = $next($request, $response);
        return $response;

    }

}