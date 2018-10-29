function verify_password_to_submit(password, confirmPassword){

    if(password && confirmPassword && password === confirmPassword){
        return true;
    }else{
        insert_alert("As senhas não coincidem! ", "Tente verificar as senhas clicando no icone de 'olho' ao lado do campo e comparando-as!", "danger");
        return false;
    }

}

$("[data-toggle=password]").on("click", function(){

    let input = $($(this).attr("data-target"));
    let img = $(this).prop("nodeName") !== "IMG" ? $(this).find("img") : $(this);

    if(input.attr("type") === "text"){

        input.attr("type", "password");
        img.attr("title", "Mostrar Senha").attr("src", "/assets/media/img/icons/closed_eye.png");

    }

    else{

        input.attr("type", "text");
        img.attr("title", "Esconder Senha").attr("src", "/assets/media/img/icons/eye.png");

    }

});