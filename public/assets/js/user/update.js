// Submits
function submit_info(){

    responseCodes["100"] = ["O email informado já está em uso! ", "Verifique os dados informados e tente novamente."];
    $("#form-info").submitter("/usuario/editar");

}
function submit_passwords(){

    responseCodes["100"] = ["A senha informada está incorreta! ", "Tente verificar as senhas clicando no icone de 'olho' ao lado do campo e comparando-as!"];
    $("#form-passwords").submitter("/usuario/editar_senha", {

        callbackData: function(data){
            return verify_password_to_submit(data["newPassword"], data["confirmNewPassword"]);
        }

    })

}

// Main
function init_vars(){

    responseCodes["000"] = "";

}
function get_data(){

    getJSON("/usuario/editar/json", function(userData){

        $("#name").val(userData["name"]);
        $("#email").val(userData["email"]);

    });

}
function binds(){

    $("#form-info").on("submit", submit_info);
    $("#form-passwords").on("submit", submit_passwords);

}

function main(){

    init_vars();
    get_data();
    binds();

}

window.onload = main;