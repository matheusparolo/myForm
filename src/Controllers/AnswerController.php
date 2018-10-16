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

        $results = FormModel::get_results($args["id"]);

        new PageMaker("form", ["results" => ["results" => $results]]);


    }


    public function getAnswer($req, $res, $args):void
    {

        $form = FormModel::find_by_id($args["id"], ["id", "name"]);
        $questions = FormModel::find_questions($args["id"]);

        new PageMaker("form", ["answer" => ["form" => $form, "questions" => $questions]]);

    }

    public function getAnswerByIndex($req, $res, $args)
    {

        $answers = FormModel::find_answer_by_index($args["id"], $args["answerIndex"]);
        exit(json_encode($answers));

    }


    public function getAddAnswer($req, $res, $args):void
    {

        $form = FormModel::find_by_id($args["id"], ["id", "name"]);
        $questions = FormModel::find_questions($args["id"]);

        new PageMaker("form", ["submit-answer" => ["form" => $form, "questions" => $questions]]);

    }

    public function postAddAnswer():void
    {

        $form = new FormModel();
        $form->add_answer($_POST["id"], $_POST["answers"]);

    }

}