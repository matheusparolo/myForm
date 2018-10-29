<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Controllers;

use TCC\Core\App;
use TCC\Core\PageMaker;
use TCC\Models\Models\FormModel;

class FormController
{

    private $args;

    public function getCreate($req, $res, $args):void
    {

        new PageMaker("form/create", ["researchID" => $args["researchID"]]);

    }

    public function postCreate():void
    {

        if(!$_POST["questions"]) $_POST["questions"] = [];

        $form = new FormModel();
        $form->create($_POST["researchID"], $_POST["name"], $_POST["questions"]);

    }


    public function getUpdate($request, $response, $args):void
    {

        $this->args = $args;
        App::response_type([
            "html" => function(){

                // HTML Response
                $researchID = FormModel::find_by_id($this->args["id"], ["research_id"])->getResearchID();
                new PageMaker("form/update", [
                    "formID" => $this->args["id"],
                    "researchID" => $researchID
                ]);

            },
            "json" => function(){

                // JSON Response
                $form = FormModel::find_by_id($this->args["id"], ["name"]);
                $questions = FormModel::find_questions($this->args["id"], true);
                $data = [
                    "name" => $form->getName(),
                    "questions" => $questions
                ];
                App::json_response($data);

        }]);

    }

    public function postUpdate():void
    {

        if(!$_POST["questions"]) $_POST["questions"] = [];

        $form = new FormModel();
        $form->update($_POST["id"], $_POST["name"], $_POST["questions"]);

    }


    public function postDelete():void
    {

        $form = new FormModel();
        $form->delete($_POST["id"]);

    }

}