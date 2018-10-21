function remove_form() {

    send_remove(this, "/formulario/deletar", "data-form-id");

}
function main(){

    responseCodes["000"] = "";

    $(".action-i img, .action-t").tooltip({placement: "bottom"});

    getData("/pesquisa/" + $("#research").attr("data-id") + "/json", null, function(data){

        let research = data["research"];
        let creator = data["creator"];
        let forms = data["forms"];

        $("#name").text(research["name"]);
        $("#area").text(research["area"]);
        $("#overview").text(research["overview"]);
        $("#creator-name").text(creator["name"]);

        forms.forEach(function(form){

            $("#table-forms")
                .append($("<tr>")

                    .attr("data-form-id", form["id"])
                    .append($("<td>")
                        .text(form["name"])
                    )

                    .append($("<td>")
                        .addClass("action-i")
                        .append($("<a>")
                            .attr("href", "/formulario/" + form["id"] + "/resposta/enviar")
                            .append($("<img>")
                                .attr("title", "Enviar respostas")
                                .attr("src", "/assets/media/img/icons/submit-form.png")
                                .attr("data-placement", "bottom")
                                .tooltip("enable")
                            )
                        )
                    )

                    .append($("<td>")
                        .addClass("action-i")
                        .append($("<a>")
                            .attr("href", "/formulario/" + form["id"] + "/resultados")
                            .append($("<img>")
                                .attr("title", "Ver resultados")
                                .attr("src", "/assets/media/img/icons/chart.png")
                                .attr("data-placement", "bottom")
                                .tooltip("enable")
                            )
                        )
                    )

                    .append($("<td>")
                        .addClass("action-i")
                        .append($("<a>")
                            .attr("href", "/formulario/" + form["id"] + "/editar")
                            .append($("<img>")
                                .attr("title", "Editar")
                                .attr("src", "/assets/media/img/icons/edit.png")
                                .attr("data-placement", "bottom")
                                .tooltip("enable")
                            )
                        )
                    )
                    .append($("<td>")
                        .addClass("action-i")
                            .append($("<img>")
                                .attr("title", "Remover")
                                .attr("src", "/assets/media/img/icons/delete.png")
                                .attr("data-placement", "bottom")
                                .tooltip("enable")
                                .on("click", remove_form)
                            )
                    )

                )

        })

    })

}

window.onload = main;