// Submit
function login(){

    responseCodes["100"] = ["O email e/ou a senha utilizada são invalidos! ", "Verifique os dados informados e tente novamente."];
    submit_form("#form-login", "/entrar")

}

function register(){

    responseCodes["100"] = ["O email informado já está em uso! ", "Verifique os dados informados e tente novamente."];
    submit_form("#form-register", "/cadastrar", [], true, function(data){
        return verify_password_to_submit(data, "password")
    })

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

function main(){

    // Response responseCodes
    responseCodes["000"] = "/pesquisa";

    // Bind Actions
    $("#form-login").on("submit", login);
    $("#form-register").on("submit", register);

    $(".to-login, .to-register").on("click", move_form);
    $(".input-group-append").on("click", change_password_view);

}
window.onload = main;