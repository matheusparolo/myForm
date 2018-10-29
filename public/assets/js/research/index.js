// Researches Actions
function insert_researches(data){

    let userID = data["userID"];

    let researchesData = data["researches"];
    let tableResearches = $("#researches");

    researchesData.forEach(function(researchData){

        let research = researchData["research"];
        let user = researchData["user"];

        tableResearches
            .append($("<tr>")

                .attr("data-id", research["id"])

                .append($("<td>")

                    .addClass("action")
                    .attr("title", "Visualizar")
                    .text(research["name"])
                    .on("click", function(){
                        window.location.replace("/pesquisa/" + research["id"])
                    })

                )
                .append($("<td>")

                    .addClass("action")
                    .attr("title", "Visualizar")
                    .text(userID == research["creatorId"] ? "você" : user["name"])
                    .on("click", function(){
                        window.location.replace("/pesquisa/" + research["id"])
                    })

                )
                .append($("<td>")

                    .addClass("action")
                    .attr("title", "Visualizar")
                    .text(research["applicationArea"])
                    .on("click", function(){
                        window.location.replace("/pesquisa/" + research["id"])
                    })

                )
                .append($("<td>")

                    .addClass("action")
                    .append($("<a>")

                        .css({"display" : (userID == research["creatorId"] ? "block" : "none")})
                        .attr("href", "/pesquisa/" + research["id"] + "/editar")
                        .append($("<img>")
                            .attr("src", "/assets/media/img/icons/edit.png")
                            .attr("title", "Editar")
                        )

                    )

                )
                .append($("<td>")

                    .addClass("action")
                    .append($("<span>")

                        .css({"display" : (userID == research["creatorId"] ? "block" : "none")})
                        .append($("<img>")
                            .attr("src", "/assets/media/img/icons/delete.png")
                            .attr("title", "Remover")
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

// Main
function init_vars(){

    responseCodes["000"] = "";

}
function get_data(){

    getJSON("/pesquisas/json", insert_researches);

}
function main(){

    init_vars();
    get_data();

}

window.onload = main;