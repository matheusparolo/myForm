<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Controllers;

use TCC\Core\PageMaker;
use TCC\Models\Models\FormModel;

class FormController
{

    public function getCreate($req, $res, $args):void
    {

        new PageMaker("form/create", ["researchId" => $args["research-id"]]);

    }

    public function postCreate():void
    {

        $form = new FormModel();
        $form->create($_POST["research-id"], $_POST["name"], json_decode($_POST["questions"]));

    }


    public function getUpdate($req, $res, $args):void
    {

        $researchID = FormModel::find_by_id($args["id"], ["research_id"])->getResearchID();
        new PageMaker("form/update", ["formID" => $args["id"], "researchID" => $researchID]);

    }

    public function postUpdate():void
    {

        $form = new FormModel();
        $form->update($_POST["id"], $_POST["name"], json_decode($_POST["questions"]));

    }


    public function getFormJSON($req, $res, $args){

        $form = FormModel::find_by_id($args["id"], ["name"]);
        $questions = FormModel::find_questions($args["id"], true);
        $data = [
            "name" => $form->getName(),
            "questions" => $questions
        ];
        echo json_encode($data);

    }


    public function postDelete():void
    {

        $form = new FormModel();
        $form->delete($_POST["id"]);

    }

}