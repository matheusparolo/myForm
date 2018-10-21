function submit_infos()
{

    responseCodes["100"] = ["O email informado já está em uso! ", "Verifique os dados informados e tente novamente."];
    submit_form("#form-infos", "/usuario/editar");

}
function submit_passwords()
{

    responseCodes["100"] = ["A senha informada está incorreta! ", "Tente verificar as senhas clicando no icone de 'olho' ao lado do campo e comparando-as!", "danger"];
    submit_form("#form-passwords", "/usuario/editar_senha", [], true, function(data){
        return verify_password_to_submit(data, "new-password")
    })

}

function main(){

    responseCodes["000"] = "";

    $(".input-group-append").on("click", change_password_view);

    $("#form-infos").on("submit", submit_infos);
    $("#form-passwords").on("submit", submit_passwords);

}
window.onload = main;