<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Controllers;

use TCC\Core\PageMaker;
use TCC\Models\Models\FormModel;

class FormController
{

    public function index($req, $res, $args):void
    {

        $form = FormModel::find_by_id($args["id"], ["id", "name"]);
        new PageMaker("form", ["index" => ["form" => $form]]);

    }


    public function getCreate($req, $res, $args):void
    {

        new PageMaker("form", ["create" => ["researchID" => $args["researchID"]]]);

    }

    public function postCreate():void
    {

        $form = new FormModel();
        $form->create($_POST["researchID"], $_POST["name"], $_POST["questions"]);

    }


    public function getUpdate($req, $res, $args):void
    {

        new PageMaker("form", ["update" => ["formID" => $args["id"]]]);

    }

    public function postUpdate():void
    {

        $form = new FormModel();
        $form->update($_POST["id"], $_POST["name"], $_POST["questions"]);

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