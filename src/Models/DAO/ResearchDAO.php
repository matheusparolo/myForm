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


    public function create(int $userID, string $name, array $members):void
    {

        $this->conn->begin_transaction();

        $this->conn->query("INSERT INTO tcc.research(creator_id, name) values(:userID, :name)", [
            "userID" => $userID,
            "name" => $name
        ]);

        $researchID = $this->conn->last_insert_id();

        $this->add_members($researchID, $members);

        $this->conn->commit();

    }

    public function update(int $researchID, string $name, array $members):void
    {

        $this->conn->begin_transaction();

        $this->conn->query("update tcc.research set name = :name where id = :researchID",
            [
                "name" => $name,
                "researchID" => $researchID
            ]);

        $this->conn->query("delete from tcc.researchers where research_id = :researchID",
            [
                "researchID" => $researchID
            ]);

        $this->add_members($researchID, $members);

        $this->conn->commit();

    }

    public function delete(int $id):void
    {

        $this->conn->query("delete from tcc.research where id = :id", ["id" => $id]);

    }


    public function find_by_id(int $id, array $columns = ["*"]):GenericEntity
    {

        $data = $this->conn->query("select " . join(",", $columns) . " from research where id = :id",
            [
                "id" => $id
            ]);

        return new GenericEntity(!empty($data) ? $data[0] : []);

    }

    public function find_all_by_user_id(int $userID):array
    {

        $data = $this->conn->query(
            "select tcc.research.id, tcc.research.creator_id, tcc.user.name as creator_name, tcc.research.name
             from tcc.researchers
             inner join tcc.research on researchers.user_id = :userID and research.id = researchers.research_id
             inner join tcc.user on user.id = research.creator_id;",
            [
                "userID" => $userID
            ]);

        $return = [];
        foreach($data as $line)
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

    public function find_members(int $id, int $userID):array
    {

        $users = $this->conn->query(
            "select user.id, user.name, user.email from tcc.researchers 
            inner join tcc.user where research_id = :id and user_id = user.id and user_id != :userID;",
            [
                "id" => $id,
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

        $data = $this->conn->query("select '' from tcc.researchers where research_id = :researchID and user_id = :userID",
            [
                "researchID" => $researchID,
                "userID" => $userID
            ]);

        return empty($data) ? false : true;

    }


    private function add_members(int $researchID, array $members):void
    {

        $this->conn->prepare("INSERT INTO tcc.researchers (user_id, research_id) values(:memberID, '$researchID')");
        foreach($members as $memberID)
        {

            $this->conn->bind([
                "memberID" => $memberID
            ]);

            $this->conn->execute();

        }

    }

}