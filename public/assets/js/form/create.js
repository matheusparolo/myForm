function create(){

    submit_form("#form-create", "/formulario/criar", [{"name" : "questions", "value" : JSON.stringify(questions)}]);

}

function search_index(element, attr){

    while($(element).attr(attr) === undefined) element = element.parentNode;
    return $(element).attr(attr);

}


function change_statement(){

    let index = search_index(this, "data-index");
    questions[index]["statement"] = $(this).prop("value");

}
function change_option_info(){

    let index = search_index(this, "data-index");
    let optionIndex = search_index(this, "data-option-index");
    questions[index]["options"][optionIndex]["info"] = $(this).val();

}


function change_required(){

    let index = search_index(this, "data-index");
    questions[index]["required"] = $(this).prop("checked");

}
function change_describe_allowed(){

    let index = search_index(this, "data-index");
    let optionIndex = search_index(this, "data-option-index");
    questions[index]["options"][optionIndex]["describe_allowed"] = $(this).prop("checked");

}
function change_index(){

    let index = search_index(this, "data-index");
    let newIndex = $(this).val() - 1;

    if (newIndex === "") $(this).val(index + 1);
    else if(index !== newIndex) {

        let question, type, options, temp;

        questions.splice(newIndex, 0, questions.splice(index, 1)[0]);
        temp = questions;
        questions = [];

        $("#questions").html("");
        for (var i = 0; i < temp.length; i++) {

            question = temp[i];
            type = question["type"];
            options = type !== "text" ? question["options"] : null;

            push_question(type, question["statement"], question["required"], options);

        }

    }

}


function drop_question(){

    let index = search_index(this, "data-index");
    questions.splice(index, 1);

    $($("#questions").children()[index]).remove();

    let questionsHtml = $(".question");
    for(var i = index; i < questions.length; i++)
    {

        questions[i]["index"] = parseInt(i);
        $(questionsHtml[i]).find(".question-index").prop("value", parseInt(i) + 1);
        $(questionsHtml[i]).attr("data-index", parseInt(i));

    }

}
function drop_option(){

    let index = search_index(this, "data-index");
    let optionIndex = search_index(this, "data-option-index");
    let options = questions[index]["options"];
    let optionsHTML = $($("#questions").children()[index]).find(".options");

    options.splice(optionIndex, 1);

    $(optionsHTML).children()[optionIndex].remove();

    for(var i = index; i < options.length; i++)
    {

        $($(optionsHTML).children()[i]).attr("data-option-index", i);

    }

}


function new_question(event){

    let type = $(event.currentTarget).attr("data-type");
    push_question(type);

    let questionIndex = questions.length - 1;
    let question = $("#questions").children()[questionIndex];

    if(type !== "text")
        push_option(question, questionIndex);

}

function new_option(){

    let questionIndex = search_index(this, "data-index");
    let question = $("#questions").children()[questionIndex];

    push_option(question, questionIndex);

}


function push_question(type, statement = "", required = false, options = []){

    let typeText, index;

    index = questions.length;

    questions.push(
        {
            "index" : index,
            "statement" : statement,
            "required" : required,
            "type" : type,
            "options" : options
        }
    );

    if(type === "text") typeText = "Tipo: Texto";
    else if(type === "checkbox") typeText = "Tipo: Multiplas Opções";
    else typeText = "Tipo: Unica Opção";

    insert_question(type, index, statement, required, typeText);

    if(type !== "text"){

        let question = $(".question")[index];
        let option;
        for(var optionIndex = 0; optionIndex < options.length; optionIndex++)
        {

            option = options[optionIndex];
            insert_option(question, index, optionIndex, option["info"], option["describe_allowed"]);

        }
    }

}
function push_option(question, questionIndex, info = "", describe_allowed = false){

    let optionIndex = questions[questionIndex]["options"].length;
    questions[questionIndex]["options"].push({
        "info" : info,
        "describe_allowed" : describe_allowed
    });
    insert_option(question, questionIndex, optionIndex, info, describe_allowed);

}


function insert_question(type, index, statement, required, typeText){

    let hide_options = type !== "text";

    $("#questions")
        .append($("<div>")
            .addClass("question")
            .addClass("card")
            .attr("data-index", index)

            .append($("<div>")
                .addClass("question-header")
                .addClass("card-header")

                .append($("<div>")
                    .addClass("question-type")
                    .append($("<p>").text(typeText))
                )
                .append($("<div>")
                    .addClass("question-actions")
                    .addClass("float-right")
                    .append($("<div>")
                        .addClass("switch")
                        .addClass("mr-4")
                        .append($("<label>")
                            .append($("<input>")
                                .attr("type", "checkbox")
                                .prop("checked", required)
                                .on("change", change_required)
                            )
                            .append($("<span>").text("Obrigatório"))
                        )
                    )
                    .append($("<div>")
                        .addClass("action-i")
                        .append($("<img>")
                            .attr("title", "Remover")
                            .attr("src", "/assets/media/img/icons/delete.png")
                            .tooltip({"placement" : "bottom"})
                            .on("click", drop_question)
                        )
                    )
                )
                .append($("<div>")
                    .addClass("question-statement")
                    .addClass("input-group")
                    .addClass("mb-3")
                    .append($("<div>")
                        .addClass("input-group-prepend")
                        .append($("<input>")
                            .addClass("form-control")
                            .addClass("question-index")
                            .addClass("index")
                            .attr("type", "number")
                            .attr("placeholder", "Indice")
                            .attr("max-length", "256")
                            .attr("data-required", "1")
                            .prop("value", index + 1)
                            .on("blur", change_index)
                        )
                    )
                    .append($("<input>")
                        .addClass("form-control")
                        .addClass("statement")
                        .attr("type", "text")
                        .attr("placeholder", "Enunciado da Questão")
                        .attr("maxlength", "256")
                        .attr("data-required", "1")
                        .prop("value", statement)
                        .on("blur", change_statement)
                    )
                )
            )

            .append($("<div>")
                .css("display", hide_options ? "unset" : "none")
                .addClass("card-body")
                .addClass("options")
            )
            .append($("<div>")
                .css("display", hide_options ? "unset" : "none")
                .addClass("card-footer")
                .append($("<input>")
                    .addClass("btn")
                    .addClass("btn-secondary")
                    .addClass("float-right")
                    .attr("type", "button")
                    .prop("value", "Nova Opção")
                    .on("click", new_option)
                )
            )

        );

}
function insert_option(question, questionIndex, optionIndex, info, describe_allowed){

    $(question).find(".options")
        .append($("<div>")
            .attr("data-option-index", optionIndex)
            .addClass("option")
            .addClass("pb-3")
            .append($("<div>")
                .addClass("input-group")
                .append($("<div>")
                    .addClass("switch")
                    .addClass("mr-3")
                    .addClass("mt-2")
                    .append($("<label>")
                        .append($("<input>").attr("type", "checkbox").on("change", change_describe_allowed))
                        .append($("<span>").text("Descrever"))
                        .prop("checked", describe_allowed)
                    )
                )
                .append($("<input>")
                    .addClass("form-control")
                    .attr("type", "text")
                    .attr("placeholder", "Opção")
                    .attr("maxlength", "256")
                    .attr("data-required", "1")
                    .on("blur", change_option_info)
                    .val(info)
                )
                .append($("<div>")
                    .addClass("pl-3")
                    .addClass("pt-2")
                    .addClass("action-i")
                    .append($("<img>")
                        .attr("title", "Remover")
                        .attr("src", "/assets/media/img/icons/delete.png")
                        .tooltip({"placement" : "bottom"})
                        .on("click", drop_option)
                    )
                )
            )

        );

}

function main(){

    responseCodes["000"] = "/pesquisa/" + $("#research-id").val();

    questions = [];

    $("#add-question-text, #add-question-checkbox, #add-question-radio").on("click", new_question);

    $(".action-i img, .action-t").tooltip({placement: "bottom"});

    $("#form-create").on("submit", create);

}

window.onload = main;