<?php
/**
 * @author Matheus Parolo Miranda
 */

namespace TCC\Models\DAO;

use TCC\Models\Entities\GenericEntity;

class ResearchDAO{

    private $conn;

    public function __construct()
    {

        $this->conn = new Connector();

    }


    public function create(int $userID, string $name, string $overview, string $applicationArea, array $members):void
    {

        $this->conn->begin_transaction();

        $this->conn->query("insert into myForm.research(creator_id, name, overview, application_area) values(:userID, :name, :overview, :applicationArea)", [
            "userID" => $userID,
            "name" => $name,
            "overview" => $overview,
            "applicationArea" => $applicationArea,
        ]);

        $researchID = $this->conn->last_insert_id();

        $this->add_members($researchID, $members);

        $this->conn->commit();

    }

    public function update(int $researchID, string $name, string $overview, string $applicationArea, array $users):void
    {

        $this->conn->begin_transaction();

        $this->conn->query("update myForm.research set name = :name, overview = :overview, application_area = :applicationArea where id = :researchID",
            [
                "name" => $name,
                "overview" => $overview,
                "applicationArea" => $applicationArea,
                "researchID" => $researchID
            ]);

        $this->conn->query("delete from myForm.researchers where research_id = :researchID",
            [
                "researchID" => $researchID
            ]);

        $this->add_members($researchID, $users);

        $this->conn->commit();

    }

    public function delete(int $id):void
    {

        $this->conn->query("delete from myForm.research where id = :id", ["id" => $id]);

    }


    public function find_by_id(int $id, array $columns = ["*"]):GenericEntity
    {

        $data = $this->conn->select("select " . join(",", $columns) . " from research where id = :id",
            [
                "id" => $id
            ]);

        return new GenericEntity(!empty($data) ? $data[0] : []);

    }

    public function find_all_by_user_id(int $userID):array
    {

        $researches = $this->conn->select(
            "select research.id, research.creator_id, research.name, user.name as creator_name
             from myForm.researchers
             inner join myForm.research on research.id = researchers.research_id
             inner join myForm.user on user.id = research.creator_id
             where researchers.user_id = :userID;",
            [
                "userID" => $userID
            ]);

        $return = [];
        foreach($researches as $line)
        {

            $research = new GenericEntity([
                "id" => $line["id"],
                "name" => $line["name"],
                "creatorId" => $line["creator_id"]
            ]);

            array_push($return, [
                "researchEntity" => $research,
                "creator_name" => $line["creator_name"]
            ]);

        }

        return $return;

    }

    public function find_all_application_areas():array
    {

        $areas = $this->conn->select("select application_area from myForm.research");

        foreach ($areas as &$area)
            $area = new GenericEntity($area);

        return $areas;

    }

    public function find_members(int $researchID, int $userID):array
    {

        $users = $this->conn->select(
            "select user.id, user.name, user.email from myForm.researchers 
            inner join myForm.user on user_id = user.id
            where research_id = :id and user_id != :userID;",
            [
                "id" => $researchID,
                "userID" => $userID
            ]);

        $return = [];
        foreach($users as $user)
        {

            array_push($return, new GenericEntity($user));

        }
        return $return;

    }


    public function isMember(int $researchID, int $userID):bool
    {

        $data = $this->conn->select("select '' from myForm.researchers where research_id = :researchID and user_id = :userID",
            [
                "researchID" => $researchID,
                "userID" => $userID
            ]);

        return empty($data) ? false : true;

    }


    private function add_members(int $researchID, array $users):void
    {

        $this->conn->prepare("insert into myForm.researchers (user_id, research_id) values(:userID, '$researchID')");
        foreach($users as $userID)
        {

            $this->conn->bind([
                "userID" => $userID
            ]);

            $this->conn->execute();

        }

    }

}