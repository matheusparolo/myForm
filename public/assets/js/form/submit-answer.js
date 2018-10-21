function submit_add(){

    // Verify inputs requireds
    let answers = verifyRequireds();
    if(answers)
    {

        // Init login alert
        insert_loading();

        // Disable button submit
        let submit = $(this).find("input[type='submit']");
        change_disabled(submit, true);

        // Submit
        $.post("/formulario/resposta/enviar", {
            "id" : $("#form-id").val(),
            "answers" : answers
        }, function(responseData){

            // Response operations
            show_response(responseData["code"], false);
            change_disabled(submit, false);
            clear_questions();

            $('html, body').animate({scrollTop: $("#questions").offset().top - 80}, 250);

        }, "json");

    }

}

function verifyRequireds(){

    let questions = $(".question");

    let answers = [];
    let notSubmit = false;

    for(var i = 0; i < questions.length; i++)
    {

        let question = $(questions[i]);

        let id = question.attr("data-id");
        let type = question.attr("data-type");
        let required = question.attr("data-required") === "1";
        let index = question.attr("data-index");

        let block = false;
        let answer;
        if(type === "text")
        {

            let input = question.find("textarea");
            answer = input.val();

            if(required && answer == ""){

                input.attr("title", "Campo obrigatório!").attr("data-placement", "bottom").tooltip("enable").tooltip('show');

                window.setTimeout(function(){
                    input.removeAttr("title").tooltip("hide").tooltip("disable");
                }, 3000);

                $('html, body').animate({scrollTop: $(question).offset().top - 80}, 250);

                block = true;

            }

        }else{

            answer = [];
            let options = question.find(".option");
            for(var j = 0; j < options.length; j++)
            {

                let option = $(options[j]);
                let checked = option.find("input").prop("checked");
                if(checked)
                {

                    let id = option.attr("data-id");
                    let describe = option.attr("data-describe");
                    if(describe === "1")
                    {

                        let input = $(option.find("input")[1]);
                        describe = input.val();
                        if(!describe){

                            input.attr("title", "Campo obrigatório!").attr("data-placement", "bottom").tooltip("enable").tooltip('show');

                            window.setTimeout(function(){
                                input.removeAttr("title").tooltip("hide").tooltip("disable");
                            }, 3000);

                            notSubmit = true;

                            $('html, body').animate({scrollTop: $(question).offset().top - 80}, 250);

                            block = true;
                            break;

                        }

                    }else{
                        describe = [];
                    }

                    answer.push({
                        "id" : id,
                        "describe" : describe
                    });

                    if(type === "radio") break;

                }

            }
            if(required && answer == "")
            {

                question.attr("title", "Questão obrigatório!").attr("data-placement", "bottom").tooltip("enable").tooltip('show');

                window.setTimeout(function(){
                    question.removeAttr("title").tooltip("hide").tooltip("disable");
                }, 3000);

                notSubmit = true;

                $('html, body').animate({scrollTop: $(question).offset().top - 80}, 250);

                block = true;

            }

        }

        if(block){
            notSubmit = true;
            break;
        }else{
            if(answer != ""){
                answers.push(
                    {
                        "id" : id,
                        "type" : type,
                        "answer" : answer
                    }
                );
            }
        }
    }
    if(notSubmit)
        return false;
    else
        return answers;

}

function clear_questions(){

    let questions = $(".question");
    for(var i = 0; i < questions.length; i++)
    {

        let question = $(questions[i]);
        if(question.attr("data-type") === "text") question.find("textarea").val("");
        else{

            let options = question.find(".option");
            for(var j = 0; j < options.length; j++)
            {


                let option = $(options[j]);

                option.find("input").prop("checked", false);
                if(option.attr("data-describe") === "1") option.find("input")[1].value = "";

            }

        }

    }

}

function insert_question(question){

    let id = question["id"];
    let index = parseInt(question["index"]);
    let statement = question["statement"];
    let required = question["required"];
    let type = question["type"];
    let options = question["options"] !== undefined ? question["options"] : [];

    $("#questions")
        .append($("<div>")
            .attr("data-id", id)
            .attr("data-type", type)
            .attr("data-required", required)
            .attr("data-index", index)
            .addClass("question")
            .addClass("card")
            .append($("<div>")
                .addClass("question-header")
                .addClass("card-header")
                .addClass("pb-0")
                .append($("<div>")
                    .addClass("question-statement")
                    .append($("<p>")
                        .text((index + 1) + ". " + statement + " " + (required === "1" ? "(Obrigatória)" : ""))
                    )
                )
                .append($("<div>")
                    .addClass("card-body")
                    .addClass("options")
                )
            )
        );

    question = $($("#questions").children()[index]);
    if(type === "text")
    {

        question.find(".options")
            .append($("<div>")
                .addClass("form-group")
                .addClass("mb-3")
                .append($("<textarea>")
                    .addClass("form-control")
                    .attr("rows", 3)
                    .attr("placeholder", "Resposta")
                )
            )

    }else{

        options.forEach(function(option){

            let optionID = option["id"];
            let info = option["info"];
            let describeAllowed = option["describe_allowed"];

            question.find(".options")
                .append($("<div>")
                    .attr("data-id", optionID)
                    .attr("data-describe", describeAllowed)
                    .addClass("option")
                    .addClass("pb-3")
                    .append($("<div>")
                        .addClass("input-group")
                        .append($("<div>")
                            .addClass("custom-control")
                            .addClass((type === "checkbox") ? "custom-checkbox" : "custom-radio")
                            .addClass("pt-1")
                            .addClass("pr-4")
                            .append($("<input>")
                                .addClass("custom-control-input")
                                .attr("type", type)
                                .attr("id", "option-" + optionID)
                                .attr("name", "question-" + id)
                            )
                            .append($("<label>")
                                .addClass("custom-control-label")
                                .attr("for", "option-" + optionID)
                                .text(info)
                            )
                        )
                        .append($("<input>")
                            .css("display", (describeAllowed === "1") ? "unset" : "none")
                            .addClass("form-control")
                            .attr("type", "text")
                            .attr("placeholder", "Descreva")
                            .attr("max-length", "256")
                        )
                    )
                )

        });

    }


}

function main(){

    responseCodes["000"] = ["Resposta cadastrada com sucesso!", "Continue cadastrando respostas."];

    getData("/formulario/" + $("#form-id").val() + "/json", function(data){

        $("#form-name").text(data["name"]);
        let questions = data["questions"];

        questions.forEach(function(question){
            insert_question(question);
        });

    });

    $("#form-add").on("submit", submit_add)

}

window.onload = main;