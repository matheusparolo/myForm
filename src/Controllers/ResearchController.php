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

        $userID = $_SESSION[UserModel::SESSION];
        $researches = ResearchModel::find_all_by_user_id($userID);

        new PageMaker("research", ["index" => ["researches" => $researches, "userID" => $userID]]);

    }


    public function getResearch($req, $res, $args):void
    {

        $research = ResearchModel::find_by_id($args["id"], ["id", "name"]);
        $forms = FormModel::find_all_by_research_id($args["id"]);

        new PageMaker("research", ["research" => [
            "research" => $research,
            "forms" => $forms
        ]]);

    }


    public function getCreate():void
    {

        $areas = ResearchModel::find_all_application_areas();
        new PageMaker("research", ["create" => ["areas" => $areas]]);

    }

    public function postCreate():void
    {

        if(!$_POST["members"]) $_POST["members"] = [];

        $research = new ResearchModel();
        $research->create($_SESSION[UserModel::SESSION], $_POST["name"], $_POST["overview"], $_POST["applicationArea"], $_POST["members"]);

    }


    public function getUpdate($req, $res, $args):void
    {

        $research = ResearchModel::find_by_id($args["id"], ["id", "name", "overview", "application_area"]);
        $areas = ResearchModel::find_all_application_areas();
        $members = ResearchModel::find_members($args["id"], $_SESSION[UserModel::SESSION]);

        new PageMaker("research", [
            "update" => [
                "research" => $research,
                "areas" => $areas,
                "members" => $members
            ]
        ]);

    }

    public function postUpdate():void
    {

        if(!$_POST["members"]) $_POST["members"] = [];

        $research = new ResearchModel();
        $research->update($_POST["id"], $_SESSION[UserModel::SESSION], $_POST["name"], $_POST["overview"], $_POST["applicationArea"], $_POST["members"]);

    }


    public function postDelete():void
    {

        $research = new ResearchModel();
        $research->delete($_POST["id"]);

    }

}