<?php
/**
 * @author Matheus Parolo Miranda
 */

use \TCC\Middleware\Auth;
use \TCC\Middleware\ResearchCreator;
use \TCC\Middleware\ResearchMember;

// Index
$this->redirect('/', '/pesquisa')->add(new Auth());

// Auth
$this->get("/usuario", "\TCC\Controllers\AuthController:index");

$this->post("/entrar", "\TCC\Controllers\AuthController:postLogin");
$this->post("/cadastrar", "\TCC\Controllers\AuthController:postRegister");

$this->get("/sair", "\TCC\Controllers\AuthController:getLogout");

// User
$this->group('/usuario', function (){

    $this->get("/editar", "\TCC\Controllers\UserController:getUpdate");
    $this->post("/editar", "\TCC\Controllers\UserController:postUpdate");

    $this->post("/editar_senha", "\TCC\Controllers\UserController:postUpdatePassword");

    $this->get("/buscar/{name}", "\TCC\Controllers\UserController:getSearchUser");

})->add(new Auth());


// Research
$this->group('/pesquisa', function (){

    $this->get("", "\TCC\Controllers\ResearchController:index");
    $this->get("/json", "\TCC\Controllers\ResearchController:indexJson");



    $this->get("/criar", "\TCC\Controllers\ResearchController:getCreate");
    $this->post("/criar", "\TCC\Controllers\ResearchController:postCreate");

    $this->get("/{id}", "\TCC\Controllers\ResearchController:getResearch")->add(new ResearchMember("research"));
    $this->get("/{id}/json", "\TCC\Controllers\ResearchController:getResearchJson")->add(new ResearchMember("research"));

    $this->group("/", function(){

        $this->get("{id}/editar", "\TCC\Controllers\ResearchController:getUpdate");
        $this->get("{id}/editar/json", "\TCC\Controllers\ResearchController:getUpdateJson");

        $this->post("editar", "\TCC\Controllers\ResearchController:postUpdate");
        $this->post("deletar", "\TCC\Controllers\ResearchController:postDelete");

    })->add(new ResearchCreator());

})->add(new Auth());


// Form
$this->group('/formulario', function (){

    $this->group("/criar", function(){

        $this->get("/{research-id}", "\TCC\Controllers\FormController:getCreate");
        $this->post("", "\TCC\Controllers\FormController:postCreate");

    })->add(new ResearchMember("research", "research-id"));


    $this->group("/", function(){

        $this->post("editar", "\TCC\Controllers\FormController:postUpdate");
        $this->post("deletar", "\TCC\Controllers\FormController:postDelete");
        $this->post("resposta/enviar", "\TCC\Controllers\AnswerController:postAddAnswer");

        $this->group("{id}", function(){

            $this->get("", "\TCC\Controllers\FormController:index");

            $this->get("/json", "\TCC\Controllers\FormController:getFormJSON");

            $this->get("/editar", "\TCC\Controllers\FormController:getUpdate");
            $this->get("/resposta", "\TCC\Controllers\AnswerController:getAnswer");
            $this->get("/resposta/enviar", "\TCC\Controllers\AnswerController:getAddAnswer");
            $this->get("/resposta/{answerIndex}", "\TCC\Controllers\AnswerController:getAnswerByIndex");

            $this->get("/resultados", "\TCC\Controllers\AnswerController:getResults");
            $this->get("/resultados/json", "\TCC\Controllers\AnswerController:getResultsJSON");

        });


    })->add(new ResearchMember());

})->add(new Auth());


// Exception
$this->get("/erro", "\TCC\Controllers\ExceptionController:index");