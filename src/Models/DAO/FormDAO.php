<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Models\DAO;

use TCC\Models\Entities\GenericEntity;

class FormDAO{

    private $conn;

    public function __construct()
    {

        $this->conn = new Connector();

    }


    public function create(int $researchID, string $name, array $questions):void
    {

        $this->conn->begin_transaction();

        $this->conn->query("INSERT INTO myForm.form(research_id, name) values(:researchID, :name)", [
            "researchID" => $researchID,
            "name" => $name
        ]);

        $formID = $this->conn->last_insert_id();

        $this->add_questions($formID, $questions);

        $this->conn->commit();

    }

    public function update(int $formID, string $name, array $questions):void
    {

        $this->conn->begin_transaction();

        $this->conn->query("update myForm.form set name = :name where id = :id",[
            "id" => $formID,
            "name" => $name
        ]);


        foreach($questions as $question)
        {

            $this->conn->query("update myForm.question set `index` = :index, required = :required, statement = :statement where id = :id",[
                "id" => $question["id"],
                "index" => $question["index"],
                "required" => $question["required"] == "true" ? 1 : 0,
                "statement" => $question["statement"]
            ]);

            if($question["options"])
            {

                foreach($question["options"] as $option)
                {

                    $this->conn->query("update myForm.boxOption set info = :info, describe_allowed = :describeAllowed where id = :id",[
                        "id" => $option["id"],
                        "info" => $option["info"],
                        "describeAllowed" => $option["describe_allowed"] == "true" ? 1 : 0
                    ]);

                }

            }

        }

        $this->conn->commit();

    }

    public function delete(int $id):void
    {

        $this->conn->query("delete from myForm.form where id = :id", ["id" => $id]);

    }


    public function get_results(int $id):array
    {

        $cInterviewee =  $this->conn->select("select count(interviewee.id) as c_interviewee from myForm.interviewee where form_id = :id",[
            "id" => $id
        ])[0]["c_interviewee"];

        $return = [
            "c_interviewee" => $cInterviewee,
            "answers" => []
        ];

        if($cInterviewee > 0)
        {

            $return["answers"] = $this->conn->select(
                "select `index`, statement, question_id, info, count(boxOption_id) as votes, round(((count(boxOption_id) * 100) / :cInterviewee), 2) as percent
                    from myForm.interviewee 
                    inner join myForm.boxAnswer on interviewee.id = boxAnswer.interviewee_id
                    inner join boxOption on boxOption.id = boxOption_id
                    inner join question on question.id = boxOption.question_id
                    where interviewee.form_id = :id
                    group by boxOption_id",
            [
                "id" => $id,
                "cInterviewee" => $cInterviewee
            ]);

        }


        return $return;

    }


    public function find_by_id(int $id, array $columns = ["*"]):GenericEntity
    {

        $data = $this->conn->select("select " . join(",", $columns) . " from form where id = :id",
            [
                "id" => $id
            ]);

        return new GenericEntity(!empty($data) ? $data[0] : []);

    }

    public function find_all_by_research_id(int $researchID):array
    {

        $data = $this->conn->select(
            "select id, name from myForm.form where research_id = :researchID",
            [
                "researchID" => $researchID
            ]);

        $return = [];
        foreach($data as $line)
        {

            array_push($return, new GenericEntity($line));

        }

        return $return;

    }

    public function find_questions(int $id, bool $returnJSON = false):array
    {

        $questions = $this->conn->select(
            "select id, `index`, statement, required, type from myForm.question where form_id= :id order by `index`",
            [
                "id" => $id
            ]);


        $options = $this->conn->select(
            "select boxOption.id, boxOption.question_id, boxOption.info, boxOption.describe_allowed
            from question
            inner join boxOption on question.id = boxOption.question_id
            where question.form_id = :formID",
            [
                "formID" => $id
            ]);

        foreach ($questions as &$question)
        {

            foreach($options as $option)
            {

                if($option["question_id"] == $question["id"]){


                    if(!$question["options"]) $question["options"] = [];
                    array_push($question["options"],($returnJSON) ? $option : new GenericEntity($option) );

                }

            }
            if(!$returnJSON) $question = new GenericEntity($question);

        }

        return $questions;

    }

    public function find_answer_by_index(int $id, int $index):array
    {

        $interviewee = $this->conn->select("select id from myForm.interviewee where form_id = :id limit " . ($index - 1) . ",1", [
            "id" => $id
        ]);

        if(!empty($interviewee)){

            $interviewee = $interviewee[0];
            $questions = $this->conn->select("select id, statement, type from myForm.question where form_id = :id order by `index`;", [
                "id" => $id
            ]);

            $return = [];
            foreach($questions as $question)
            {

                if($question["type"] == "text")
                {

                    $answer = $this->conn->select("select answer from myForm.textAnswer where question_id = :questionID and interviewee_id = :intervieweeID", [
                        "questionID" => $question["id"],
                        "intervieweeID" => $interviewee["id"]
                    ]);
                    array_push($return, !empty($answer) ? $answer[0] : []);

                }else{

                    $answer = $this->conn->select("select boxOption_id as id, describe_text from myForm.boxOption inner join myForm.boxAnswer where question_id = :questionID and boxOption.id = boxAnswer.boxOption_id and interviewee_id = :intervieweeID", [
                        "questionID" => $question["id"],
                        "intervieweeID" => $interviewee["id"]
                    ]);
                    array_push($return, [
                        "answer" => !empty($answer) ? $answer : []
                    ]);

                }

            }
        }else{
            $return = [];
        }

        return $return;


    }


    public function add_answer(int $id, array $answers):void
    {

        $this->conn->begin_transaction();

        $this->conn->query("insert into myForm.interviewee (form_id) values(:formID)", [
            "formID" => $id
        ]);

        $intervieweeID = $this->conn->last_insert_id();
        foreach ($answers as $answer)
        {

            if($answer["type"] == "text"){
                $this->conn->query("insert into myForm.textAnswer (question_id, interviewee_id, answer) values(:questionID, :intervieweeID, :answer)", [
                    "questionID" => $answer["id"],
                    "intervieweeID" => $intervieweeID,
                    "answer" => $answer["answer"]
                ]);
            }else{

                foreach($answer["answer"] as $option)
                {

                    $this->conn->query("insert into myForm.boxAnswer (boxOption_id, interviewee_id, describe_text) values(:boxOptionID, :intervieweeID, :describeText)", [
                        "boxOptionID" => $option["id"],
                        "intervieweeID" => $intervieweeID,
                        "describeText" => $option["describe"] ? $option["describe"] : null
                    ]);

                }

            }

        }

        $this->conn->commit();

    }

    private function add_questions(int $formID, array $questions):void
    {

        $this->conn->prepare("INSERT INTO myForm.question (form_id, `index`, statement, required, type) values('$formID', :index, :statement, :required, :type)");
        foreach($questions as $question)
        {

            $this->conn->bind([
                "index" => $question["index"],
                "statement" => $question["statement"],
                "required" => $question["required"] == "true" ? 1 : 0,
                "type" => $question["type"]
            ]);

            $this->conn->execute();

            if($question["type"] != "text"){

                $question_id = $this->conn->last_insert_id();
                $this->conn->prepare("INSERT INTO myForm.boxOption (question_id, info, describe_allowed) values('$question_id', :info, :describeAllowed)");
                foreach($question["options"] as $option)
                {

                    $this->conn->bind([
                        "info" => $option["info"],
                        "describeAllowed" => $option["describe_allowed"] == "true" ? 1 : 0
                    ]);

                    $this->conn->execute();

                }
                $this->conn->prepare("INSERT INTO myForm.question (form_id, `index`, statement, required, type) values('$formID', :index, :statement, :required, :type)");

            }

        }

    }

}