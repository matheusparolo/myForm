// Submit
function submit_add(){

    let cpf = ($("#cpf").val()).replace(/\./g, '').replace(/\-/g, '');

    if(verify_cpf(cpf)){

        if(answers){

            // Init login alert
            insert_loading();

            // Disable button submit
            let submit = $("#form-add").find("input[type='submit']");
            change_disabled(submit, true);

            // Submit
            var formData = new FormData();
            formData.append("id", $("#form-id").val());
            formData.append("cpf", cpf);
            formData.append("answers", JSON.stringify(answers));

            for(var id in audios)
                formData.append("question_" + id, audios[id], "question_" + id);

            $.ajax({
                type: 'POST',
                url: "/formulario/resposta/enviar",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json"
            }).done(function (responseData) {

                // Response operations
                show_response(responseData["code"], false);
                change_disabled(submit, false);
                clear_questions();

                $('html, body').animate({scrollTop: $("#questions").offset().top - 80}, 250);
                $("#modal-terms").modal("hide");

            });

        }
    }else{

        insert_alert("CPF invalido!", "Verifique as informações e tente novamente.", "danger");

    }

}
function verifyRequireds(){

    let questions = $(".question");

    let answers = [];
    let notSubmit = false;

    if(recording)
    {
        insert_alert("Ainda há uma gravação em andamento!", "Finalize todas as gravações para que possa continuar.", "warning");
        return notSubmit;
    }

    for(var i = 0; i < questions.length; i++) {

        let question = $(questions[i]);

        let id = question.attr("data-id");
        let type = question.attr("data-type");
        let required = question.attr("data-required") === "1";

        let block = false;
        let answer;
        if (type === "text") {

            if (required) {

                let audioButton = question.find("img");

                if (audios[id] === undefined) {

                    insert_alert("Campos obrigatórios não informados!", "Informe todos os campos marcados como obrigatórios para que possa continuar.", "warning");

                    $('html, body').animate({scrollTop: $(question).offset().top - 80}, 250);

                    block = true;

                }else{

                    answers.push(
                        {
                            "id": id,
                            "type": "text",
                            "audioLink": "/private/assets/audio/" + $("#form-id").val()
                        });

                }

            } else {

                if (audios[id] !== undefined) {

                    answers.push(
                        {
                            "id": id,
                            "type": "text",
                            "audioLink": "/private/assets/audio/" + $("#form-id").val()
                        });

                }


            }

        } else {

            answer = [];
            let options = question.find(".option");
            for (var j = 0; j < options.length; j++) {

                let option = $(options[j]);
                let checked = option.find("input").prop("checked");
                if (checked) {

                    let id = option.attr("data-id");
                    let describe = option.attr("data-describe");
                    if (describe === "1") {

                        let input = $(option.find("input")[1]);
                        describe = input.val();
                        if (!describe) {

                            insert_alert("Campos obrigatórios não informados!", "Informe todos os campos marcados como obrigatórios para que possa continuar.", "warning");

                            notSubmit = true;

                            $('html, body').animate({scrollTop: $(question).offset().top - 80}, 250);

                            block = true;
                            break;

                        }

                    } else {
                        describe = [];
                    }

                    answer.push({
                        "id": id,
                        "describe": describe
                    });

                    if (type === "radio") break;

                }

            }
            if (required && answer == "") {

                insert_alert("Campos obrigatórios não informados!", "Informe todos os campos marcados como obrigatórios para que possa continuar.", "warning");

                notSubmit = true;

                $('html, body').animate({scrollTop: $(question).offset().top - 80}, 250);

                block = true;

            }

            if (answer != "") {
                answers.push(
                    {
                        "id": id,
                        "type": type,
                        "answer": answer
                    });
            }

        }
        if (block) {
            notSubmit = true;
            break;
        }

    }

    if(notSubmit)
        return false;
    else
        return answers;

}
function clear_questions(){

    let questions = $(".question");
    $("#cpf").val("");
    $("#accept-terms").prop("checked", false);
    for(var i = 0; i < questions.length; i++)
    {

        let question = $(questions[i]);
        if(question.attr("data-type") === "text")
        {
            question.find(".status").html("");
            audios = {};
        }
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


// Mic
function change_recorder(){

    if($(this).attr("data-recording") === "1")
    {

        $(this)
            .attr("data-recording", 0)
            .removeClass("recording");
        $($(this).parent()).find(".status")
            .removeClass("mt-3");

        recorder.stop();

    }else{

        if(micAllowed){

            if(!recording){

                let id = search_parent_attr(this, "data-id");
                if(audios[id] === undefined || confirm("Já existe uma resposta cadastrada para essa questão, deseja mesmo sobreescreve-la?") ){

                    recording = id;

                    $(this)
                        .attr("data-recording", 1)
                        .addClass("recording");

                    $($(this).parent()).find(".status")
                        .html("")
                        .text("Gravando...")
                        .addClass("mt-3");

                    chunks = [];
                    recorder.start();

                }

            }else{

                insert_alert("Ainda há uma gravação em andamento!", "Finalize todas as gravações para que possa continuar.", "warning");

            }

        }else{

            insert_alert("O microfone não está habilitado para uso!", "Habilite o acesso ao microfone para gravar audios.<br>", "warning");

        }

    }

}
function request_mic(){

    media = {
        tag: 'audio',
        type: 'audio/ogg',
        ext: '.ogg',
        gUM: {audio: true}
    };

    navigator.mediaDevices.getUserMedia(media.gUM).then(_stream => {

        micAllowed = true;

        stream = _stream;
        recorder = new MediaRecorder(stream);
        recorder.ondataavailable = e => {
            chunks.push(e.data);
            if(recorder.state == 'inactive'){

                audios[recording] = new Blob(chunks, {type: media.type});
                $(".question[data-id=" + recording + "]").find(".status").html("")
                    .append($("<audio controls>")
                        .append($("<source>")
                            .attr("src", URL.createObjectURL(audios[recording]))
                            .attr("type", "audio/ogg")
                        )
                    );

                recording = false;

            }
        };

    }).catch(function(){

        micAllowed = false;

    });

}


// Insert
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

                .append($("<div>")
                    .addClass("recorder")
                    .addClass("mb-3")
                    .append($("<img>")
                        .attr("data-recording", "0")
                        .attr("src", "/assets/media/img/icons/mic.png")
                        .addClass("mic")
                        .attr("title", "Gravar")
                        .on("click", change_recorder)
                    )
                    .append($("<span>")
                        .addClass("status")
                    )
                )
                .append($("<div>")
                    .append($("<p>")
                        .append($("<small>")
                            .addClass("text-muted")
                            .text("É necessário gravar o audio para enviar respostas de perguntas abertas. Você poderá transcreve-las em texto após serem cadastradas.")
                        )
                    )
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

// Validations
function verify_cpf(cpf){

    if(cpf.length === 11){
        var rest, sum;

        sum = 0;
        if (cpf === "00000000000" ||
            cpf === "11111111111" ||
            cpf === "22222222222" ||
            cpf === "33333333333" ||
            cpf === "44444444444" ||
            cpf === "55555555555" ||
            cpf === "66666666666" ||
            cpf === "77777777777" ||
            cpf === "88888888888" ||
            cpf === "99999999999"){

            return false;

        }
        for (i=1; i<=9; i++) {

            sum = sum + parseInt(cpf.substring(i - 1, i)) * (11 - i);

        }

        rest = (sum * 10) % 11;

        if ((rest === 10) || (rest === 11)){

            rest = 0;

        }

        if (rest !== parseInt(cpf.substring(9, 10)) ){

            return false;

        }

        sum = 0;
        for (var i = 1; i <= 10; i++){

            sum = sum + parseInt(cpf.substring(i-1, i)) * (12 - i);

        }

        rest = (sum * 10) % 11;

        if ((rest === 10) || (rest === 11)){

            rest = 0;

        }

        return rest === parseInt(cpf.substring(10, 11));
    }else{

        return false;

    }

}


// main
function init_vars(){

    responseCodes["000"] = ["Resposta cadastrada com sucesso!", "Continue cadastrando respostas."];
    audios = {};
    recording = false;
    micAllowed = false;

}
function get_data(){

    getJSON("/formulario/" + $("#form-id").val() + "/resposta/enviar/json", function(data){

        $("#form-name").text(data["name"]);
        $("#terms").val(data["terms"]);
        let questions = data["questions"];

        questions.forEach(function(question){
            insert_question(question);
        });

    });

}
function binds(){

    $("#form-add").on("submit", function(){
        answers = verifyRequireds();
        if(answers){
            $("#modal-terms").modal("show");
        }
    });
    $("#form-terms").on("submit", submit_add)

}
function masks(){

    $(document).ready(function() {

        $('#cpf').mask('000.000.000-00');

    });

}

function main(){

    init_vars();
    get_data();
    binds();
    masks();
    request_mic();

}

window.onload = main;