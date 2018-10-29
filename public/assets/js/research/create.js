// Submits
function submit_create(){

    $("#form-create").submitter("/pesquisa/criar", {
        data : {
            "members" : members
        }
    });

}

// Members Actions
function new_member(){

    let userID = search_parent_attr(this, "data-userid");
    insert_member(userID);

}
function insert_member(userID){

    if(members.indexOf(userID) === -1){

        let user = users[userID];
        members.push(user["id"]);

        $("#table-members")
            .append($("<tr>")
                .attr("data-userid", user["id"])
                .append($("<td>").text(user["name"]))
                .append($("<td>").text(user["email"]))
                .append($("<td>")
                    .addClass("action")
                    .append($("<img>")
                        .attr("title", "Remover membro")
                        .attr("src", "/assets/media/img/icons/delete.png")
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

// Users searchs
function insert_users(data){

    users = [];
    let tableUsers = $("#table-users");
    tableUsers.html("");

    if(data.length > 0)
    {

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
                        .addClass("action")
                        .append($("<img>")
                            .attr("title", "Adicionar como membro")
                            .attr("src", "/assets/media/img/icons/add-person.png")
                            .on("click", new_member)
                        )
                    )

                )

        });

    }

}
function search_user(){

    let val = $("#input-search").val();
    if(val !== "")
    {

        let btn = this;
        change_disabled(btn, true);

        getJSON("/usuario/buscar/" + val, function(data){
            insert_users(data);
            change_disabled(btn, false);
        })

    }

}

// main
function init_vars(){

    responseCodes["000"] = "/pesquisas";
    members = [];

}
function get_data(){

    getJSON("/pesquisa/criar/json", function(data){

        let areas = data["areas"];
        areas.forEach(function(area){

            $("#list-areas").append($("<option>").text(area))

        });

    })

}
function binds(){

    $("#form-create").on("submit", submit_create);
    $("#btn-search").on("click", search_user);

}

function main(){

    init_vars();
    get_data();
    binds();

}

window.onload = main;