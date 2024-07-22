const apiHost = window.location.hostname;
const apiPort = window.location.port;
const host = `http://${apiHost}:${apiPort}/api/console/v1/`;
const pageHost = `http://${apiHost}:${apiPort}/`;

var selectedMenu = [];
var selectedCategory;
var discountType = "nominal";

$(document).ready(function () {
    getProfile();
    getMenuCategory();
    getMenuPrices();
    scrollCategoryMenu();
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
            if (response.user.user_role.id === 'admin') $('#addMenuCategoryBtn').show();
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

$('#formToggleButton').click(function () {
    $('#discountDiv, #paymentMethodDiv').toggle();
    if ($('#discountDiv').is(':visible')) {
        $(this).html('Metode pembayaran?');
    } else {
        $(this).html('Tambahkan diskon?');
    }
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

function selectCategory(element) {
    selectedCategory = element.dataset.uuid;
    $('.menu-category-card').each(function () {
        $(this).removeClass('menu-category-card-selected');
        $(this).removeClass('clicked');
    });
    if ($(element).hasClass('menu-category-card-selected')) {
        $(element).removeClass('menu-category-card-selected');
        $(element).toggleClass('clicked');
    } else {
        $(element).toggleClass('clicked');
        $(element).addClass('menu-category-card-selected');
    }
    if (!element.dataset.uuid) $('#search').removeAttr('data-category-uuid');
    $('#search').attr('data-category-uuid', element.dataset.uuid);
    if ($('.accordion-item').hasClass('accordion-item')) {
        refreshStock(element.dataset.uuid);
    } else {
        getMenuPrices(element.dataset.uuid);
    }
}

let loadMenu = false;

function selectMenu(element) {
    // if (loadMenu === true) return;
    $(element).toggleClass('clicked');
    if ($(element).hasClass('clicked')) {
        selectedMenu.push(element.dataset.uuid);
        debouncedGetMenu(selectedMenu);
        if (selectedMenu.length > 0) $('#chartList').find('p.fs-5.text-center').remove();
    } else {
        let hehe = $('#chartList').find(`.accordion-item[data-uuid="${element.dataset.uuid}"]`).find(
            'input[type="number"][name="qty[]"]').val(0);
        if ($('.accordion-item').hasClass('accordion-item')) refreshStock(selectedCategory);
        $('#chartList').find(`.accordion-item[data-uuid="${element.dataset.uuid}"]`).remove();
        selectedMenu = selectedMenu.filter(item => item !== element.dataset.uuid);
        debouncedGetMenu(selectedMenu);
    }
}

function checkoutConfirmation() {
    let payment = $('#paymentMethods').find('button.active');
    if (payment.data('method') === "cash") {
        $('#customerMoney').val(null);
        $('#customerExchangeMoney').val(null);
        $('#primary').modal('show');
    } else {
        Swal2.fire({
            icon: "question",
            title: "Periksa pembayaran QRIS",
            text: "Apakah anda ingin menkonfirmasi transaksi ini?",
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak',
            reverseButtons: false // optional, makes the "No" button come first
        }).then((result) => {
            if (result.isConfirmed) {
                continueCheckout();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                console.log('Operation cancelled');
            }
        });
    }
}

function continuePay(element) {
    event.preventDefault();
    continueCheckout();
    $('#primary').modal('hide');
}

function continueCheckout() {
    var data = [];
    var idx = 0;
    $.each($('#chart').serializeArray(), function (index, item) {
        if (item.name === 'uuid[]') {
            data.push({
                uuid: item.value
            });
        } else {
            data[idx].qty = item.value;
            idx++;
        }
    });

    let payment = $('#paymentMethods').find('button.active');

    if (data.length === 0) {
        Toast.fire({
            icon: 'warning',
            title: 'Pilih menu terlebih dahulu',
            timer: 1500
        });
        return;
    } else {
        var headers = {
            'Authorization': 'Bearer ' + localStorage.getItem("bearer")
        };
        $.ajax({
            url: host + 'checkout',
            type: 'POST',
            data: {
                data: data,
                discount: {
                    type: $('#discountDiv').find('input[name="discountType"]').val(),
                    qty: $('#discountDiv').find('input[type="number"][name="discount"]').val() || 0
                },
                payment_method: payment.data('method')
            },
            headers: headers,
            success: function (response) {
                selectedMenu = [];

                $('#chartList').empty();
                $('#chart').addClass('align-items-center');
                $('#chartList').append(`<p class="fs-5 text-center">--Keranjang kosong--</p>`);

                $('#subTotal').find('.fs-6.fw-bolder').html("Rp0,00");
                $('#discount').find('.fs-6.fw-bolder').html("Rp0,00");
                $('#totalPayment').find('.fs-5.fw-bolder').html("Rp0,00");

                $('#discountDiv').find('input[type="number"]').val(null);

                $('#continueCheckoutButton').attr('disabled', true);
                let search = $('#search');
                getMenuPrices(search.data('categoryUuid'), search.val());

                Toast.fire({
                    icon: 'success',
                    title: 'Pembelian sukses',
                    timer: 1500
                });
            },
            error: function (xhr, status, error) {
                clearInputErrors();
                let errors = JSON.parse(xhr.responseText).errors;
                $.each(errors, function (index, item) {
                    let parts = index.split('.');
                    let idx = parts.find(part => !isNaN(part));
                    let inputField = $('input[name="qty[]"]').eq(idx);
                    let inputFieldParent = inputField.parent();
                    inputField.addClass('is-invalid');
                    inputFieldParent.after('<div class="invalid-feedback d-block">' + item +
                        '</div>');
                });
                Toast.fire({
                    icon: 'error',
                    title: 'Jumlah pembelian tidak boleh 0',
                    timer: 1500
                });
            }
        });
    }
}

function setExchangeValue(element) {

    // Remove all non-digit characters
    let value = element.value.replace(/\D/g, '');

    // Convert to number and format it
    if (value) {
        value = parseFloat(value).toLocaleString('de-DE', {
            maximumFractionDigits: 2
        });
    }

    // Update the input value
    element.value = value;

    let totalCheckout = parseInt($('#checkoutTotal').val());
    let exchange = parseInt(value.replace(/\D/g, '')) - totalCheckout;
    let exchangeDisplay = parseFloat(exchange).toLocaleString('de-DE', {
        maximumFractionDigits: 2
    })
    if (exchange > 0) {
        $('#customerExchangeMoney').val(exchangeDisplay);
        $('#continueCheckoutButton').attr('disabled', false);
    }
    if (exchange < 0 || isNaN(value)) {
        $('#customerExchangeMoney').val(null);
        $('#continueCheckoutButton').attr('disabled', true);
    }
}

function checkout(element) {
    event.preventDefault();
}

function getMenus(menuUuids) {
    loadMenu = true;
    var queryParams = {
        'uuids': menuUuids
    };
    if (menuUuids.length == 0) {
        $('#chartList').empty();
        $('#chart').addClass('align-items-center');
        $('#chartList').append(`<p class="fs-5 text-center">--Keranjang kosong--</p>`);
        loadMenu = false;
        return;
    }
    var headers = {
        'Authorization': 'Bearer ' + localStorage.getItem("bearer")
    };
    $.ajax({
        url: host + 'menu',
        type: 'GET',
        data: queryParams,
        headers: headers,
        success: function (response) {
            $.each(response.data, function (index, item) {
                var accordion = `<div class="accordion-item" data-uuid="${item.price.uuid}">
                                <h2 class="accordion-header">
                                    <button class="btn w-100 collapsed d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#panelMenu${item.uuid}" title="${item.name}">
                                        <p class="fw-bolder m-2 text-start" style="width:140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${item.name}</p>
                                        <p class="fs-6 fw-semibold m-2 w-auto">@${item.price_display}</p>
                                    </button>
                                </h2>
                                <div id="panelMenu${item.uuid}" class="accordion-collapse collapse">
                                    <div class="accordion-body">
                                        <div class="input-group">
                                            <span class="input-group-text" data-price-uuid="${item.price.uuid}" onclick="decreaseValue(this)" style="cursor: pointer; user-select: none;">-</span>
                                            <input type="hidden" name="uuid[]" value="${item.price.uuid}" required>
                                            <input type="number" name="qty[]" class="form-control text-center" min="0" data-price-uuid="${item.price.uuid}" oninput="debouncedvalidateQty(this)" max="${item.price.stock_remaining}" min="0" value="${(item.price.stock_remaining > 0) ? 1 : 0}" required>
                                            <span class="input-group-text" data-price-uuid="${item.price.uuid}" onclick="increaseValue(this)" style="cursor: pointer; user-select: none;">+</span>
                                        </div>
                                    </div>
                                </div>
                            </div>`
                if ($(`#panelMenu${item.uuid}`).length === 0) {
                    $('#chart').removeClass('align-items-center');
                    $('#chartList').append(accordion);
                }
            });
            $('.accordion').find('.accordion-item').each(function(){
                let uuid = $(this).data('uuid');
                if(!selectedMenu.includes(uuid)) $(this).remove();
            });
            refreshStock(selectedCategory);
            loadMenu = false;
        },
        error: function (xhr, status, error) {
            console.error(JSON.parse(xhr.responseText).message);
        }
    });
}

function increaseValue(element) {
    var input = $(`input[name="qty[]"][data-price-uuid="${element.dataset.priceUuid}"]`);
    var value = parseInt(input.val());
    var max = parseInt($(input).attr('max'));
    value++;
    if (parseInt(input.val()) === max) return;
    if (parseInt(value) > parseInt(max)) value = max;
    input.val(value);
    debouncedvalidateQty(input[0]);
}

function decreaseValue(element) {
    var input = $(`input[name="qty[]"][data-price-uuid="${element.dataset.priceUuid}"]`);
    var value = parseInt(input.val());
    var min = parseInt($(input).attr('min'));
    value--;
    if (parseInt(input.val()) === min) return;
    if (parseInt(value) < parseInt(min)) value = min;
    input.val(value);
    debouncedvalidateQty(input[0]);
}

function validateQty(element) {
    if (parseInt(element.value) > parseInt(element.max)) element.value = element.max;
    if (parseInt(element.value) < parseInt(element.min)) element.value = element.min;
    refreshStock(selectedCategory);
    if (parseInt(element.value) == 0) {
        $(`.accordion-item[data-uuid="${element.dataset.priceUuid}"]`).remove();
        Toast.fire({
            icon: 'success',
            title: '1 menu pada keranjang terhapus',
            timer: 1500
        });
        selectedMenu = selectedMenu.filter(item => item !== element.dataset.priceUuid);
        if (selectedMenu.length == 0) {
            $('#chart').addClass('align-items-center');
            $('#chartList').append(`<p class="fs-5 text-center">--Keranjang kosong--</p>`);
        }
    }
}

function validateDiscount(element) {
    if (parseInt($(element).val()) < parseInt($(element).attr('min'))) $(element).val(0);
    if ($(element).attr('max')) {
        if (parseInt($(element).val()) > parseInt($(element).attr('max'))) $(element).val(parseInt($(element).attr(
            'max')));
    }
    refreshStock(selectedCategory);
}

function changeDiscountType(element) {
    $('#dropdownDiscountType').html(`${element.dataset.type}`);
    let input = $('#discountDiv').find('input[type="number"]');
    let type = $('#discountDiv').find('input[name="discountType"]');
    input.val(null);
    if (element.dataset.type === "Nominal") {
        input.removeAttr('max');
        input.attr('placeholder', 'Rp');
        type.val('nominal');
    }
    if (element.dataset.type === "Persentase") {
        input.attr('max', 100);
        input.attr('placeholder', '%');
        type.val('persentase');
    }
}

function refreshStock(uuid, q) {
    var data = [];
    var queryParams = {
        'category_uuid': uuid,
        'q': (q) ? q : $('#search').val()
    };
    var idx = 0;
    $.each($('#chart').serializeArray(), function (index, item) {
        if (item.name === 'uuid[]') {
            data.push({
                uuid: item.value
            });
        } else {
            data[idx].qty = item.value;
            idx++;
        }
    });
    var headers = {
        'Authorization': 'Bearer ' + localStorage.getItem("bearer")
    };
    $.ajax({
        url: host + 'menu-price',
        type: 'POST',
        data: {
            data: data,
            discount: {
                type: $('#discountDiv').find('input[name="discountType"]').val(),
                qty: $('#discountDiv').find('input[type="number"][name="discount"]').val() || 0
            },
            query_params: queryParams
        },
        headers: headers,
        success: function (response) {
            $('#menuPrices').empty();
            $('#subTotal').find('.fs-6.fw-bolder').html(response.meta.subtotal);
            $('#discount').find('.fs-6.fw-bolder').html(response.meta.discount);
            $('#totalPayment').find('.fs-5.fw-bolder').html(response.meta.total);
            $('#checkoutTotal').val(response.meta.total_calc);
            $('#checkoutTotalPayment').html(`Total Pembayaran: ${response.meta.total}`);

            if (selectedMenu.length > 0) {
                $('#buttonProceedOrder').prop('disabled', false);
            } else {
                $('#buttonProceedOrder').prop('disabled', true);
            }

            $.each(response.data, function (index, item) {
                let clicked = (selectedMenu.includes(item.uuid)) ? 'clicked' : '';
                var card = `<div class="card bg-secondary m-0 text-white menu-card ${clicked}" title="${item.name}" style="cursor: pointer; ${(item.availability !== true) ? 'opacity: 0.5;' : ''}" data-uuid="${item.uuid}" ${(item.availability == true) ? `onclick="selectMenu(this)"` : ``}>
                                <img src="${pageHost}${item.image}" class="card-img-top" style="width: 200px; height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title" style="width:140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${item.name}</h5>
                                    <p class="card-text">${item.price}</p>
                                    ${(item.availability !== true) ? `<p class="card-text">Habis</p>` : `<p class="card-text">Stok: ${item.stock_remaining}</p>`}
                                </div>
                            </div>`;
                $('#menuPrices').append(card);
            });
            $.each(response.data, function (index, item) {
                if ($('.accordion-item').hasClass('accordion-item')) {
                    $('input[type="number"][name="qty[]"]').each(function () {
                        let priceUuid = $(this).data('priceUuid');
                        if (priceUuid === item.uuid) {
                            let value = $(this).val();
                            let maxVal = parseInt(value) + parseInt(item
                                .stock_remaining);
                            $(this).attr({
                                'max': maxVal
                            });
                            if (parseInt(maxVal) === 0) {
                                let parent = $(this).closest('.accordion-item');
                                let priceUuid = parent.data('uuid');
                                selectedMenu = selectedMenu.filter(item => item !==
                                    priceUuid);
                                parent.remove();
                                $(`.menu-card[data-uuid="${priceUuid}"]`).toggleClass(
                                    'clicked');
                            }
                        }
                    });
                }
            });
        },
        error: function (xhr, status, error) {
            console.error(JSON.parse(xhr.responseText).message);
        }
    });
}

function getMenuCategory(element) {
    var queryParams = {
        'hasMenu': true
    };
    var headers = {
        'Authorization': 'Bearer ' + localStorage.getItem("bearer")
    };
    $.ajax({
        url: host + 'menu-category',
        type: 'GET',
        data: queryParams,
        headers: headers,
        success: function (response) {
            $('#kategoriMenu').empty();
            var allCard = `<div class="card bg-secondary menu-category-card-selected clicked m-0 text-white menu-category-card" style="cursor: pointer; flex-shrink: 0; min-width: 150px;" onclick="selectCategory(this)">
                                <div class="card-body">
                                    <p class="card-text">Semua</p>
                                </div>
                            </div>`;

            $('#kategoriMenu').append(allCard);
            $.each(response.data, function (index, item) {
                var card = `<div class="card bg-secondary m-0 text-white menu-category-card" style="cursor: pointer; flex-shrink: 0; min-width: 150px;" data-uuid="${item.uuid}" onclick="selectCategory(this)">
                                <div class="card-body">
                                    <p class="card-text">${item.name}</p>
                                </div>
                            </div>`;
                $('#kategoriMenu').append(card);
            });
        },
        error: function (xhr, status, error) {
            console.error(JSON.parse(xhr.responseText).message);
        }
    });
}

const debouncedGetMenu = debounce(getMenus, 350);
const debouncedValidateDiscount = debounce(validateDiscount, 500);
const debouncedSearch = debounce(searchMenu, 500);
const debouncedvalidateQty = debounce(validateQty, 500);

function searchMenu(element) {
    if ($('.accordion-item').hasClass('accordion-item')) {
        refreshStock(element.dataset.categoryUuid);
    } else {
        getMenuPrices(element.dataset.categoryUuid, element.value)
    }
}

function debounce(func, delay) {
    let debounceTimer;
    return function () {
        const context = this;
        const args = arguments;
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => func.apply(context, args), delay);
    };
}

function getMenuPrices(uuid, q) {
    var queryParams = {
        'category_uuid': uuid,
        'q': (q) ? q : $('#search').val()
    };
    var headers = {
        'Authorization': 'Bearer ' + localStorage.getItem("bearer")
    };
    $.ajax({
        url: host + 'menu-price',
        type: 'GET',
        data: queryParams,
        headers: headers,
        success: function (response) {
            $('#menuPrices').empty();
            $.each(response.data, function (index, item) {
                let clicked = (selectedMenu.includes(item.uuid)) ? 'clicked' : '';
                var card = `<div class="card bg-secondary m-0 text-white menu-card ${clicked}" title="${item.name}" style="cursor: pointer; ${(item.availability !== true) ? 'opacity: 0.5;' : ''}" data-uuid="${item.uuid}" ${(item.availability == true) ? `onclick="selectMenu(this)"` : ``}>
                                <img src="${pageHost}${item.image}" class="card-img-top" style="width: 200px; height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title" style="width:140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${item.name}</h5>
                                    <p class="card-text">${item.price}</p>
                                    ${(item.availability !== true) ? `<p class="card-text">Habis</p>` : `<p class="card-text">Stok: ${item.stock_remaining}</p>`}
                                </div>
                            </div>`;
                $('#menuPrices').append(card);
            });
        },
        error: function (xhr, status, error) {
            console.error(JSON.parse(xhr.responseText).message);
        }
    });
}

function changeActivePaymentMethod(element) {
    document.querySelectorAll('#paymentMethods .btn').forEach(btn => btn.classList.remove('active'));
    element.classList.add('active');
}

function scrollCategoryMenu() {
    const $kategoriMenu = $('#kategoriMenu');
    let isDown = false;
    let startX;
    let scrollLeft;

    $kategoriMenu.on('mousedown', function (e) {
        isDown = true;
        $kategoriMenu.addClass('active');
        startX = e.pageX - $kategoriMenu.offset().left;
        scrollLeft = $kategoriMenu.scrollLeft();
    });

    $kategoriMenu.on('mouseleave', function () {
        isDown = false;
        $kategoriMenu.removeClass('active');
    });

    $kategoriMenu.on('mouseup', function () {
        isDown = false;
        $kategoriMenu.removeClass('active');
    });

    $kategoriMenu.on('mousemove', function (e) {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - $kategoriMenu.offset().left;
        const walk = (x - startX) * 1; //scroll-fast
        $kategoriMenu.scrollLeft(scrollLeft - walk);
    });
}
