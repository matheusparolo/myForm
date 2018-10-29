// Update
function update(){

    $("#form-update").submitter("/formulario/editar", {
        data : {
            "questions" : questions
        }
    });

}


// Push
function push_question(id, type, statement = "", required = false, options = []){

    let typeText, index;

    index = questions.length;

    questions.push(
        {
            "id" : id,
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
            insert_option(question, index, optionIndex, option["info"], option["describe_allowed"] === "1");

        }
    }

}


// Insert
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
                        .append($("<input>").attr("type", "checkbox").on("change", change_describe_allowed).prop("checked", describe_allowed))
                        .append($("<span>").text("Descrever"))
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
            )

        );

}


// Changes
function change_statement(){

    let index = search_parent_attr(this, "data-index");
    questions[index]["statement"] = $(this).prop("value");

}
function change_option_info(){

    let index = search_parent_attr(this, "data-index");
    let optionIndex = search_parent_attr(this, "data-option-index");
    questions[index]["options"][optionIndex]["info"] = $(this).val();

}
function change_required(){

    let index = search_parent_attr(this, "data-index");
    questions[index]["required"] = $(this).prop("checked");

}
function change_describe_allowed(){

    let index = search_parent_attr(this, "data-index");
    let optionIndex = search_parent_attr(this, "data-option-index");
    questions[index]["options"][optionIndex]["describe_allowed"] = $(this).prop("checked");

}
function change_index(){

    let index = search_parent_attr(this, "data-index");
    let newIndex = $(this).val() - 1;

    if(newIndex === "") $(this).val(index + 1);
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

            push_question(question["id"], type, question["statement"], question["required"], options);

        }

    }

}


// main
function init_vars(){

    responseCodes["000"] = "/pesquisa/" + $("#research-id").val();
    questions = [];

}
function get_data(){

    getJSON("/formulario/" + $("#form-id").val() + "/editar/json", function(data) {

        $("#name").val(data["name"]);
        let questions = data["questions"];

        questions.forEach(function(question){

            push_question(question["id"], question["type"], question["statement"], question["required"] === "1", question["options"]);

        });

    });

}
function binds(){

    $("#form-update").on("submit", update);

}


function main(){

    init_vars();
    get_data();
    binds();

}

window.onload = main;