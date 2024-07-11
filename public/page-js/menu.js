const apiHost = window.location.hostname;
const apiPort = window.location.port;
const host = `http://${apiHost}:${apiPort}/api/console/v1/`;
const pageHost = `http://${apiHost}:${apiPort}/`;
var menuPricesTable;

$(document).ready(function () {
    getProfile();
    if (getQueryParamValue('filter[trashed]')) $('#filterTrashed').val(getQueryParamValue('filter[trashed]'));
    getMenuCategoryDropdown('menuCategoryId');
    getMenuCategoryDropdown('menuCategoryIdEdit');
});

var Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
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
            if (response.user.user_role.id === 'admin') $('#addMenuBtn').show();
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

function getMenuCategoryDropdown(type) {
    var headers = {
        'Authorization': 'Bearer ' + localStorage.getItem("bearer")
    };
    $.ajax({
        url: host + "dropdown/menu-category",
        type: 'GET',
        headers: headers,
        success: function (response) {
            var selectElement = $('#' + type);
            selectElement.empty(); // Clear existing options
            selectElement.append(
                '<option value="" style="display: none;" disabled selected>Pilih kategori menu</option>'
            );

            response.data.forEach(function (item, index) {
                selectElement.append('<option value="' + item.uuid + '">' + item.name +
                    '</option>');
            });
        },
        error: function (xhr, status, error) {
            console.error(JSON.parse(xhr.responseText).message);
        }
    });
}

function getInventoryDropdown(excludes) {
    var headers = {
        'Authorization': 'Bearer ' + localStorage.getItem("bearer")
    };
    $.ajax({
        url: host + "dropdown/inventory" + (excludes ? `?${excludes}` : ''),
        type: 'GET',
        headers: headers,
        body: body,
        success: function (response) {
            var selectElement = $('#inventories');
            selectElement.empty(); // Clear existing options
            selectElement.append(
                '<option value="" style="display: none;" disabled selected>Pilih bahan yang dibutuhkan</option>'
            );

            response.data.forEach(function (item, index) {
                selectElement.append(`<option value="${item.uuid}" data-name="${item.name}" data-unit="${item.unit}">${item.name}</option>`);
            });
        },
        error: function (xhr, status, error) {
            console.error(JSON.parse(xhr.responseText).message);
        }
    });
}

function addMenu(element) {
    event.preventDefault();
    var headers = {
        'Authorization': 'Bearer ' + localStorage.getItem("bearer")
    };
    $.ajax({
        url: host + 'menu',
        type: 'POST',
        data: new FormData(element),
        contentType: false, // Tell jQuery not to process the data
        processData: false, // Tell jQuery not to set contentType,
        headers: headers,
        success: function (response) {
            clearInputErrors();
            $('#primary').modal('hide');
            clearForm(element.id);
            Toast.fire({
                icon: 'success',
                title: 'Data berhasil ditambahkan',
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

let customized_datatable = $('#menuTable').DataTable({
    columns: [
        {
            data: null,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }
        },
        {
            data: null,
            render: function (data, type, row) {
                return `<img src="${pageHost}${row.image}" class="me-2" style="width: 100px; height: 100px; object-fit: cover;"></img>` + row.name;
            }
        },
        {
            data: 'category'
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
                let editButton = '<button onclick="getMenu(this)" data-uuid="' + row.uuid + '" class="btn btn-secondary d-flex justify-content-center align-items-center"> <span class="me-2"><i class="bi bi-pencil-square"></i></i></span>Ubah</button>';
                let pricesButton = '<button onclick="getPrices(this)" data-uuid="' + row.uuid + '" class="btn btn-success d-flex justify-content-center align-items-center"> <span class="me-2"><i class="bi bi-cash-coin"></i></i></i></span>Daftar Harga & Bahan</button>';
                let unBanButton = '<button onclick="restoreMenu(this)" data-uuid="' + row.uuid + '" class="btn btn-danger d-flex justify-content-center align-items-center"> <span class="me-2"><i class="bi bi-unlock"></i></i></span>Restore</button>';
                let grouped = '<div class="d-flex gap-2">' +
                    ((metaValue.logined_role === 'admin') ?
                        ((row.status === 'active') ? editButton + pricesButton : unBanButton) : '') +
                    '</div>';
                return grouped;
            }
        }
    ],
    ajax: {
        url: host + 'menu',
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

function getMenu(element) {
    var queryParams = {};
    var headers = {
        'Authorization': 'Bearer ' + localStorage.getItem("bearer")
    };
    $.ajax({
        url: host + 'menu/' + element.dataset.uuid,
        type: 'GET',
        data: queryParams,
        headers: headers,
        success: function (response) {
            $('#uuidEdit').val(response.data.uuid);
            $('#nameEdit').val(response.data.name);
            $('#menuCategoryIdEdit').val(response.data.category_uuid);
            $('#delete').attr('data-uuid', response.data.uuid);
            $('#secondaryEdit').modal('show');
        },
        error: function (xhr, status, error) {
            console.error(JSON.parse(xhr.responseText).message);
        }
    });
}

function getPrices(element) {
    if ($.fn.DataTable.isDataTable('#menuPricesTable')) {
        $('#menuPricesTable').DataTable().clear().destroy();
    }
    var queryParams = {};
    var headers = {
        'Authorization': 'Bearer ' + localStorage.getItem("bearer")
    };
    $.ajax({
        url: host + 'menu/' + element.dataset.uuid + '/price',
        type: 'GET',
        data: queryParams,
        headers: headers,
        success: function (response) {
            document.getElementById('tempRecipeForm').setAttribute('data-uuid', element.dataset.uuid);
            $('#modalMenuName').html(response.meta.menu_name);
            menuPricesTable = $('#menuPricesTable').DataTable({
                columns: [
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: null,
                        width: "95%",
                        render: function (data, type, row, meta) {
                            let recipes = row.recipes.map(recipe => {
                                return `<button type="button" class="list-group-item list-group-item-action d-flex justify-content-between"><span>${recipe.name}</span><span class="fw-semibold">${recipe.qty}${recipe.unit} @${recipe.per_serving_price}</span></button>`;
                            }).join('');
                            let setActiveBtn = (row.status !== 'active') ? `<button class="btn btn-success w-100 mb-2 btn-sm" data-menu-uuid="${metaValue.menu_uuid}" data-uuid="${row.uuid}" onclick="activatePrice(this)">Aktifkan harga</button>` : '';
                            let badge = (row.status === 'active') ? 'success' : 'danger';
                            return `<div class="accordion-item">
								<h2 class="accordion-header" id="headingOne${meta.row + meta.settings._iDisplayStart + 1}">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${meta.row + meta.settings._iDisplayStart + 1}" aria-expanded="false">
									<span class="me-2 fw-bold">${row.price}</span><span class="badge bg-light-${badge}">${row.status}</span>
									</button>
								</h2>
								<div id="collapse${meta.row + meta.settings._iDisplayStart + 1}" class="accordion-collapse collapse" style="">
									<div class="accordion-body">
                                        ${setActiveBtn}
                                        <div class="list-group">
                                            ${recipes}
                                        <hr>
                                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-end"><span class="fw-semibold">HPP: ${row.total_per_serving_price}</span></button>
                                        </div>
									</div>
								</div>
							</div>`;
                        }
                    },
                ],
                ajax: {
                    url: host + 'menu/' + element.dataset.uuid + '/price',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem("bearer")
                    },
                    data: function (d) {
                        let filter = {};
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

                        $('#successPrices').modal('show');
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
        },
        error: function (xhr, status, error) {
            console.error(JSON.parse(xhr.responseText).message);
        }
    });
}

function getInventoryHistoryDropdown(inventoryUuid) {
    var queryParams = {};
    var headers = {
        'Authorization': 'Bearer ' + localStorage.getItem("bearer")
    };
    $.ajax({
        url: host + 'dropdown/inventory/' + inventoryUuid + '/history',
        type: 'GET',
        data: queryParams,
        headers: headers,
        success: function (response) {
            var selectElement = $(`#${inventoryUuid}`);
            selectElement.empty(); // Clear existing options
            selectElement.append(
                '<option value="" style="display: none;" disabled selected>Pilih harga restock terbaru</option>'
            );
            response.data.forEach(function (item, index) {
                selectElement.append(`<option value="${item.uuid}" data-price-per-unit="${item.price_per_unit}">${item.price}</option>`);
            });
        },
        error: function (xhr, status, error) {
            console.error(JSON.parse(xhr.responseText).message);
        }
    });
}

const rupiah = (number) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR"
    }).format(number);
}

// var HPP = 0;
function calculateHpp() {
    let HPP = 0;
    if ($('[name="inventory_history[]"]').length === 0) {
        $('#hppPlaceholder').html(`HPP: ${rupiah(HPP)}`);
        return; // Exit function early if there are no elements
    }
    $('[name="inventory_history[]"]').each(function () {
        var selectedOption = $(this).find('option:selected');
        var pricePerUnit = selectedOption.data('price-per-unit') ? selectedOption.data('price-per-unit') : 0;

        // Find the nearest <tr> element
        var row = $(this).closest('tr');
        var qty = parseFloat(row.find('input[name="qty[]"]').val());
        HPP = HPP + (pricePerUnit * qty);
    });
    $('#hppPlaceholder').html(`HPP: ${rupiah(HPP)}`);
}

function changeUnitPlaceholder(element) {
    var selectedOption = $('#' + element.id).find(':selected');
    $('#unitPlaceholder').html(selectedOption.data('unit'));
}

function addPrice(element) {
    $('#successPrices').modal('hide');
    $('#addMenuPriceTable tbody').empty();
    $('#saveMenuPriceButton').hide();
    getInventoryDropdown();
    calculateHpp();
    $('#successAddMenuPrice').modal('show');
}

function backToPriceModal() {
    $('#successAddMenuPrice').modal('hide');
    $('#successPrices').modal('show');
}

function addRecipeTemp(element) {
    event.preventDefault();
    let attributes = $('#' + element.id).serializeArray();
    var selectedOption = $('#inventories').find(':selected');
    var inventoryName = selectedOption.data('name');
    var inventoryUnit = selectedOption.data('unit');
    var rowCount = $('#addMenuPriceTable tbody tr').length + 1;
    var newRow = `<tr>
                <td class="row-number">${rowCount}</td>
                <td>${inventoryName}</td>
                <td>${attributes[1].value}${inventoryUnit}</td>
                <td>
                    <select id="${attributes[0].value}" data-group="${attributes[0].value}" onchange="calculateHpp()" name="inventory_history[]" class="form-control" required>
                        <option style="display: none;" selected>Pilih harga restock terbaru</option>
                    </select>
                </td>
                <input type="hidden" id="inventoryUuid" value="${attributes[0].value}" data-group="${attributes[0].value}" name="inventory_uuid[]">
                <input type="hidden" id="qty" value="${attributes[1].value}" data-group="${attributes[0].value}" name="qty[]">
                <td><button type="button" onclick="removeRow(this)" class="removeRow btn btn-danger w-100">Hapus</button></td>
                </tr>`;

    getInventoryHistoryDropdown(attributes[0].value);
    clearForm(element.id);
    $('#unitPlaceholder').html('');
    $('#addMenuPriceTable tbody').append(newRow);
    let excludedInventories = $('#tempRecipeForm').serializeArray('inventory_uuid').filter(item => item.name === 'inventory_uuid[]').map(item => item.value);
    getInventoryDropdown(`excludes=${excludedInventories}`);
    $('#saveMenuPriceButton').show();
}

function saveMenuPriceForm(element) {
    event.preventDefault();
    // Step 1: Find how many groups are there
    let groups = {};
    $('[data-group]').each(function () {
        let groupName = $(this).data('group');
        groups[groupName] = true; // Use an object to collect unique group names
    });

    let groupNames = Object.keys(groups); // Array of unique group names
    let groupedAttributes = {};

    groupNames.forEach(function (groupName, index) {
        groupedAttributes[index] = [];

        $('[data-group="' + groupName + '"]').each(function () {
            let fieldName = $(this).attr('name');
            let value = $(this).val();
            groupedAttributes[index].push({ name: fieldName, value: value, group: groupName });
        });
    });

    let recipes = [];
    let price = $(`#${element.id}`).find('input[name="price"]').val();
    Object.keys(groupedAttributes).forEach(index => {
        let uuidItem = groupedAttributes[index].find(item => item.name === 'inventory_history[]');
        let qtyItem = groupedAttributes[index].find(item => item.name === 'qty[]');

        // Push a new recipe object into recipes array
        if (uuidItem && qtyItem) {
            let recipe = {
                uuid: uuidItem.value,
                qty: qtyItem.value
            };

            // Remove null values from recipe object
            recipe = Object.fromEntries(
                Object.entries(recipe).filter(([_, v]) => v != null)
            );

            recipes.push(recipe);
        }
    });

    let dataObject = { recipes: recipes };
    if (price) dataObject.price = price;

    var headers = {
        'Authorization': 'Bearer ' + localStorage.getItem("bearer")
    };
    $.ajax({
        url: host + `menu/${element.dataset.uuid}/price`,
        type: 'POST',
        data: dataObject,
        headers: headers,
        success: function (response) {
            clearInputErrors();
            Toast.fire({
                icon: 'success',
                title: 'Harga menu berhasil ditambahkan',
                timer: 1500
            });
            menuPricesTable.ajax.reload();
            backToPriceModal();
        },
        error: function (xhr, status, error) {
            clearInputErrors();
            if (xhr.responseJSON) {
                $.each(xhr.responseJSON.errors, function (fieldName, errorMessage) {
                    if (fieldName === "price") {
                        var inputField = $('[name="' + fieldName + '"]');
                        inputField.addClass('is-invalid');
                        inputField.after('<div class="invalid-feedback">' + errorMessage + '</div>');
                    }
                    if (fieldName.includes("recipes") && fieldName.includes("uuid")) {
                        let iteration = parseInt(fieldName.split(".")[1]);

                        $('[name="inventory_history[]"]').each(function (index, element) {
                            if (index === iteration) {
                                let inputField = $(element);
                                inputField.addClass('is-invalid');
                                inputField.after('<div class="invalid-feedback">Pilihan tidak boleh kosong</div>');
                            }
                        });
                    }
                });
            }
            console.error(JSON.parse(xhr.responseText).message);
        }
    });
}

function updateRowNumbers() {
    $('#addMenuPriceTable tbody tr').each(function (index) {
        $(this).find('.row-number').text(index + 1);
    });
}

function removeRow(element) {
    $(element).closest('tr').remove();
    updateRowNumbers();
    calculateHpp();
    let excludedInventories = $('#tempRecipeForm').serializeArray('inventory_uuid').filter(item => item.name === 'inventory_uuid[]').map(item => item.value);
    getInventoryDropdown(`excludes=${excludedInventories}`);
}

function editMenu(element) {
    event.preventDefault();
    var headers = {
        'Authorization': 'Bearer ' + localStorage.getItem("bearer")
    };
    var bodyData = new FormData(element);
    bodyData.append('_method', 'PUT');
    $.ajax({
        url: host + 'menu/' + $('#' + element.id).serializeArray()[0].value,
        type: 'POST',
        data: bodyData,
        contentType: false, // Tell jQuery not to process the data
        processData: false, // Tell jQuery not to set contentType,
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

function deleteMenu(element) {
    Swal2.fire({
        icon: "question",
        title: "Apakah anda yakin?",
        text: "Apakah anda yakin menghapus menu ini?",
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
                url: host + 'menu/' + element.dataset.uuid,
                type: 'DELETE',
                headers: headers,
                success: function (response) {
                    $('#secondaryEdit').modal('hide');
                    clearInputErrors();
                    clearForm('editMenuForm');
                    Toast.fire({
                        icon: 'success',
                        title: 'Menu berhasil terhapus',
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

function restoreMenu(element) {
    Swal2.fire({
        icon: "question",
        title: "Apakah anda yakin?",
        text: "Apakah anda yakin mengembalikan menu ini?",
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
                url: host + 'menu/' + element.dataset.uuid + '/restore',
                type: 'GET',
                headers: headers,
                success: function (response) {
                    Toast.fire({
                        icon: 'success',
                        title: 'Menu berhasil dikembalikan',
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

function activatePrice(element) {
    Swal2.fire({
        icon: "question",
        title: "Apakah anda yakin?",
        text: "Apakah anda yakin mengaktifkan harga ini?",
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
                url: host + 'menu/' + element.dataset.menuUuid + '/price/' + element.dataset.uuid + '/activate',
                type: 'GET',
                headers: headers,
                success: function (response) {
                    Toast.fire({
                        icon: 'success',
                        title: 'Harga berhasil diaktifkan',
                        timer: 1500
                    });
                    let lastPage = menuPricesTable.page();
                    menuPricesTable.ajax.reload(function () {
                        menuPricesTable.page(lastPage).draw(false);
                    });
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

const setTableColor = () => {
    document.querySelectorAll('.dataTables_paginate .pagination').forEach(dt => {
        dt.classList.add('pagination-primary');
    });
}
setTableColor();
