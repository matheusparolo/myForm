function show_response(data, redirect = true){

    console.log(data);

    data = JSON.parse(data);
    let code = data["code"];

    switch (code) {
        case "000":
            if(redirect) window.location.replace(codes["000"]);
            break;
        case "300":
            window.location.replace("/entrar");
            break;
        default:
            $("#result").text(codes[code]);
            break;
    }

}
