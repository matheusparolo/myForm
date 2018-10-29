function show_response(code, redirect){

    switch (code) {
        case "000":
            (redirect) ? window.location.replace(responseCodes["000"]) : insert_alert(responseCodes[code][0], responseCodes[code][1], "success");
            break;
        case "300":
            window.location.replace("/usuario");
            break;
        case "400":
            window.location.replace("/pesquisas");
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

        let input = inputs[i];
        a = input;
        if($(input).val() === "")
        {

            input.reportValidity();

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
function send_remove(element, url, attr = "data-id"){

    if(confirm("Tem certeza que deseja deletar? Essa operação não poderá ser desfeita.")){

        let id = search_parent_attr(element, attr);

        insert_loading();
        $.post(url, {
            "id" : id
        }, function(data){

            remove_loading();
            show_response(data["code"], true);

        }, "json");

    }

}
function getJSON(url, callback){

    insert_loading();
    $.get(url, function(responseData){

        remove_loading();
        if(responseData["code"] !== undefined)
            show_response(responseData["code"]);

        else
            callback(responseData);

    }, "json");

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

// Prototypes
$.prototype.serializeJSON = function(){

    let serialized = this.serializeArray();
    let toReturn = {};

    serialized.forEach(function(field){

        toReturn[field["name"]] = field["value"];

    });

    return toReturn;

};
$.prototype.submitter = function(url, inputOptions = {}){

    if(this.prop("nodeName") === "FORM"){

        let optionsDefault = {

            redirect : true,
            useSerialize : true,
            data : [],
            callbackData : function(){return true},
            callbackForm : function(){},

        };
        let options = {};
        $.extend(options, optionsDefault, inputOptions);

        // Init login alert
        insert_loading();

        // Disable button submit
        let submit = this.find("input[type='submit']");
        change_disabled(submit, true);

        // Make form Data
        let formSerialized = options["useSerialize"] ? this.serializeJSON() : {};
        let formData = {};
        $.extend(formData, formSerialized, options["data"]);

        if(options["callbackData"](formData)){

            // Submit
            $.post(url, formData, function(responseData){

                // Response operations
                show_response(responseData["code"], options["redirect"]);
                change_disabled(submit, false);

                // Callback
                options["callbackForm"](responseData);

            }, "json");

        }else{

            // Enable Button submit
            remove_loading();
            change_disabled(submit, false);

        }

    }

};

// Inits
responseCodes = {
    "200" : ["Ops! Parece que estamos passando por problemas tecnicos.", "Tente novamente mais tarde."]
};