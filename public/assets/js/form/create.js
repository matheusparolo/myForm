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


[{"index":0,"statement":"Você é:","required":false,"type":"radio","options":[{"info":"homem","describe_allowed":false},{"info":"mulher","describe_allowed":false}]},{"index":1,"statement":"Sua idade:","required":false,"type":"radio","options":[{"info":"menor de 18 anos","describe_allowed":false},{"info":"entre 19-25","describe_allowed":false},{"info":"26-35","describe_allowed":false},{"info":"36-45","describe_allowed":false},{"info":"46-55","describe_allowed":false},{"info":"46-55","describe_allowed":false},{"info":"56-64","describe_allowed":false},{"info":"65+","describe_allowed":false}]},{"index":2,"statement":"Estado civil:","required":false,"type":"radio","options":[{"info":"casado(a)","describe_allowed":false},{"info":"solteiro(a) ","describe_allowed":false},{"info":"outro: ","describe_allowed":true}]},{"index":3,"statement":"Tem filhos?","required":false,"type":"radio","options":[{"info":"Sim","describe_allowed":false},{"info":"Não","describe_allowed":false}]},{"index":4,"statement":"Sua escolaridade:","required":false,"type":"radio","options":[{"info":"EF incompleto","describe_allowed":false},{"info":"EF completo","describe_allowed":false},{"info":"EM incompleto","describe_allowed":false},{"info":"EM completo","describe_allowed":false},{"info":"Superior incompleto","describe_allowed":false},{"info":"Superior completo","describe_allowed":false},{"info":"Pós-graduação incompleta","describe_allowed":false},{"info":"Pós-graduação completa","describe_allowed":false}]},{"index":5,"statement":"","required":false,"type":"radio","options":[{"info":"AC","describe_allowed":false},{"info":"AL","describe_allowed":false},{"info":"AM","describe_allowed":false},{"info":"AP","describe_allowed":false},{"info":"BA","describe_allowed":false},{"info":"CE","describe_allowed":false},{"info":"DF","describe_allowed":false},{"info":"ES","describe_allowed":false},{"info":"GO","describe_allowed":false},{"info":"MA","describe_allowed":false},{"info":"MG","describe_allowed":false},{"info":"MS","describe_allowed":false},{"info":"MT","describe_allowed":false},{"info":"PA","describe_allowed":false},{"info":"PB","describe_allowed":false},{"info":"PE","describe_allowed":false},{"info":"PI","describe_allowed":false},{"info":"PR","describe_allowed":false},{"info":"RJ","describe_allowed":false},{"info":"RN","describe_allowed":false},{"info":"RO","describe_allowed":false},{"info":"RR","describe_allowed":false},{"info":"RS","describe_allowed":false},{"info":"SC","describe_allowed":false},{"info":"SE","describe_allowed":false},{"info":"SP","describe_allowed":false},{"info":"TO","describe_allowed":false}]},{"index":6,"statement":"Atualmente você:","required":false,"type":"radio","options":[{"info":"Trabalha","describe_allowed":false},{"info":"Trabalha/estuda","describe_allowed":false},{"info":"Estuda","describe_allowed":false},{"info":"Outro:","describe_allowed":true}]},{"index":7,"statement":"Você é o principal responsável por comprar alimentos para sua casa?","required":false,"type":"radio","options":[{"info":"Sim","describe_allowed":false},{"info":"Não","describe_allowed":false}]},{"index":8,"statement":"Você é o principal responsável por escolher os alimentos para sua casa? ","required":false,"type":"radio","options":[{"info":"Sim","describe_allowed":false},{"info":"Não","describe_allowed":false}]},{"index":9,"statement":"Você acha importante ter uma alimentação saudável? ","required":false,"type":"radio","options":[{"info":"Sim","describe_allowed":false},{"info":"Não","describe_allowed":false}]},{"index":10,"statement":"Como você qualifica a preservação ambiental em Goioerê?","required":false,"type":"radio","options":[{"info":"Péssima","describe_allowed":false},{"info":"Ruim","describe_allowed":false},{"info":"Regular","describe_allowed":false},{"info":"Boa","describe_allowed":false},{"info":"Ótima","describe_allowed":false},{"info":"Não sabe/Não quis responder","describe_allowed":false}]},{"index":11,"statement":"Quando o assunto é risco ambiental em Goioerê, qual a primeira palavra que lhe vem à mente?","required":false,"type":"text","options":[]},{"index":12,"statement":"Você sabe o que são agrotóxicos?","required":false,"type":"radio","options":[{"info":"Sim","describe_allowed":false},{"info":"Não","describe_allowed":false}]},{"index":13,"statement":"Você pode citar o exemplo de um agrotóxico e para que ele é utilizado?","required":false,"type":"radio","options":[{"info":"Sim","describe_allowed":true},{"info":"Não","describe_allowed":false}]},{"index":14,"statement":"Você acredita que existe uma forma segura para o uso de agrotóxicos? ","required":false,"type":"radio","options":[{"info":"Sim","describe_allowed":false},{"info":"Não","describe_allowed":false}]},{"index":15,"statement":"Você sabe das pulverizações aéreas de feitas próximo a cidade? ","required":false,"type":"radio","options":[{"info":"Sim","describe_allowed":false},{"info":"Não","describe_allowed":false}]},{"index":16,"statement":"Você sabe o que é lançado dos aviões?","required":false,"type":"radio","options":[{"info":"Sim","describe_allowed":false},{"info":"Não","describe_allowed":false}]},{"index":17,"statement":"Para você, qual o risco para quem manuseia agrotóxicos em seu dia a dia no trabalho?","required":false,"type":"radio","options":[{"info":"Nenhum risco","describe_allowed":false},{"info":"Pouco risco","describe_allowed":false},{"info":"Baixo risco","describe_allowed":false},{"info":"Médio risco ","describe_allowed":false},{"info":"Alto risco ","describe_allowed":false},{"info":"Muito alto risco","describe_allowed":false}]},{"index":18,"statement":"Para você, qual o risco para quem consome alimentos produzidos com agrotóxicos?","required":false,"type":"radio","options":[{"info":"Nenhum risco","describe_allowed":false},{"info":"Pouco risco","describe_allowed":false},{"info":"Baixo risco","describe_allowed":false},{"info":"Médio risco ","describe_allowed":false},{"info":"Alto risco ","describe_allowed":false},{"info":"Muito alto risco","describe_allowed":false}]},{"index":19,"statement":"Para você, qual o risco para o meio ambiente (rios, solo, ar, animais e florestas) que os agrotóxicos oferecem?","required":false,"type":"radio","options":[{"info":"Nenhum risco","describe_allowed":false},{"info":"Pouco risco","describe_allowed":false},{"info":"Baixo risco","describe_allowed":false},{"info":"Médio risco ","describe_allowed":false},{"info":"Alto risco ","describe_allowed":false},{"info":"Muito alto risco","describe_allowed":false}]},{"index":20,"statement":"Você acredita que agrotóxicos podem causar doenças? ","required":false,"type":"radio","options":[{"info":"Sim","describe_allowed":false},{"info":"Não","describe_allowed":false}]},{"index":21,"statement":"Da lista abaixo, quais você acredita que podem ser causadas pela exposição/consumo de alimentos produzidos com agrotóxicos?","required":false,"type":"checkbox","options":[{"info":"Fraqueza","describe_allowed":false},{"info":"Cólicas abdominais","describe_allowed":false},{"info":"Náuseas/Vômitos","describe_allowed":false},{"info":"Espasmos musculares","describe_allowed":false},{"info":"Convulsões","describe_allowed":false},{"info":"Efeitos nervosos a longo prazo","describe_allowed":false},{"info":"Alterações genéticas","describe_allowed":false},{"info":"Dermatites/ irritações","describe_allowed":false},{"info":"Alergias diversas","describe_allowed":false},{"info":"Dor de cabeça","describe_allowed":false},{"info":"Lesões nos rins","describe_allowed":false},{"info":"Parkinson","describe_allowed":false},{"info":"Cânceres","describe_allowed":false},{"info":"Arritmias cardíacas","describe_allowed":false},{"info":"Má formação de fetos","describe_allowed":false},{"info":"Outra:","describe_allowed":true}]},{"index":22,"statement":"conhece alguém que já foi intoxicado por agrotóxicos? ","required":false,"type":"radio","options":[{"info":"Sim","describe_allowed":false},{"info":"Não","describe_allowed":false}]},{"index":23,"statement":"Você como se descarta a embalagem dos produtos agrotóxicos?","required":false,"type":"radio","options":[{"info":"Sim","describe_allowed":false},{"info":"Não","describe_allowed":false}]},{"index":24,"statement":"Você sabia que existem leis para normatizar o uso de agrotóxicos no Brasil e em Goioerê? ","required":false,"type":"radio","options":[{"info":"Sim","describe_allowed":false},{"info":"Não","describe_allowed":false}]},{"index":25,"statement":"Falando do meio ambiente em geral, na sua opinião, quais os riscos que o uso de agrotóxicos pode oferecer? ","required":false,"type":"checkbox","options":[{"info":"poluição de rios","describe_allowed":false},{"info":"poluição do solo","describe_allowed":false},{"info":"poluição do ar","describe_allowed":false},{"info":"perda de solo","describe_allowed":false},{"info":"danos a vegetação","describe_allowed":false},{"info":"danos a fauna","describe_allowed":false}]}]