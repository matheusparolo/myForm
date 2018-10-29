<?php
/**
 * @author Matheus Parolo Miranda
 */

use \TCC\Middleware\web\IsAuth;
use \TCC\Middleware\web\IsResearchCreator;
use \TCC\Middleware\web\IsResearchMember;

$this->authController          = "\TCC\Controllers\AuthController:";
$this->userController          = "\TCC\Controllers\UserController:";
$this->researchController      = "\TCC\Controllers\ResearchController:";
$this->formController          = "\TCC\Controllers\FormController:";
$this->answerController        = "\TCC\Controllers\AnswerController:";
$this->exceptionController     = "\TCC\Controllers\ExceptionController:";
$this->privateFilesController  = "\TCC\Controllers\PrivateFilesController:";


// Index
$this->redirect('/', '/pesquisas')->add(new IsAuth());


// IsAuth
$this->get("/usuario",    $this->authController . "index");

$this->post("/entrar",    $this->authController . "postLogin");
$this->post("/cadastrar", $this->authController . "postRegister");

$this->get("/sair",       $this->authController . "getLogout");


// User
$this->group('/usuario', function (){

    $this->get("/editar",            $this->userController . "getUpdate");
    $this->get("/editar/{dataType}", $this->userController . "getUpdate");

    $this->post("/editar",           $this->userController . "postUpdate");

    $this->post("/editar_senha",     $this->userController . "postUpdatePassword");

    $this->get("/buscar/{name}",     $this->userController . "getSearchUser");

})->add(new IsAuth());


// Research
$this->group('/pesquisa', function(){

    $this->get("s",                  $this->researchController . "index");
    $this->get("s/{dataType}",       $this->researchController . "index");

    $this->get("/criar",             $this->researchController . "getCreate");
    $this->get("/criar/{dataType}",  $this->researchController . "getCreate");
    $this->post("/criar",            $this->researchController . "postCreate");

    $this->group("/", function(){

        $this->get("{id}/editar",             $this->researchController . "getUpdate");
        $this->get("{id}/editar/{dataType}",  $this->researchController . "getUpdate");

        $this->post("editar",                 $this->researchController . "postUpdate");
        $this->post("deletar",                $this->researchController . "postDelete");

    })->add(new IsResearchCreator());

    $this->get("/{id}",             $this->researchController . "getResearch")->add(new IsResearchMember("research"));
    $this->get("/{id}/{dataType}",   $this->researchController . "getResearch")->add(new IsResearchMember("research"));

})->add(new IsAuth());


// Form
$this->group('/formulario', function (){

    $this->group("/criar", function(){

        $this->post("",             $this->formController . "postCreate");
        $this->get("/{researchID}", $this->formController . "getCreate");

    })->add(new IsResearchMember("research", "researchID"));


    $this->group("/", function(){

        $this->post("editar",                 $this->formController . "postUpdate");
        $this->post("deletar",                $this->formController . "postDelete");
        $this->post("resposta/enviar",        $this->answerController . "postAddAnswer");
        $this->post("resposta/editar_texto",  $this->answerController . "postUpdateText");

        $this->group("{id}", function(){

            $this->get("/editar",                             $this->formController . "getUpdate");
            $this->get("/editar/{dataType}",                  $this->formController . "getUpdate");

            $this->get("/resposta/enviar",                    $this->answerController . "getAddAnswer");
            $this->get("/resposta/enviar/{dataType}",         $this->answerController . "getAddAnswer");

            $this->get("/resposta/{answerIndex}/{dataType}",  $this->answerController . "getAnswerByIndex");

            $this->get("/resultados",                         $this->answerController . "getResults");
            $this->get("/resultados/{dataType}",              $this->answerController . "getResults");

        });


    })->add(new IsResearchMember());

})->add(new IsAuth());

$this->get("/private/assets/audio/{formID}/{intervieweeID}/{questionID}", $this->privateFilesController . "answerAudio")->add(new IsResearchMember("form", "formID"));

// Exception
$this->get("/erro", $this->exceptionController . "index");