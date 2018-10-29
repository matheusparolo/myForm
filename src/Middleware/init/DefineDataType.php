<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Middleware\init;

use TCC\Core\App;

class DefineDataType{

    public function __invoke($request, $response, $next)
    {

        $method = $request->getMethod();
        $dataType = $request->getAttribute('route')->getArgument("dataType");

        if($method == "GET" && $dataType == null)
            $dataType = "html";

        else if($dataType == null)
            $dataType = "json";

        else if($dataType != "json")
            return $response->withStatus(404);

        App::add_env("dataType", $dataType);;

    }

}