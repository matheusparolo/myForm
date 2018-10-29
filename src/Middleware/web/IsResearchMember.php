<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Middleware\web;

use TCC\Core\App;
use TCC\Models\Models\FormModel;
use TCC\Models\Models\ResearchModel;
use TCC\Models\Models\UserModel;

class IsResearchMember
{

    private $name, $fromTable;

    public function __construct(String $fromTable = "form", String $name = "id")
    {

        $this->name = $name;
        $this->fromTable = $fromTable;

    }

    public function __invoke($request, $response, $next)
    {

        $getId = ($request->getMethod() == "GET") ? $request->getAttribute('route')->getArgument($this->name) : $_POST[$this->name];

        if($this->fromTable == "form"){
            $entity = FormModel::find_by_id($getId, ["research_id"]);
            $getId = $entity->isEmpty() ? false : $entity->getResearchID();
        }

        $isMember = $getId ? ResearchModel::isMember($getId, $_SESSION[UserModel::SESSION]["id"]) : false;

        if(!$isMember){

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