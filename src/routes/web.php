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
$this->get("/entrar", "\TCC\Controllers\AuthController:getLogin");
$this->post("/entrar", "\TCC\Controllers\AuthController:postLogin");

$this->get("/cadastrar", "\TCC\Controllers\AuthController:getRegister");
$this->post("/cadastrar", "\TCC\Controllers\AuthController:postRegister");

$this->get("/sair", "\TCC\Controllers\AuthController:getLogout");

// User
$this->group('/usuario', function (){

    $this->redirect("", "/usuario/editar");
    $this->redirect("/", "/usuario/editar");

    $this->get("/editar", "\TCC\Controllers\UserController:getUpdate");
    $this->post("/editar", "\TCC\Controllers\UserController:postUpdate");

    $this->get("/editar_senha", "\TCC\Controllers\UserController:getUpdatePassword");
    $this->post("/editar_senha", "\TCC\Controllers\UserController:postUpdatePassword");

    $this->get("/buscar/{name}", "\TCC\Controllers\UserController:getSearchUser");

})->add(new Auth());


// Research
$this->group('/pesquisa', function (){

    $this->get("", "\TCC\Controllers\ResearchController:index");

    $this->get("/criar", "\TCC\Controllers\ResearchController:getCreate");
    $this->post("/criar", "\TCC\Controllers\ResearchController:postCreate");

    $this->get("/{id}", "\TCC\Controllers\ResearchController:getResearch")->add(new ResearchMember("research"));

    $this->group("/", function(){

        $this->get("{id}/editar", "\TCC\Controllers\ResearchController:getUpdate");

        $this->post("editar", "\TCC\Controllers\ResearchController:postUpdate");
        $this->post("deletar", "\TCC\Controllers\ResearchController:postDelete");

    })->add(new ResearchCreator());

})->add(new Auth());


// Form
$this->group('/formulario', function (){

    $this->group("/criar", function(){

        $this->get("/{researchID}", "\TCC\Controllers\FormController:getCreate");
        $this->post("", "\TCC\Controllers\FormController:postCreate");

    })->add(new ResearchMember("research", "researchID"));


    $this->group("/", function(){

        $this->post("deletar", "\TCC\Controllers\FormController:postDelete");
        $this->post("resposta/enviar", "\TCC\Controllers\FormController:postAddAnswer");

        $this->group("{id}", function(){

            $this->get("", "\TCC\Controllers\FormController:index");

            $this->get("/resposta", "\TCC\Controllers\FormController:getAnswer");
            $this->get("/resposta/enviar", "\TCC\Controllers\FormController:getAddAnswer");
            $this->get("/resposta/{answerIndex}", "\TCC\Controllers\FormController:getAnswerIndex");

            $this->get("/resultados", "\TCC\Controllers\FormController:getResults");

        });


    })->add(new ResearchMember());

})->add(new Auth());