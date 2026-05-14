var menuPricesTable;

$(document).ready(function () {
    getProfile();
    if (getQueryParamValue('filter[trashed]')) $('#filterTrashed').val(getQueryParamValue('filter[trashed]'));
    initMenuCategorySelect2('menuCategoryId', 'primary');
    initMenuCategorySelect2('menuCategoryIdEdit', 'secondaryEdit');
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

function initMenuCategorySelect2(selectId, modalId) {
    $('#' + selectId).select2({
        theme: 'bootstrap-5',
        placeholder: 'Pilih kategori menu',
        allowClear: true,
        dropdownParent: $('#' + modalId),
        ajax: {
            url: host + 'dropdown/menu-category',
            headers: { 'Authorization': 'Bearer ' + localStorage.getItem('bearer') },
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return { q: params.term || '', page: params.page || 1 };
            },
            processResults: function (response, params) {
                return {
                    results: response.data.map(function (item) {
                        return { id: item.uuid, text: item.name };
                    }),
                    pagination: { more: response.meta.current_page < response.meta.last_page }
                };
            },
            cache: true
        }
    });
}

function initInventorySelect2() {
    if ($('#inventories').hasClass('select2-hidden-accessible')) {
        $('#inventories').select2('destroy');
    }
    $('#inventories').select2({
        theme: 'bootstrap-5',
        placeholder: 'Pilih bahan yang dibutuhkan',
        allowClear: true,
        dropdownParent: $('#successAddMenuPrice'),
        ajax: {
            url: host + 'dropdown/inventory',
            headers: { 'Authorization': 'Bearer ' + localStorage.getItem('bearer') },
            dataType: 'json',
            delay: 300,
            data: function (params) {
                var excludes = $('#tempRecipeForm').serializeArray()
                    .filter(item => item.name === 'inventory_uuid[]')
                    .map(item => item.value);
                return { q: params.term || '', page: params.page || 1, excludes: excludes.join(',') };
            },
            processResults: function (response, params) {
                return {
                    results: response.data.map(function (item) {
                        return { id: item.uuid, text: item.name, unit: item.unit };
                    }),
                    pagination: { more: response.meta.current_page < response.meta.last_page }
                };
            },
            cache: false
        }
    }).on('select2:select', function (e) {
        $('#unitPlaceholder').html(e.params.data.unit || '');
    }).on('select2:clear', function () {
        $('#unitPlaceholder').html('');
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
                const imgEl = row.image
                    ? `<img src="${pageHost}${row.image}" class="me-2" style="width: 100px; height: 100px; object-fit: cover;">`
                    : `<div class="me-2 d-inline-flex align-items-center justify-content-center bg-secondary rounded text-white" style="width: 100px; height: 100px; font-size: 0.7rem;">No Image</div>`;
                return imgEl + row.name;
            }
        },
        {
            data: 'category'
        },
        {
            data: null,
            render: function (data, type, row) {
                return row.price_display;
            }
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
            var categoryOption = new Option(response.data.category, response.data.category_uuid, true, true);
            $('#menuCategoryIdEdit').empty().append(categoryOption).trigger('change');
            $('#delete').attr('data-uuid', response.data.uuid);
            resetImageEdit();
            if (response.data.image) {
                $('#imagePreviewImgEdit').attr('src', response.data.image);
                $('#imagePreviewEdit').show();
                $('#clearImageEdit').show();
            }
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

function initHistorySelect2(inventoryUuid) {
    $('#' + inventoryUuid).select2({
        theme: 'bootstrap-5',
        placeholder: 'Pilih harga restock',
        dropdownParent: $('#successAddMenuPrice'),
        ajax: {
            url: host + 'dropdown/inventory/' + inventoryUuid + '/history',
            headers: { 'Authorization': 'Bearer ' + localStorage.getItem('bearer') },
            dataType: 'json',
            delay: 250,
            processResults: function (response) {
                return {
                    results: response.data.map(function (item) {
                        return { id: item.uuid, text: item.price + ' @' + item.qty, pricePerUnit: item.price_per_unit };
                    })
                };
            },
            cache: true
        }
    }).on('select2:select', function () {
        calculateHpp();
    });
}

function switchPriceMode(button, inventoryUuid, mode) {
    var btnGroup = $(button).closest('.btn-group');
    btnGroup.find('.btn').removeClass('active');
    $(button).addClass('active');

    if (mode === 'history') {
        $('#custom-section-' + inventoryUuid).hide();
        $('#custom-price-' + inventoryUuid).prop('required', false).val('');
        $('#history-section-' + inventoryUuid).show();
        $('#' + inventoryUuid).prop('required', true);
        $('#is-custom-' + inventoryUuid).val('0');
    } else {
        $('#history-section-' + inventoryUuid).hide();
        $('#' + inventoryUuid).prop('required', false).val(null).trigger('change');
        $('#custom-section-' + inventoryUuid).show();
        $('#custom-price-' + inventoryUuid).prop('required', true);
        $('#is-custom-' + inventoryUuid).val('1');
    }

    calculateHpp();
}

const rupiah = (number) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR"
    }).format(number);
}

function calculateHpp() {
    let HPP = 0;

    $('[name="is_custom[]"]').each(function () {
        var group = $(this).data('group');
        var isCustom = $(this).val() === '1';
        var qty = parseFloat($('input[name="qty[]"][data-group="' + group + '"]').val()) || 0;

        if (isCustom) {
            var customPrice = parseFloat($('#custom-price-' + group).val()) || 0;
            HPP += customPrice * qty;
        } else {
            var selectedData = $('#' + group).select2('data');
            var pricePerUnit = (selectedData && selectedData.length > 0 && selectedData[0].id)
                ? parseFloat(selectedData[0].pricePerUnit) || 0
                : 0;
            HPP += pricePerUnit * qty;
        }
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
    calculateHpp();
    $('#successAddMenuPrice').modal('show');
    initInventorySelect2();
}

function backToPriceModal() {
    $('#successAddMenuPrice').modal('hide');
    $('#successPrices').modal('show');
}

function addRecipeTemp(element) {
    event.preventDefault();
    let attributes = $('#' + element.id).serializeArray();
    var selectedData = $('#inventories').select2('data')[0];
    var inventoryName = selectedData ? selectedData.text : '';
    var inventoryUnit = selectedData ? selectedData.unit : '';
    var uuid = attributes[0].value;
    var qty = attributes[1].value;
    var rowCount = $('#addMenuPriceTable tbody tr').length + 1;

    var newRow = `<tr>
                <td class="row-number">${rowCount}</td>
                <td>${inventoryName}</td>
                <td>${qty}${inventoryUnit || ''}</td>
                <td>
                    <div class="btn-group btn-group-sm w-100 mb-1" role="group">
                        <button type="button" class="btn btn-outline-secondary active" onclick="switchPriceMode(this, '${uuid}', 'manual')">Manual</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="switchPriceMode(this, '${uuid}', 'history')">Riwayat</button>
                    </div>
                    <div id="history-section-${uuid}" style="display:none; width:100%;">
                        <select id="${uuid}" data-group="${uuid}" name="inventory_history[]" class="form-control" style="width:100%;"></select>
                    </div>
                    <div id="custom-section-${uuid}" style="width:100%;">
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" id="custom-price-${uuid}" data-group="${uuid}" name="custom_price[]" class="form-control" min="1" placeholder="Harga per satuan" oninput="calculateHpp()" required>
                        </div>
                    </div>
                </td>
                <input type="hidden" value="${uuid}" data-group="${uuid}" name="inventory_uuid[]">
                <input type="hidden" value="${qty}" data-group="${uuid}" name="qty[]">
                <input type="hidden" id="is-custom-${uuid}" data-group="${uuid}" name="is_custom[]" value="1">
                <td><button type="button" onclick="removeRow(this)" class="removeRow btn btn-danger w-100">Hapus</button></td>
                </tr>`;

    $('#addMenuPriceTable tbody').append(newRow);
    initHistorySelect2(uuid);
    $('#saveMenuPriceButton').show();
    $('#inventories').val(null).trigger('change');
    $('[name="qtyTemp"]').val('');
    $('#unitPlaceholder').html('');
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
        let isCustomItem = groupedAttributes[index].find(item => item.name === 'is_custom[]');
        let isCustom = isCustomItem && isCustomItem.value === '1';
        let qtyItem = groupedAttributes[index].find(item => item.name === 'qty[]');

        let recipe = { qty: qtyItem ? qtyItem.value : null, is_custom: isCustom ? 1 : 0 };

        if (isCustom) {
            let inventoryUuidItem = groupedAttributes[index].find(item => item.name === 'inventory_uuid[]');
            let customPriceItem = groupedAttributes[index].find(item => item.name === 'custom_price[]');
            recipe.uuid = inventoryUuidItem ? inventoryUuidItem.value : null;
            recipe.custom_price = customPriceItem ? customPriceItem.value : null;
        } else {
            let historyUuidItem = groupedAttributes[index].find(item => item.name === 'inventory_history[]');
            recipe.uuid = historyUuidItem ? historyUuidItem.value : null;
        }

        if (recipe.uuid && recipe.qty) {
            recipes.push(Object.fromEntries(Object.entries(recipe).filter(([_, v]) => v != null)));
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
                    lastPage = customized_datatable.page();
                    customized_datatable.ajax.reload(function () {
                        customized_datatable.page(lastPage).draw(false);
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

function resetImageEdit() {
    $('#imageEdit').val('');
    $('#clearImageFlag').val('0');
    $('#imagePreviewEdit').hide();
    $('#imagePreviewImgEdit').attr('src', '');
    $('#clearImageEdit').hide();
}

function clearImageInput() {
    $('#imageEdit').val('');
    $('#clearImageFlag').val('1');
    $('#imagePreviewEdit').hide();
    $('#imagePreviewImgEdit').attr('src', '');
    $('#clearImageEdit').hide();
}

$('#imageEdit').on('change', function () {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            $('#imagePreviewImgEdit').attr('src', e.target.result);
            $('#imagePreviewEdit').show();
            $('#clearImageEdit').show();
            $('#clearImageFlag').val('0');
        };
        reader.readAsDataURL(file);
    }
});

$('#secondaryEdit').on('hidden.bs.modal', function () {
    resetImageEdit();
    $('#menuCategoryIdEdit').val(null).trigger('change');
});

$('#primary').on('hidden.bs.modal', function () {
    $('#menuCategoryId').val(null).trigger('change');
});

$('#successAddMenuPrice').on('hidden.bs.modal', function () {
    if ($('#inventories').hasClass('select2-hidden-accessible')) {
        $('#inventories').val(null).trigger('change');
    }
    $('#unitPlaceholder').html('');
});
