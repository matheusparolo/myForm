function change_password_view(){

    let input = $(this).parent().find("input");

    if(input.attr("type") === "text") {
        input.attr("type", "password");
        $(this).find("img").attr("title", "Mostrar Senha").attr("src", "/assets/media/img/icons/closed_eye.png");
    }else{
        input.attr("type", "text");
        $(this).find("img").attr("title", "Esconder Senha").attr("src", "/assets/media/img/icons/eye.png");
    }

}
function verify_password_to_submit(data, name){

    let password, confirmPassword;

    data.forEach(function(input){
        if(input["name"] === name)
            password = input["value"];

        else if(input["name"] === "confirm-" + name)
            confirmPassword = input["value"];
    });

    if(password && confirmPassword && password === confirmPassword){
        return true;
    }else{
        insert_alert("As senhas não coincidem! ", "Tente verificar as senhas clicando no icone de 'olho' ao lado do campo e comparando-as!", "danger");
        return false;
    }

}
