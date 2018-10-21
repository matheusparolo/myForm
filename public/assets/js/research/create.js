function change_status_search_member(){

    ($(this).attr("id") === "search-member") ? $("#search").show() : $("#search").hide();

}

function add_member(){

    let userID = search_parent_attr(this, "data-userid");

    if(members.indexOf(userID) === -1){

        let user = users[userID];
        members.push(user["id"]);

        $("#table-members")
            .append($("<tr>")

                .attr("data-userid", user["id"])
                .append($("<td>").text(user["name"]))
                .append($("<td>").text(user["email"]))
                .append($("<td>")
                    .addClass("action-i")
                    .append($("<img>")
                        .attr("title", "Remover membro")
                        .attr("src", "/assets/media/img/icons/delete.png")
                        .tooltip({"placement" : "bottom"})
                        .on("click", remove_member)
                    )
                )

            )

    }

}

function remove_member(){

    let userid = search_parent_attr(this, "data-userid");
    members.splice(members.indexOf(userid), 1);
    $(this.parentNode.parentNode).remove();

}

function search_user(){

    let val = $("#input-search").val();
    if(val !== "")
    {

        let btn = this;
        change_disabled(btn, true);
        insert_loading();

        getData("/usuario/buscar/" + val, null, function(data){

            users = [];
            if(data.length > 0)
            {

                let tableUsers = $("#table-users");
                tableUsers.html("");

                data.forEach(function(user){

                    users[user["id"]] = user;
                    tableUsers
                        .append($("<tr>")

                            .attr("data-userid", user["id"])
                            .append($("<td>")
                                .text(user["name"])
                            )
                            .append($("<td>")
                                .text(user["email"])
                            )
                            .append($("<td>")
                                .addClass("action-i")
                                .append($("<img>")
                                    .attr("title", "Adicionar como membro")
                                    .attr("src", "/assets/media/img/icons/add-person.png")
                                    .tooltip({"placement" : "bottom"})
                                    .on("click", add_member)
                                )
                            )

                        )

                });


            }

            change_disabled(btn, false);
            remove_loading();

        })

    }

}

function submit_create(){

    submit_form("#form-create", "/pesquisa/criar", [{"name" : "members", "value" : members}]);

}

function main(){

    responseCodes["000"] = "/pesquisa";

    members = [];

    $("#search-member, #close-search").on("click", change_status_search_member);
    $(".action-i img, .action-t").tooltip({
        enable : true,
        placement: "bottom"
    });

    $("#form-create").on("submit", submit_create);

    $("#btn-search").on("click", search_user);

}

window.onload = main;