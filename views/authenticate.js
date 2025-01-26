var cookieStorage = {
    getItem: function(index) {
        let cookies = document.cookie.split(';');
        for (cookie of cookies) {
            let values = cookie.trim().split('=');
            if (values[0] === index) {
                return values[1];
            }
        }
        return undefined;
    },
    setItem: function(index, value, expires = 'Fri, 31 Dec 9999 23:59:59 GMT') {
        let cookie = index + '=' + value + ';';
        cookie += 'expires=' + expires + ';';
        cookie += 'path=/';
        document.cookie = cookie;
    },
    removeItem: function(index) {
        cookieStorage.setItem(index, "", 'Thu, 01 Jan 1970 00:00:00 GMT');
    },
};

var authentication_response = null;
var authentication_callbacks = [];

function requestAuthentication() {
    var request = new XMLHttpRequest();
    request.onreadystatechange = function() {
        if (request.readyState < 4) return;

        authentication_response = (request.status == 200) ? request.responseText : "";
    }
    request.open("POST", "/authenticate", true);
    request.send(null);
}
requestAuthentication();

function authenticated(callback) {
    authentication_callbacks.push(callback);
}
