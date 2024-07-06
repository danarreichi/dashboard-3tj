const apiHost = window.location.hostname;
const apiPort = window.location.port;
const host = `http://${apiHost}:${apiPort}/api/console/v1/`;
const pageHost = `http://${apiHost}:${apiPort}/`;
let startBetween = null;
let inventoryHistoryTable;

$(document).ready(function () {
    getProfile();
    if (getQueryParamValue('filter[trashed]')) $('#filterTrashed').val(getQueryParamValue('filter[trashed]'));
});

function toggleButtonClass(element) {
    if ($(element).hasClass('collapsed')) {
        $(element).removeClass('btn-outline-primary').addClass('btn-primary');
    } else {
        $(element).removeClass('btn-primary').addClass('btn-outline-primary');
    }
}

var Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

var flatPickr = flatpickr('.flatpickr-range', {
    altInput: true,
    altFormat: "F j, Y",
    dateFormat: "Y-m-d",
    mode: 'range',
    locale: 'id'
});

const Swal2 = Swal.mixin({
    customClass: {
        input: 'form-control'
    }
});

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
            if (response.user.user_role.id === 'admin') $('#addInventoryBtn').show();
            if (response.user.user_role.id === 'admin') $('#delete').show();
            $('#profileName').html(response.user.name);
            $('#profileRole').html(response.user.user_role.name);
        },
        error: function (xhr, status, error) {
            console.error(JSON.parse(xhr.responseText).message);
            window.location.href = pageHost + 'login?from-path=' + encodeURIComponent(window.location.pathname);
        }
    });
}

function addInventory(element) {
    event.preventDefault();
    var headers = {
        'Authorization': 'Bearer ' + localStorage.getItem("bearer")
    };
    $.ajax({
        url: host + 'inventory',
        type: 'POST',
        data: $('#' + element.id).serializeArray(),
        headers: headers,
        success: function (response) {
            clearInputErrors();
            $('#primary').modal('hide');
            clearForm(element.id);
            Toast.fire({
                icon: 'success',
                title: 'Data added',
                timer: 1500
            });
            customized_datatable.ajax.reload();
        },
        error: function (xhr, status, error) {
            clearInputErrors();
            if (xhr.responseJSON) {
                $.each(xhr.responseJSON.errors, function (fieldName, errorMessage) {
                    var inputField = $('[name="' + fieldName + '"]');
                    inputField.addClass('is-invalid');
                    inputField.after('<div class="invalid-feedback">' + errorMessage + '</div>');
                });
            }
            console.error(JSON.parse(xhr.responseText).message);
        }
    });
}

let trashedFilter = getQueryParamValue('filter[trashed]') ? getQueryParamValue('filter[trashed]') : '';

$('#filterTrashed').on('change', function () {
    trashedFilter = $(this).val();
    addOrUpdateQueryParam('filter[trashed]', trashedFilter);
    customized_datatable.ajax.reload();
});

let customized_datatable = $('#inventoryTable').DataTable({
    columns: [
        {
            data: null,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }
        },
        {
            data: 'code'
        },
        {
            data: 'name'
        },
        {
            data: 'unit'
        },
        {
            data: 'qty'
        },
        {
            data: null,
            render: function (data, type, row) {
                return dateIndFormat(row.updated_at);
            }
        },
        {
            data: null,
            width: "28%",
            render: function (data, type, row) {
                var editButton = '<button onclick="getInventory(this)" data-uuid="' + row.uuid + '" class="btn btn-secondary d-flex justify-content-center align-items-center"> <span class="me-2"><i class="bi bi-pencil-square"></i></i></span>Ubah</button>';
                var historyButton = '<button onclick="getInventoryHistory(this)" data-uuid="' + row.uuid + '" class="btn btn-info d-flex justify-content-center align-items-center"> <span class="me-2"><i class="bi bi-clock-history"></i></i></span>Riwayat</button>';
                var unBanButton = '<button onclick="restoreInventory(this)" data-uuid="' + row.uuid + '" class="btn btn-danger d-flex justify-content-center align-items-center"> <span class="me-2"><i class="bi bi-unlock"></i></i></span>Restore</button>';
                var grouped = '<div class="d-flex gap-2">' +
                    ((metaValue.logined_role === 'admin') ?
                        ((row.status === 'active') ? editButton + historyButton : unBanButton) :
                        ((row.status === 'active') ? historyButton : '')) +
                    '</div>';
                return grouped;
            }
        }
    ],
    ajax: {
        url: host + 'inventory',
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem("bearer")
        },
        data: function (d) {
            return {
                q: d.search.value,
                filter: {
                    trashed: trashedFilter
                },
                page: (d.start / d.length) + 1,
                limit: d.length,
            };
        },
        dataFilter: function (callBack) {
            var json = jQuery.parseJSON(callBack);

            json.recordsTotal = json.meta.total;
            json.recordsFiltered = json.meta.total;

            metaValue = json.meta;
            json.data = json.data;
            return JSON.stringify(json);
        },
        error: function (xhr, errorType, exception) {
            console.error('Error fetching data:', exception);
        },
        cache: true,
    },
    paging: true,
    pageLength: 10, // Default number of rows per page
    lengthMenu: [5, 10, 25, 50, 100], // Options for rows per page
    responsive: true,
    autoWidth: false,
    lengthChange: true,
    ordering: false,
    processing: true,
    serverSide: true,
    language: dataTablesIdLang
});

function getInventoryHistory(element) {
    if ($.fn.DataTable.isDataTable('#inventoryHistoryTable')) {
        $('#inventoryHistoryTable').DataTable().clear().destroy();
        startBetween = null;
        flatPickr.clear();
        flatPickr.set('minDate', null);
        flatPickr.set('maxDate', null);
        $('#dateRange').val(null);
        $('#modalInventoryName').html(null);
        $('#modalInventoryQty').html(null);
    }
    clearInputErrors();
    clearForm('adjustStockForm');
    getModalInventoryName(element);
    inventoryHistoryTable = $('#inventoryHistoryTable').DataTable({
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'name'
            },
            {
                data: null,
                render: function (data, type, row) {
                    let badgeColor = (row.status === 'in') ? 'bg-success' : 'bg-danger';
                    let status = (row.status === 'in') ? 'masuk' : 'keluar';
                    return `<span class="badge ${badgeColor}">${status}</span>`;
                }
            },
            {
                data: 'qty'
            },
            {
                data: 'new_qty'
            },
            {
                data: null,
                render: function (data, type, row) {
                    return dateIndFormat(row.created_at);
                }
            }
        ],
        ajax: {
            url: host + 'inventory/' + element.dataset.uuid + '/history',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem("bearer")
            },
            data: function (d) {
                let filter = {
                    start_between: startBetween,
                };
                if ((!filter.start_between)) filter = {};
                return {
                    q: d.search.value,
                    filter: filter,
                    page: (d.start / d.length) + 1,
                    limit: d.length,
                };
            },
            dataFilter: function (callBack) {
                var json = jQuery.parseJSON(callBack);

                json.recordsTotal = json.meta.total;
                json.recordsFiltered = json.meta.total;

                // Extract date part from min_date and max_date
                if (json.meta.date_min && json.meta.date_max) {
                    let minDate = json.meta.date_min.split(' ')[0]; // Get "YYYY-MM-DD" part
                    let maxDate = json.meta.date_max.split(' ')[0]; // Get "YYYY-MM-DD" part

                    // Update Flatpickr minDate and maxDate dynamically
                    flatPickr.set('minDate', minDate);
                    flatPickr.set('maxDate', maxDate);
                }

                $('#infoHistory').modal('show');
                metaValue = json.meta;
                json.data = json.data;
                return JSON.stringify(json);
            },
            error: function (xhr, errorType, exception) {
                console.error('Error fetching data:', exception);
            },
            cache: true,
        },
        paging: true,
        pageLength: 5, // Default number of rows per page
        lengthMenu: [5, 10, 25, 50, 100], // Options for rows per page
        responsive: true,
        autoWidth: false,
        lengthChange: true,
        ordering: false,
        processing: true,
        serverSide: true,
        language: dataTablesIdLang
    });
}

function getDatepickr(element) {
    let selectedDates = element._flatpickr.selectedDates;
    if (selectedDates.length === 2) {
        startBetween = `${selectedDates[0].toLocaleDateString('sv-SE')},${selectedDates[1].toLocaleDateString('sv-SE')}`;
        inventoryHistoryTable.ajax.reload();
    }
}

function getModalInventoryName(element) {
    var queryParams = {};
    var headers = {
        'Authorization': 'Bearer ' + localStorage.getItem("bearer")
    };
    $.ajax({
        url: host + 'inventory/' + element.dataset.uuid,
        type: 'GET',
        data: queryParams,
        headers: headers,
        success: function (response) {
            $('#uuidAdjust').val(response.data.uuid);
            $('#modalInventoryName').html(response.data.name);
            $('#modalInventoryQty').html(`${response.data.qty} ${response.data.unit}`);
        },
        error: function (xhr, status, error) {
            console.error(JSON.parse(xhr.responseText).message);
        }
    });
}

function getInventory(element) {
    var queryParams = {};
    var headers = {
        'Authorization': 'Bearer ' + localStorage.getItem("bearer")
    };
    $.ajax({
        url: host + 'inventory/' + element.dataset.uuid,
        type: 'GET',
        data: queryParams,
        headers: headers,
        success: function (response) {
            $('#uuidEdit').val(response.data.uuid);
            $('#nameEdit').val(response.data.name);
            $('#codeEdit').val(response.data.code);
            $('#unitEdit').val(response.data.unit);
            $('#qtyEdit').val(response.data.qty);
            $('#delete').attr('data-uuid', response.data.uuid);
            $('#secondaryEdit').modal('show');
        },
        error: function (xhr, status, error) {
            console.error(JSON.parse(xhr.responseText).message);
        }
    });
}

function editInventory(element) {
    event.preventDefault();
    var headers = {
        'Authorization': 'Bearer ' + localStorage.getItem("bearer")
    };
    $.ajax({
        url: host + 'inventory/' + $('#' + element.id).serializeArray()[0].value,
        type: 'PUT',
        data: $('#' + element.id).serializeArray(),
        headers: headers,
        success: function (response) {
            $('#secondaryEdit').modal('hide');
            clearInputErrors();
            clearForm(element.id);
            Toast.fire({
                icon: 'success',
                title: 'Data berhasil diubah',
                timer: 1500
            });
            let lastPage = customized_datatable.page();
            customized_datatable.ajax.reload(function () {
                customized_datatable.page(lastPage).draw(false);
            });
        },
        error: function (xhr, status, error) {
            clearInputErrors();
            if (xhr.responseJSON) {
                $.each(xhr.responseJSON.errors, function (fieldName, errorMessage) {
                    var inputField = $('[name="' + fieldName + '"]');
                    inputField.addClass('is-invalid');
                    inputField.after('<div class="invalid-feedback">' + errorMessage + '</div>');
                });
            }
            console.error(JSON.parse(xhr.responseText).message);
        }
    });
}

function deleteInventory(element) {
    Swal2.fire({
        icon: "question",
        title: "Apakah anda yakin?",
        text: "Apakah anda yakin menghapus inventory ini?",
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
        reverseButtons: false // optional, makes the "No" button come first
    }).then((result) => {
        if (result.isConfirmed) {
            var headers = {
                'Authorization': 'Bearer ' + localStorage.getItem("bearer")
            };
            $.ajax({
                url: host + 'inventory/' + element.dataset.uuid,
                type: 'DELETE',
                headers: headers,
                success: function (response) {
                    $('#secondaryEdit').modal('hide');
                    clearInputErrors();
                    clearForm('editInventoryForm');
                    Toast.fire({
                        icon: 'success',
                        title: 'Inventory berhasil terhapus',
                        timer: 1500
                    });
                    customized_datatable.ajax.reload();
                },
                error: function (xhr, status, error) {
                    Toast.fire({
                        icon: 'error',
                        title: JSON.parse(xhr.responseText).message,
                        timer: 1500
                    });
                    console.error(JSON.parse(xhr.responseText).message);
                }
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            console.log('Operation cancelled');
        }
    });
}

function restoreInventory(element) {
    Swal2.fire({
        icon: "question",
        title: "Apakah anda yakin?",
        text: "Apakah anda yakin mengembalikan inventory ini?",
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
        reverseButtons: false // optional, makes the "No" button come first
    }).then((result) => {
        if (result.isConfirmed) {
            var headers = {
                'Authorization': 'Bearer ' + localStorage.getItem("bearer")
            };
            $.ajax({
                url: host + 'inventory/' + element.dataset.uuid + '/restore',
                type: 'GET',
                headers: headers,
                success: function (response) {
                    Toast.fire({
                        icon: 'success',
                        title: 'Inventory berhasil dikembalikan',
                        timer: 1500
                    });
                    customized_datatable.ajax.reload();
                },
                error: function (xhr, status, error) {
                    console.error(JSON.parse(xhr.responseText).message);
                }
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            console.log('Operation cancelled');
        }
    });
}

function adjustStock(element) {
    event.preventDefault();
    let data = $(element).serializeArray();
    Swal2.fire({
        icon: "question",
        title: "Apakah anda yakin?",
        text: `Apakah anda ingin ${(data[1].value === 'in') ? `memasukkan ${data[2].value} item ke` : `mengurangi ${data[2].value} item dari`} inventory?`,
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
        inputAutoFocus: false,
        didOpen: () => {
            flatPickr.destroy();
        },
        didClose: () => {
            flatPickr = flatpickr('.flatpickr-range', {
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
                mode: 'range',
                locale: 'id'
            });
        },
        reverseButtons: false // optional, makes the "No" button come first
    }).then((result) => {
        if (result.isConfirmed) {
            let uuid = data[0].value;
            var headers = {
                'Authorization': 'Bearer ' + localStorage.getItem("bearer")
            };
            $.ajax({
                url: host + `inventory/${uuid}/adjust`,
                type: 'POST',
                data: data,
                headers: headers,
                success: function (response) {
                    clearInputErrors();
                    clearForm(element.id);
                    Toast.fire({
                        icon: 'success',
                        title: 'Data berhasil diatur',
                        timer: 1500
                    });
                    getModalInventoryName({
                        dataset: {
                            uuid: uuid
                        }
                    });
                    inventoryHistoryTable.ajax.reload();

                    let lastPage = customized_datatable.page();
                    customized_datatable.ajax.reload(function () {
                        customized_datatable.page(lastPage).draw(false);
                    });
                },
                error: function (xhr, status, error) {
                    clearInputErrors();
                    if (xhr.responseJSON) {
                        $.each(xhr.responseJSON.errors, function (fieldName, errorMessage) {
                            var inputField = $('[name="' + fieldName + '"]');
                            inputField.addClass('is-invalid');
                            inputField.after('<div class="invalid-feedback">' + errorMessage + '</div>');
                        });
                        getModalInventoryName({
                            dataset: {
                                uuid: uuid
                            }
                        });
                        inventoryHistoryTable.ajax.reload();
                    }
                    console.error(JSON.parse(xhr.responseText).message);
                }
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            console.log('Operation cancelled');
        }
    });
}

const setTableColor = () => {
    document.querySelectorAll('.dataTables_paginate .pagination').forEach(dt => {
        dt.classList.add('pagination-primary');
    });
}
setTableColor();
