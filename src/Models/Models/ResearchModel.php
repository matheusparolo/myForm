<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Models\Models;

use TCC\Core\App;
use TCC\Models\DAO\ResearchDAO;

class ResearchModel{

    private $researchDAO;

    public function __construct()
    {

        $this->researchDAO = new ResearchDAO();

    }


    public function create(int $userID, string $name, string $overview, string $terms, string $applicationArea, array $members):void
    {

        array_push($members, $userID);

        $this->researchDAO->create($userID, $name, $overview, $terms, $applicationArea, $members);
        App::code_json_response("000");

    }

    public function update(int $researchID, int $userID, string $name, string $overview, string $terms, string $applicationArea, array $members):void
    {

        array_push($members, $userID);

        $this->researchDAO->update($researchID, $name, $overview, $terms, $applicationArea, $members);
        App::code_json_response("000");

    }

    public function delete(int $id):void
    {

        $forms = FormModel::find_all_by_research_id($id);
        $this->researchDAO->delete($id);

        foreach($forms as $form)
            App::rmdir($_SERVER["DOCUMENT_ROOT"] . "/../private/assets/audio/form_" . $form->getId(), true);

        App::code_json_response("000");

    }


    public static function find_by_id(int $id, array $columns = ["*"], bool $returnWithoutEntity = false)
    {

        $researchDAO = new ResearchDAO();
        return $researchDAO->find_by_id($id, $columns, $returnWithoutEntity);

    }

    public static function find_creator(int $id, array $columns = ["*"], bool $returnWithoutEntity = false)
    {

        $researchDAO = new ResearchDAO();
        return $researchDAO->find_creator($id, $columns, $returnWithoutEntity);

    }

    public static function find_members(int $id, int $userID, bool $returnWithoutEntity = false):array
    {

        $researchDAO = new ResearchDAO();
        return $researchDAO->find_members($id, $userID, $returnWithoutEntity);

    }

    public static function find_all_by_user_id(int $userID, bool $returnWithoutEntity = false):array
    {

        $researchDAO = new ResearchDAO();
        return $researchDAO->find_all_by_user_id($userID, $returnWithoutEntity);

    }

    public static function find_all_application_areas():array
    {

        $researchDAO = new ResearchDAO();
        return $researchDAO->find_all_application_areas();

    }


    public static function isMember(int $researchID, int $userID):bool
    {

        $researchDAO = new ResearchDAO();
        return $researchDAO->isMember($researchID, $userID);

    }

}