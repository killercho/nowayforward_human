var authentication_response = null;
var authentication_callbacks = [];

function requestAuthentication() {
    var request = new XMLHttpRequest();
    request.onreadystatechange = function() {
        if (request.readyState < 4) return;

        authentication_response = (request.status == 200) ? request.responseText : "";
    }
    request.open("POST", "/authenticate", true);
    request.setRequestHeader("Authorization", sessionStorage.getItem("token"));
    request.send(null);
}
requestAuthentication();

function authenticated(callback) {
    authentication_callbacks.push(callback);
}
