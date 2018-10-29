<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Controllers;

use TCC\Core\App;
use TCC\Core\PageMaker;
use TCC\Models\Models\FormModel;
use TCC\Models\Models\ResearchModel;

class AnswerController
{


    private $args;

    public function getResults($request, $response, $args):void
    {

        $this->args = $args;
        App::response_type([
            "html" => function(){

                // HTML Response
                $form = FormModel::find_by_id($this->args["id"], ["name"]);

                new PageMaker("form/results", [
                    "formName" => $form->getName(),
                    "formID" => $this->args["id"]
                ]);

            },
            "json" => function(){

                // JSON Response
                $results = FormModel::get_results($this->args["id"]);
                $form = FormModel::find_by_id($this->args["id"], ["name"]);
                $questions = FormModel::find_questions($this->args["id"], true);
                App::json_response([
                    "results" => $results,
                    "name" => $form->getName(),
                    "questions" => $questions
                ]);

            }]);


    }


    public function getAnswerByIndex($req, $res, $args)
    {

        $answers = FormModel::find_answer_by_index($args["id"], $args["answerIndex"]);
        App::json_response($answers);

    }


    public function getAddAnswer($req, $res, $args):void
    {

        $this->args = $args;

        App::response_type([

            "html" => function(){

                // HTML Response
                new PageMaker("form/submit-answer", ["formID" => $this->args["id"]]);

            },
            "json" => function(){

                // JSON Response
                $form = FormModel::find_by_id($this->args["id"], ["name", "research_id"]);
                $research = ResearchModel::find_by_id($form->getResearchID(), ["terms"]);
                $questions = FormModel::find_questions($this->args["id"], true);
                $data = [
                    "name" => $form->getName(),
                    "terms" => $research->getTerms(),
                    "questions" => $questions
                ];
                App::json_response($data);

            }

        ]);


    }

    public function postAddAnswer():void
    {

        if(!$_POST["answers"])
            $_POST["answers"] = [];
        else
            $_POST["answers"] = json_decode($_POST["answers"]);

        $form = new FormModel();
        $form->add_answer($_POST["id"], $_POST["cpf"], $_POST["answers"], $_FILES);

    }


    public function postUpdateText():void
    {

        if(!$_POST["textAnswers"])
            $_POST["textAnswers"] = [];

        $form = new FormModel();
        $form->update_text($_POST["id"], $_POST["intervieweeID"], $_POST["textAnswers"]);


    }


}