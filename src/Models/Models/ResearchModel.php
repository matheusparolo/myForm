<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Models\Models;

use TCC\Core\App;
use TCC\Models\DAO\ResearchDAO;
use TCC\Models\Entities\GenericEntity;

class ResearchModel{

    private $researchDAO;

    public function __construct()
    {

        $this->researchDAO = new ResearchDAO();

    }


    public function create(int $userID, string $name, string $overview, string $applicationArea, array $members):void
    {

        array_push($members, $userID);

        $this->researchDAO->create($userID, $name, $overview, $applicationArea, $members);
        App::action_response("000");

    }

    public function update(int $researchID, int $userID, string $name, string $overview, string $applicationArea, array $members):void
    {

        array_push($members, $userID);

        $this->researchDAO->update($researchID, $name, $overview, $applicationArea, $members);
        App::action_response("000");

    }

    public function delete(int $id):void
    {

        $this->researchDAO->delete($id);
        App::action_response("000");

    }


    public static function find_by_id(int $id, array $columns = ["*"]):GenericEntity
    {

        $researchDAO = new ResearchDAO();
        return $researchDAO->find_by_id($id, $columns);

    }

    public static function find_members(int $id, int $userID):array
    {

        $researchDAO = new ResearchDAO();
        return $researchDAO->find_members($id, $userID);

    }

    public static function find_all_by_user_id(int $userID):array
    {

        $researchDAO = new ResearchDAO();
        return $researchDAO->find_all_by_user_id($userID);

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