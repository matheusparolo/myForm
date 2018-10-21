<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Controllers;

use TCC\Core\PageMaker;
use TCC\Models\Models\FormModel;
use TCC\Models\Models\ResearchModel;
use TCC\Models\Models\UserModel;

class ResearchController
{

    public function index():void
    {

        new PageMaker("research/index", []);

    }

    public function indexJson():void
    {

        $userID = $_SESSION[UserModel::SESSION];
        $researches = ResearchModel::find_all_by_user_id($userID, true);

        echo json_encode([
           "userID" => $userID,
           "researches" => $researches
        ]);

    }


    public function getResearch($req, $res, $args):void
    {

        new PageMaker("research/research", [
            "id" => $args["id"]
        ]);

    }

    public function getResearchJson($req, $res, $args):void
    {

        $research = ResearchModel::find_by_id($args["id"], ["id", "name", "overview", "application_area"], true);
        $creator = ResearchModel::find_creator($args["id"], ["user.name"], true);
        $forms = FormModel::find_all_by_research_id($args["id"], true);
        echo json_encode(
            [
                "research" => $research,
                "creator" => $creator,
                "forms" => $forms
            ]
        );

    }


    public function getCreate():void
    {

        $areas = ResearchModel::find_all_application_areas();
        new PageMaker("research/create", ["areas" => $areas]);

    }

    public function postCreate():void
    {

        if(!$_POST["members"])
            $_POST["members"] = [];
        else
            $_POST["members"] = explode(",", $_POST["members"]);

        $research = new ResearchModel();
        $research->create($_SESSION[UserModel::SESSION], $_POST["name"], $_POST["overview"], $_POST["application-area"], (array)$_POST["members"]);

    }


    public function getUpdate($req, $res, $args):void
    {

        $areas = ResearchModel::find_all_application_areas();
        new PageMaker("research/update", ["id" => $args["id"], "areas" => $areas]);

    }

    public function getUpdateJson($req, $res, $args):void
    {

        $research = ResearchModel::find_by_id($args["id"], ["id", "name", "overview", "application_area"], true);
        $members = ResearchModel::find_members($args["id"], $_SESSION[UserModel::SESSION], true);
        echo json_encode([
            "research" => $research,
            "members" => $members
        ]);

    }


    public function postUpdate():void
    {

        if(!$_POST["members"])
            $_POST["members"] = [];
        else
            $_POST["members"] = explode(",", $_POST["members"]);

        $research = new ResearchModel();
        $research->update($_POST["id"], $_SESSION[UserModel::SESSION], $_POST["name"], $_POST["overview"], $_POST["application-area"], $_POST["members"]);

    }


    public function postDelete():void
    {

        $research = new ResearchModel();
        $research->delete($_POST["id"]);

    }

}