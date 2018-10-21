<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Models\Models;

use TCC\Core\App;
use TCC\Models\DAO\FormDAO;
use TCC\Models\Entities\GenericEntity;

class FormModel{

    private $formDAO;

    public function __construct()
    {

        $this->formDAO = new FormDAO();

    }


    public function create(int $researchID, string $name, array $questions):void
    {

        $this->formDAO->create($researchID, $name, $questions);
        App::action_response("000");

    }

    public function update(int $formID, string $name, array $questions):void
    {

        $this->formDAO->update($formID, $name, $questions);
        App::action_response("000");

    }

    public function delete(int $id):void
    {

        $this->formDAO->delete($id);
        App::action_response("000");

    }


    public function add_answer(int $id, array $answers):void
    {

        $this->formDAO->add_answer($id, $answers);
        App::action_response("000");

    }


    public static function get_results(int $id):array
    {

        $formDAO = new FormDAO();
        return $formDAO->get_results($id);

    }


    public static function find_by_id(int $id, array $columns = ["*"]):GenericEntity
    {

        $formDAO = new FormDAO();
        return $formDAO->find_by_id($id, $columns);

    }

    public static function find_questions(int $id, bool $returnWithoutEntity = false):array
    {

        $formDAO = new FormDAO();
        return $formDAO->find_questions($id, $returnWithoutEntity);

    }

    public static function find_all_by_research_id(int $researchID, bool $returnWithoutEntity = false):array
    {

        $formDAO = new FormDAO();
        return $formDAO->find_all_by_research_id($researchID, $returnWithoutEntity);

    }

    public static function find_answer_by_index(int $id, int $index):array
    {

        $formDAO = new FormDAO();
        return $formDAO->find_answer_by_index($id, $index);

    }

}