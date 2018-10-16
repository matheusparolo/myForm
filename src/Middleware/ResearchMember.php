<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Middleware;

use TCC\Core\App;
use TCC\Models\Models\FormModel;
use TCC\Models\Models\ResearchModel;
use TCC\Models\Models\UserModel;

class ResearchMember
{

    private $name, $fromTable;

    public function __construct(String $fromTable = "form", String $name = "id")
    {

        $this->name = $name;
        $this->fromTable = $fromTable;

    }

    public function __invoke($request, $response, $next)
    {

        $method = $request->getMethod();

        $getId = ($method == "GET") ? $request->getAttribute('route')->getArgument($this->name) : $_POST[$this->name];

        if($this->fromTable == "form"){
            $entity = FormModel::find_by_id($getId, ["research_id"]);
            $getId = $entity->isEmpty() ? false : $entity->getResearchID();
        }

        $isMember = $getId ? ResearchModel::isMember((int)$getId, $_SESSION[UserModel::SESSION]) : false;

        if(!$isMember){

            if($method == "GET") {
                header("location: /pesquisa");
                exit;
            }
            else App::action_response(400);

        }
        $response = $next($request, $response);
        return $response;

    }

}