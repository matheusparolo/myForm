function show_response(code, redirect = true){

    switch (code) {
        case "000":
            (redirect) ? window.location.replace(responseCodes["000"]) : insert_alert(responseCodes[code][0], responseCodes[code][1], "success");
            break;
        case "300":
            window.location.replace("/usuario");
            break;
        default:
            insert_alert(responseCodes[code][0], responseCodes[code][1], "danger");
            break;
    }

}
function change_disabled(btn, disable){

    $(btn).prop("disabled", disable);

}
function verifyRequireds(form){

    let toReturn = true;
    let inputs = $(form).find("[data-required='1']");

    for(var i = 0; i < inputs.length; i++)
    {

        let input = $(inputs[i]);
        if(input.val() === "")
        {

            input.attr("title", "Campo obrigatório!").attr("data-placement", "bottom").tooltip("enable").tooltip('show');

            window.setTimeout(function(){
                input.removeAttr("title").tooltip("hide").tooltip("disable");
            }, 3000);

            $('html, body').animate({scrollTop: $(input).offset().top - 80}, 250);

            toReturn = false;
            break;

        }

    }
    return toReturn;

}
function search_parent_attr(element, attr){

    while($(element).attr(attr) === undefined) element = element.parentNode;
    return $(element).attr(attr);

}

// Submits
function submit_form(form, url, data = [], redirect = true, callbackData = null, callbackForm = null){

    // Init
    if(callbackData === null)
        callbackData = function(){return true};

    if(callbackForm === null)
        callbackForm = function(){};

    // Verify inputs requireds
    if(verifyRequireds(form))
    {

        // Init login alert
        insert_loading();

        // Disable button submit
        let submit = $(this).find("input[type='submit']");
        change_disabled(submit, true);

        // Make form Data
        let formData = $(form).serializeArray();
        formData = formData.concat(data);

        if(callbackData(formData)){

            // Submit
            $.post(url, formData, function(responseData){

                // Response operations
                show_response(responseData["code"], redirect);
                change_disabled(submit, false);

                // Callback
                callbackForm(responseData);

            }, "json");

        }else{

            // Enable Button submit
            remove_loading();
            change_disabled(submit, false);

        }

    }

}
function getData(url, formData, callback){

    $.get(url, formData, function(responseData) {

        if(responseData["code"] !== undefined){
            show_response(responseData["code"]);
        }
        else{
            callback(responseData);
        }

    }, "json");

}
function send_remove(element, url, attr = "data-id"){

    if(confirm("Tem certeza que deseja deletar? Essa operação não poderá ser desfeita.")){

        let id = search_parent_attr(element, attr);
        $.post(url, {
            "id" : id
        }, function(data){

            show_response(data["code"])

        }, "json");

    }

}

// Side label / alerts
function insert_alert(strongText = "", message = "", alertType = "info"){

    $("#alert").hide().html("")
        .append($("<div>")
            .addClass("alert")
            .addClass("alert-" + alertType)
            .addClass("alert-dismissible")
            .addClass("fade")
            .addClass("show")
            .append($("<button>")
                .addClass("close")
                .attr("data-dismiss", "alert")
                .html("&times;")
            )
            .append($("<span>")

                .append($("<strong>").html(strongText))
                .append($("<span>").html(message))
            )
        ).fadeIn(250);

}
function insert_loading(){

    $("#alert").hide().html("")
        .append($("<div>")
            .addClass("alert")
            .addClass("alert-info")
            .addClass("alert-loading")
            .addClass("alert-dismissible")
            .addClass("fade")
            .addClass("show")
            .append($("<span>")

                .append($("<div>").addClass("alert-loading-spinner"))
                .append($("<strong>").text("Aguarde!"))

            )
        ).fadeIn(250)


}
function remove_loading(){
    $("#alert").find(".alert-loading").remove();
}

responseCodes = {
    "200" : ["Ops! Parece que estamos passando por problemas tecnicos.", "Tente novamente mais tarde."]
};