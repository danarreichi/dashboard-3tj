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
