const apiHost = window.location.hostname;
const apiPort = window.location.port;
const host = `http://${apiHost}:${apiPort}/api/console/v1/`;
const pageHost = `http://${apiHost}:${apiPort}/`;

var Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

$(document).ready(function () {
    getProfile();
});

function getQueryParamValue(key) {
    // Get the current URL
    var currentUrl = window.location.href;

    // Parse the URL to extract its components
    var url = new URL(currentUrl);

    // Get the value of the specified query parameter
    var paramValue = url.searchParams.get(key);

    return paramValue;
}

function login(element) {
    event.preventDefault();
    generateToken();
}

function getProfile() {
    var headers = {
        'Authorization': 'Bearer ' + localStorage.getItem("bearer")
    };
    $.ajax({
        url: host + 'profile',
        type: 'GET',
        dataType: 'json',
        headers: headers,
        success: function (response) {
            window.location.href = pageHost + 'inventory';
        },
        error: function (xhr, status, error) {

        }
    });
}

function generateToken() {
    $.ajax({
        url: host + 'login',
        type: 'POST',
        dataType: 'json',
        data: $('#loginCred').serializeArray(),
        success: function (response) {
            var fromPath = getQueryParamValue('from-path');
            localStorage.setItem("bearer", response.token);
            Toast.fire({
                icon: 'success',
                title: 'Login berhasil',
                timer: 1500
            });
            setTimeout(function () {
                window.location.href = pageHost + (fromPath ? decodeURIComponent(fromPath).substring(1) : '');
            }, 2000);

        },
        error: function (xhr, status, error) {
            Toast.fire({
                icon: 'error',
                title: JSON.parse(xhr.responseText).message,
            });
            $('#loginCred')[0].reset();
            console.error(JSON.parse(xhr.responseText).message);
        }
    });
}
