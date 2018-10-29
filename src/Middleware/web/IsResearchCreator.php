<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Middleware\web;

use TCC\Core\App;
use TCC\Models\Models\ResearchModel;
use TCC\Models\Models\UserModel;

class IsResearchCreator
{

    private $name;

    public function __construct(String $name = "id")
    {

        $this->name = $name;

    }

    public function __invoke($request, $response, $next)
    {

        $researchId = ($request->getMethod() == "GET") ? $request->getAttribute('route')->getArgument($this->name) : $_POST[$this->name];

        $research = ResearchModel::find_by_id($researchId, ["creator_id"]);
        $isCreator = !$research->isEmpty() && $_SESSION[UserModel::SESSION]["id"] == $research->getCreatorId();

        if(!$isCreator){

            App::response_type([

                "html" => function(){

                    App::location_replace("/pesquisas");

                },
                "json" => function(){

                    App::code_json_response(400);

                }

            ]);

        }
        $response = $next($request, $response);
        return $response;

    }

}