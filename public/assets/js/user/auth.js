// Submit
function login(){

    responseCodes["100"] = ["O email e/ou a senha utilizada são invalidos!", "Verifique os dados informados e tente novamente."];
    $("#form-login").submitter("/entrar")

}
function register(){

    responseCodes["100"] = ["O email informado já está em uso!", "Verifique os dados informados e tente novamente."];
    $("#form-register").submitter("/cadastrar", {

        callbackData: function(data){
            return verify_password_to_submit(data["registerPassword"], data["confirmPassword"])
        }

    });

}

// Actions in HTML
function move_form(){

    let form = $("#div-form");

    if($(this).attr("data-action") === "to-login")
    {

        form.css("left", "50%");
        window.setTimeout(function(){form.css("left", "47%");}, 400);
        $("#login").show();
        $("#register").hide();


    }else{

        form.css("left", "0%");
        window.setTimeout(function(){form.css("left", "3%");}, 400);
        $("#register").show();
        $("#login").hide();

    }

}

// Main
function init_vars(){

    responseCodes["000"] = "/pesquisas";

}
function binds(){

    $("#form-login").on("submit", login);
    $("#form-register").on("submit", register);
    $(".to-login, .to-register").on("click", move_form);

}

function main(){

    init_vars();
    binds();

}

window.onload = main;