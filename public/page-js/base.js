const apiProtocol = window.location.protocol;
const apiHost = window.location.hostname;
const apiPort = window.location.port;
const host = `${apiProtocol}//${apiHost}:${apiPort}/api/console/v1/`;
const pageHost = `${apiProtocol}//${apiHost}:${apiPort}/`;

// Base JS
function dateIndFormat(dateTime) {
    var dateObject = new Date(dateTime);
    var jakartaTime = dateObject.toLocaleString('en-US', {
        timeZone: 'Asia/Jakarta'
    });
    return new Intl.DateTimeFormat('en', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    }).format(new Date(jakartaTime));
}

function dateIndWithTimeFormat(dateTime) {
    var dateObject = new Date(dateTime);
    var jakartaTime = dateObject.toLocaleString('en-US', {
        timeZone: 'Asia/Jakarta'
    });
    return new Intl.DateTimeFormat('en', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    }).format(new Date(jakartaTime));
}

function clearForm(formId, selectPlaceholder) {
    var form = document.getElementById(formId);
    var inputs = form.getElementsByTagName('input');
    var selects = form.getElementsByTagName('select');

    for (var i = 0; i < inputs.length; i++) inputs[i].value = '';
    for (var i = 0; i < selects.length; i++) selects[i].selectedIndex = 0;
}

function clearInputErrors() {
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').remove();
}

function addOrUpdateQueryParam(key, value) {
    // Get the current URL
    var currentUrl = window.location.href;

    // Parse the URL to extract its components
    var url = new URL(currentUrl);

    // Add or the query parameter
    url.searchParams.set(key, value);

    // Construct the new URL
    var newUrl = url.toString();

    // Update the current URL
    window.history.replaceState({}, document.title, newUrl);

    return newUrl;
}

function getQueryParamValue(key) {
    // Get the current URL
    var currentUrl = window.location.href;

    // Parse the URL to extract its components
    var url = new URL(currentUrl);

    // Get the value of the specified query parameter
    var paramValue = url.searchParams.get(key);

    return paramValue;
}

function showLoading() {
    $('#ajaxLoadingOverlay').show();
}

function hideLoading() {
    $('#ajaxLoadingOverlay').hide();
}

$(document).ready(function () {
    $('body').append(
        '<div id="ajaxLoadingOverlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:99999;">' +
        '<div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:var(--bs-body-bg);color:var(--bs-body-color);padding:24px 32px;border-radius:10px;text-align:center;box-shadow:0 4px 24px rgba(0,0,0,0.4);border:1px solid var(--bs-border-color);">' +
        '<div class="spinner-border text-primary" role="status" style="width:2.5rem;height:2.5rem;"></div>' +
        '<p class="mt-3 mb-0 fw-semibold">Memproses...</p>' +
        '</div></div>'
    );
});

var dataTablesIdLang = {
    "sEmptyTable": "Tidak ada data yang tersedia pada tabel ini",
    "sProcessing": "Sedang memproses...",
    "sLengthMenu": "Tampilkan _MENU_ entri",
    "sZeroRecords": "Tidak ditemukan data yang sesuai",
    "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
    "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
    "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
    "sInfoPostFix": "",
    "sSearch": "Cari:",
    "sUrl": "",
    "oPaginate": {
        "sFirst": "Pertama",
        "sPrevious": "Sebelumnya",
        "sNext": "Berikutnya",
        "sLast": "Terakhir"
    }
};
