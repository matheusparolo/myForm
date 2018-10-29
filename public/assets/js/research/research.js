// Form Actions
function insert_form(form){

    $("#table-forms")
        .append($("<tr>")

            .attr("data-form-id", form["id"])
            .append($("<td>")
                .text(form["name"])
            )

            .append($("<td>")
                .append($("<a>")
                    .addClass("action")
                    .attr("title", "Enviar respostas")
                    .attr("href", "/formulario/" + form["id"] + "/resposta/enviar")
                    .append($("<img>")
                        .attr("src", "/assets/media/img/icons/submit-form.png")
                    )
                )
            )

            .append($("<td>")
                .append($("<a>")
                    .addClass("action")
                    .attr("title", "Ver resultados")
                    .attr("href", "/formulario/" + form["id"] + "/resultados")
                    .append($("<img>")
                        .attr("src", "/assets/media/img/icons/chart.png")
                    )
                )
            )

            .append($("<td>")
                .append($("<a>")
                    .addClass("action")
                    .attr("title", "Editar")
                    .attr("href", "/formulario/" + form["id"] + "/editar")
                    .append($("<img>")
                        .attr("src", "/assets/media/img/icons/edit.png")
                    )
                )
            )
            .append($("<td>")
                .append($("<img>")
                    .addClass("action")
                    .attr("title", "Remover")
                    .on("click", remove_form)
                    .attr("src", "/assets/media/img/icons/delete.png")
                )
            )

        )

}
function remove_form() {

    send_remove(this, "/formulario/deletar", "data-form-id");

}

// Main
function init_vars(){

    responseCodes["000"] = "";

}
function get_data(){

    getJSON("/pesquisa/" + $("#research").attr("data-id") + "/json", function(data){

        let research = data["research"];
        let creator = data["creator"];
        let forms = data["forms"];

        $("#name").text(research["name"]);
        $("#area").text(research["area"]);
        $("#overview").text(research["overview"]);
        $("#creator-name").text(creator["name"]);

        forms.forEach(function(form){

            insert_form(form);

        })

    })
}

function main(){

    init_vars();
    get_data();

}

window.onload = main;