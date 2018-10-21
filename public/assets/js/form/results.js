function next_anwer(){

    $("#input-index-answer").val(parseInt(last_answer) + 1);
    search();

}

function search(){

    clear_questions();

    let formID = $("#card-results").attr("data-id");
    let answerIndex = $("#input-index-answer").val();

    if(answerIndex !== ""){

        getData("/formulario/" + formID +"/resposta/" + answerIndex, [], function(data) {

            last_answer = answerIndex;
            $("#answer-index").text(" " + answerIndex);
            clear_questions();

            let questions = $(".question");

            if (data.length !== 0) {

                for (var i = 0; i < data.length; i++) {

                    let answer = data[i];
                    let question = $(questions[i]);
                    if (answer != []) {

                        if (question.attr("data-type") === "text") question.find("textarea").val(answer["answer"]);
                        else {

                            let options = question.find(".option");
                            for (var j = 0; j < options.length; j++) {

                                let option = $(options[j]);

                                for (var k = 0; k < answer["answer"].length; k++) {

                                    let answerK = answer["answer"][k];

                                    if (parseInt(option.attr("data-id")) === parseInt(answerK["id"])) {

                                        option.find("input").prop("checked", true);
                                        if (option.attr("data-describe") === "1") option.find("input")[1].value = answerK["describe_text"];

                                    }

                                }

                            }


                        }

                    }

                }

            } else {
                insert_alert("Indice não encontrado!", "O indice não corresponde a nenhuma resposta cadastrada..", "danger");
                clear_questions();
            }

        });

        $('html, body').animate({scrollTop: $("#questions").offset().top - 80}, 250);

    }
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
                    .prop("disabled", true)
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
                                .prop("disabled", true)
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
                            .prop("disabled", true)
                        )
                    )
                )

        });

    }


}



function randomColor(){
    return ("#" + Math.random().toString(16).slice(2, 8));
}

function new_chart(canvas, questionIndex, statement, answers, type){

    let i;

    let cicles = answers.length;

    let colors = [];
    let labels = [];
    let data = [];
    for(i = 0; i < cicles; i++){

        colors.push(randomColor());
        labels.push(answers[i].info);
        data.push(answers[i]["votes"]);

    }

    data = data.map(parseFloat);

    let config = {

        type: (type === "radio") ? "doughnut" : "bar",
        data: {
            datasets: [{
                data: data,
                backgroundColor: colors
            }],
            labels: labels
        },
        options: {
            responsive: true,
            legend: {
                display : (type === "radio"),
                position: 'bottom',
            },
            title: {
                display: false,
                text: questionIndex + ". " + statement
            },
            tooltips: {
                callbacks: {
                    label: function (tooltipItem, data) {
                        let dataset = data.datasets[tooltipItem.datasetIndex];
                        let currentValue = dataset.data[tooltipItem.index];
                        let percentage = Math.floor(((currentValue / cInterviewee) * 100) + 0.5);
                        return percentage + "% - " + currentValue + " votos";
                    }
                }
            }
        }
    };

    if(type === "checkbox") {

        config["options"]["scales"] = {yAxes: [{ticks: {beginAtZero:true}}]}

    }

    new Chart($(canvas)[0].getContext('2d'), config);

}

function change_view(){

    if($(this).attr("id") === "show-answers")
    {

        $("#answers").show();
        $(".card-footer").show();
        $("#results-card").hide();

    }else{

        $("#answers").hide();
        $(".card-footer").hide();
        $("#results-card").show();

    }

}

function main(){

    getData("/formulario/" + $("#card-results").attr("data-id") + "/resultados/json", [], function(data){

        $("#c-interviewee").text(" " + data["c_interviewee"]);
        cInterviewee = data["c_interviewee"];

        if(cInterviewee > 0)
        {

            let questions = data["questions"];
            questions.forEach(function(question){

                let answers = [];
                let allAnswers = data["answers"];

                allAnswers.forEach(function(answer){

                    if(answer["question_id"] === question["id"])
                        answers.push(answer);

                });

                $("#results")
                    .append($("<div>")
                        .addClass("col-card")
                        .append($("<div>")
                            .addClass("card")
                            .append($("<div>")
                                .addClass("card-header")
                                .text((parseInt(question.index) + 1) + ". " + question.statement)
                            )
                            .append($("<div>")
                                .addClass("card-body")
                                .append($("<canvas>")
                                    .attr("id", "question-" + question["id"])
                                )
                            )
                        )
                    );

                new_chart("#question-" + question["id"], question.index, question.statement, answers, question.type);


            });

        }

    });
    getData("/formulario/" + $("#card-results").attr("data-id") + "/json", [], function(data){

        let questions = data["questions"];

        questions.forEach(function(question){
            insert_question(question);
        });

        last_answer = 0;
        $("#next-answer").on("click", next_anwer);
        next_anwer();

    });

    $("#show-answers, #show-results").on("click", change_view);

    $("#search-answer").on("click", search);
    $("#next-answer").on("click", next_anwer);

}

window.onload = main;