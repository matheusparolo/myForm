<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Controllers;

use TCC\Core\PageMaker;
use TCC\Models\Models\FormModel;

class AnswerController
{

    public function getResults($req,$res,$args):void
    {

        $form = FormModel::find_by_id($args["id"], ["name"]);

        new PageMaker("form/results", [
            "formName" => $form->getName(),
            "formID" => $args["id"]
        ]);

    }

    public function getResultsJSON($req,$res,$args):void
    {

        $results = FormModel::get_results($args["id"]);
        echo json_encode($results);

    }


    public function getAnswerByIndex($req, $res, $args)
    {

        $answers = FormModel::find_answer_by_index($args["id"], $args["answerIndex"]);
        exit(json_encode($answers));

    }


    public function getAddAnswer($req, $res, $args):void
    {

        new PageMaker("form/submit-answer", ["formID" => $args["id"]]);

    }

    public function postAddAnswer():void
    {

        $form = new FormModel();
        $form->add_answer($_POST["id"], $_POST["answers"]);

    }

}