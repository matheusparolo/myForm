<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Controllers;

use TCC\Core\App;
use TCC\Core\PageMaker;
use TCC\Models\Models\FormModel;
use TCC\Models\Models\ResearchModel;
use TCC\Models\Models\UserModel;

class ResearchController
{


    private $args;


    public function index($request, $response, $args):void
    {

        $this->args = $args;
        App::response_type([
            "html" => function(){

                // HTML Response
                new PageMaker("research/index");

            },
            "json" => function(){

                // JSON Response
                $userID = $_SESSION[UserModel::SESSION]["id"];
                $researches = ResearchModel::find_all_by_user_id($userID, true);

                App::json_response([
                    "userID" => $userID,
                    "researches" => $researches
                ]);

        }]);

    }


    public function getResearch($request, $response, $args):void
    {

        $this->args = $args;
        App::response_type([
            "html" => function(){

                // HTML Response
                new PageMaker("research/research", [
                    "id" => $this->args["id"]
                ]);

            },
            "json" => function(){

                // JSON Response
                $research = ResearchModel::find_by_id($this->args["id"], ["id", "name", "overview", "application_area"], true);
                $creator = ResearchModel::find_creator($this->args["id"], ["user.name"], true);
                $forms = FormModel::find_all_by_research_id($this->args["id"], true);
                App::json_response([
                    "research" => $research,
                    "creator" => $creator,
                    "forms" => $forms
                ]);

        }]);


    }


    public function getCreate($request, $response, $args):void
    {

        $this->args = $args;
        App::response_type([
            "html" => function(){

                // HTML Response
                new PageMaker("research/create");

            },
            "json" => function(){

                // JSON Response
                $areas = ResearchModel::find_all_application_areas();
                App::json_response([
                    "areas" => $areas
                ]);

        }]);

    }

    public function postCreate():void
    {

        if(!$_POST["members"]) $_POST["members"] = [];

        $research = new ResearchModel();
        $research->create($_SESSION[UserModel::SESSION]["id"], $_POST["name"], $_POST["overview"], $_POST["terms"], $_POST["applicationArea"], $_POST["members"]);

    }


    public function getUpdate($request, $response, $args):void
    {

        $this->args = $args;
        App::response_type([
            "html" => function(){

                // HTML Response
                new PageMaker("research/update", ["id" => $this->args["id"]]);

            },
            "json" => function(){

                // JSON Response
                $areas = ResearchModel::find_all_application_areas();
                $research = ResearchModel::find_by_id($this->args["id"], ["id", "name", "overview", "application_area", "terms"], true);
                $members = ResearchModel::find_members($this->args["id"], $_SESSION[UserModel::SESSION]["id"], true);
                App::json_response([
                    "areas" => $areas,
                    "research" => $research,
                    "members" => $members
                ]);

        }]);

    }

    public function postUpdate():void
    {

        if(!$_POST["members"]) $_POST["members"] = [];

        $research = new ResearchModel();
        $research->update($_POST["id"], $_SESSION[UserModel::SESSION]["id"], $_POST["name"], $_POST["overview"], $_POST["terms"], $_POST["applicationArea"], $_POST["members"]);

    }


    public function postDelete():void
    {

        $research = new ResearchModel();
        $research->delete($_POST["id"]);

    }


}