<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Middleware;

use TCC\Core\App;
use TCC\Models\Models\ResearchModel;
use TCC\Models\Models\UserModel;

class ResearchCreator
{

    private $name;

    public function __construct(String $name = "id")
    {

        $this->name = $name;

    }

    public function __invoke($request, $response, $next)
    {

        $method = $request->getMethod();

        $researchId = ($method == "GET") ? $request->getAttribute('route')->getArgument($this->name) : $_POST[$this->name];

        $research = ResearchModel::find_by_id($researchId, ["creator_id"]);
        $isCreator = !$research->isEmpty() && $_SESSION[UserModel::SESSION] == $research->getCreatorId() ? true : false;

        if(!$isCreator){

            if($method == "GET")
            {
                header("location: /pesquisa");
                exit;
            }else App::action_response(400);

        }
        $response = $next($request, $response);
        return $response;

    }

}