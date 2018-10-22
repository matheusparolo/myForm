function insert_researches(data){

    let userID = data["userID"];

    let researchesData = data["researches"];
    let tableResearches = $("#researches");

    researchesData.forEach(function(researchData){

        let research = researchData["research"];
        let user = researchData["user"];

        a = userID;
        b = research["creatorId"];

        tableResearches
            .append($("<tr>")

                .attr("data-id", research["id"])

                .append($("<td>")

                    .addClass("action-t")
                    .attr("title", "Visualizar")
                    .attr("data-placement", "bottom")
                    .tooltip("enable")
                    .text(research["name"])
                    .on("click", function(){
                        window.location.replace("/pesquisa/" + research["id"])
                    })

                )
                .append($("<td>")

                    .addClass("action-t")
                    .attr("title", "Visualizar")
                    .attr("data-placement", "bottom")
                    .tooltip("enable")
                    .text(userID === research["creatorId"] ? "você" : user["name"])
                    .on("click", function(){
                        window.location.replace("/pesquisa/" + research["id"])
                    })

                )
                .append($("<td>")

                    .addClass("action-t")
                    .attr("title", "Visualizar")
                    .attr("data-placement", "bottom")
                    .tooltip("enable")
                    .text(research["applicationArea"])
                    .on("click", function(){
                        window.location.replace("/pesquisa/" + research["id"])
                    })

                )
                .append($("<td>")

                    .addClass("action-i")
                    .append($("<a>")

                        .css({"display" : (userID == research["creatorId"] ? "block" : "none")})
                        .attr("href", "/pesquisa/" + research["id"] + "/editar")
                        .append($("<img>")
                            .attr("src", "/assets/media/img/icons/edit.png")
                            .attr("title", "Editar")
                            .tooltip({placement: "bottom"})
                        )

                    )

                )
                .append($("<td>")

                    .addClass("action-i")
                    .append($("<span>")

                        .css({"display" : (userID == research["creatorId"] ? "block" : "none")})
                        .append($("<img>")
                            .attr("src", "/assets/media/img/icons/delete.png")
                            .attr("title", "Remover")
                            .tooltip({placement: "bottom"})
                        )
                        .on("click", remove_research)

                    )

                )

            )

    });

}
function remove_research(){

    send_remove(this, "/pesquisa/deletar");

}

function main(){

    responseCodes["000"] = "";

    getData("/pesquisa/json", null, insert_researches);

}

window.onload = main;