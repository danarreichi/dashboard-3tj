<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - Mazer Admin Dashboard</title>

    <link rel="shortcut icon"
        href="data:image/svg+xml,%3csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%2033%2034'%20fill-rule='evenodd'%20stroke-linejoin='round'%20stroke-miterlimit='2'%20xmlns:v='https://vecta.io/nano'%3e%3cpath%20d='M3%2027.472c0%204.409%206.18%205.552%2013.5%205.552%207.281%200%2013.5-1.103%2013.5-5.513s-6.179-5.552-13.5-5.552c-7.281%200-13.5%201.103-13.5%205.513z'%20fill='%23435ebe'%20fill-rule='nonzero'/%3e%3ccircle%20cx='16.5'%20cy='8.8'%20r='8.8'%20fill='%2341bbdd'/%3e%3c/svg%3e"
        type="image/x-icon">
    <link rel="shortcut icon"
        href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACEAAAAiCAYAAADRcLDBAAAEs2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS41LjAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgeG1sbnM6ZXhpZj0iaHR0cDovL25zLmFkb2JlLmNvbS9leGlmLzEuMC8iCiAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIKICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIgogICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgZXhpZjpQaXhlbFhEaW1lbnNpb249IjMzIgogICBleGlmOlBpeGVsWURpbWVuc2lvbj0iMzQiCiAgIGV4aWY6Q29sb3JTcGFjZT0iMSIKICAgdGlmZjpJbWFnZVdpZHRoPSIzMyIKICAgdGlmZjpJbWFnZUxlbmd0aD0iMzQiCiAgIHRpZmY6UmVzb2x1dGlvblVuaXQ9IjIiCiAgIHRpZmY6WFJlc29sdXRpb249Ijk2LjAiCiAgIHRpZmY6WVJlc29sdXRpb249Ijk2LjAiCiAgIHBob3Rvc2hvcDpDb2xvck1vZGU9IjMiCiAgIHBob3Rvc2hvcDpJQ0NQcm9maWxlPSJzUkdCIElFQzYxOTY2LTIuMSIKICAgeG1wOk1vZGlmeURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiCiAgIHhtcDpNZXRhZGF0YURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiPgogICA8eG1wTU06SGlzdG9yeT4KICAgIDxyZGY6U2VxPgogICAgIDxyZGY6bGkKICAgICAgc3RFdnQ6YWN0aW9uPSJwcm9kdWNlZCIKICAgICAgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWZmaW5pdHkgRGVzaWduZXIgMS4xMC4xIgogICAgICBzdEV2dDp3aGVuPSIyMDIyLTAzLTMxVDEwOjUwOjIzKzAyOjAwIi8+CiAgICA8L3JkZjpTZXE+CiAgIDwveG1wTU06SGlzdG9yeT4KICA8L3JkZjpEZXNjcmlwdGlvbj4KIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+Cjw/eHBhY2tldCBlbmQ9InIiPz5V57uAAAABgmlDQ1BzUkdCIElFQzYxOTY2LTIuMQAAKJF1kc8rRFEUxz9maORHo1hYKC9hISNGTWwsRn4VFmOUX5uZZ36oeTOv954kW2WrKLHxa8FfwFZZK0WkZClrYoOe87ypmWTO7dzzud97z+nec8ETzaiaWd4NWtYyIiNhZWZ2TvE946WZSjqoj6mmPjE1HKWkfdxR5sSbgFOr9Ll/rXoxYapQVik8oOqGJTwqPL5i6Q5vCzeo6dii8KlwpyEXFL519LjLLw6nXP5y2IhGBsFTJ6ykijhexGra0ITl5bRqmWU1fx/nJTWJ7PSUxBbxJkwijBBGYYwhBgnRQ7/MIQIE6ZIVJfK7f/MnyUmuKrPOKgZLpEhj0SnqslRPSEyKnpCRYdXp/9++msneoFu9JgwVT7b91ga+LfjetO3PQ9v+PgLvI1xkC/m5A+h7F32zoLXug38dzi4LWnwHzjeg8UGPGbFfySvuSSbh9QRqZ6H+Gqrm3Z7l9zm+h+iafNUV7O5Bu5z3L/wAdthn7QIme0YAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAJTSURBVFiF7Zi9axRBGIefEw2IdxFBRQsLWUTBaywSK4ubdSGVIY1Y6HZql8ZKCGIqwX/AYLmCgVQKfiDn7jZeEQMWfsSAHAiKqPiB5mIgELWYOW5vzc3O7niHhT/YZvY37/swM/vOzJbIqVq9uQ04CYwCI8AhYAlYAB4Dc7HnrOSJWcoJcBS4ARzQ2F4BZ2LPmTeNuykHwEWgkQGAet9QfiMZjUSt3hwD7psGTWgs9pwH1hC1enMYeA7sKwDxBqjGnvNdZzKZjqmCAKh+U1kmEwi3IEBbIsugnY5avTkEtIAtFhBrQCX2nLVehqyRqFoCAAwBh3WGLAhbgCRIYYinwLolwLqKUwwi9pxV4KUlxKKKUwxC6ZElRCPLYAJxGfhSEOCz6m8HEXvOB2CyIMSk6m8HoXQTmMkJcA2YNTHm3congOvATo3tE3A29pxbpnFzQSiQPcB55IFmFNgFfEQeahaAGZMpsIJIAZWAHcDX2HN+2cT6r39GxmvC9aPNwH5gO1BOPFuBVWAZue0vA9+A12EgjPadnhCuH1WAE8ivYAQ4ohKaagV4gvxi5oG7YSA2vApsCOH60WngKrA3R9IsvQUuhIGY00K4flQG7gHH/mLytB4C42EgfrQb0mV7us8AAMeBS8mGNMR4nwHamtBB7B4QRNdaS0M8GxDEog7iyoAguvJ0QYSBuAOcAt71Kfl7wA8DcTvZ2KtOlJEr+ByyQtqqhTyHTIeB+ONeqi3brh+VgIN0fohUgWGggizZFTplu12yW8iy/YLOGWMpDMTPXnl+Az9vj2HERYqPAAAAAElFTkSuQmCC"
        type="image/png">


    <link rel="stylesheet" crossorigin href="{{ asset('dist/assets/compiled/css/app.css') }}">
    <link rel="stylesheet" crossorigin href="{{ asset('dist/assets/compiled/css/app-dark.css') }}">
    {{-- SweetAlert2 --}}
    <link rel="stylesheet" crossorigin href="{{ asset('dist/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
    {{-- DataTables --}}
    <link rel="stylesheet"
        href="{{ asset('dist/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" crossorigin href="{{ asset('dist/assets/compiled/css/table-datatable-jquery.css') }}">
    <style>
        .menu-category-card {
            transition: all 300ms;
            user-select: none;
        }

        .menu-category-card:hover {
            text-shadow:
                -1px 1px 0 #2DD785,
                1px 1px 0 #2DD785;
            transition: all 300ms;
            color: #1e1e2d !important;
            background-color: #00ffb3 !important;
        }

        .menu-card {
            transition: all 300ms;
            user-select: none;
        }

        .menu-card:focus {
            transition: all 300ms;
            color: #1e1e2d !important;
            background-color: #00ffb3 !important;
        }

        .menu-card.clicked {
            transform: translateY(4px);
            transition: all 300ms;
            color: #1e1e2d !important;
            background-color: #00ffb3 !important;
        }

        .menu-category-card-selected {
            color: #1e1e2d !important;
            background-color: #00ffb3 !important;
        }

        .menu-category-card.clicked {
            transition: all 300ms;
            font-weight: 700;
            transform: translateY(4px);
        }

        .scrollable-accordion {
            max-height: 180px;
            /* Atur tinggi maksimum sesuai kebutuhan Anda */
            overflow-y: scroll;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        #kategoriMenu {
            overflow-x: scroll;
            -ms-overflow-style: none;
            /* Internet Explorer 10+ */
            scrollbar-width: none;
            /* Firefox */
        }

        #kategoriMenu::-webkit-scrollbar {
            display: none;
            /* Safari and Chrome */
        }
    </style>
</head>

<body>
    <script src="{{ asset('dist/assets/static/js/initTheme.js') }}"></script>
    <div id="app">
        <x-sidebar></x-sidebar>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading d-flex align-items-center justify-content-between">
                <h3>Pesan</h3>
                <x-profile-dropdown></x-profile-dropdown>
            </div>
            <div class="page-content row flex-grow-1">
                <div class="col-lg-9 col-md-12 d-flex flex-column">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h4 class="card-title">Kategori menu</h4>
                        </div>
                        <div class="card-body w-100">
                            <div class="d-flex flex-row flex-nowrap overflow-x-auto gap-3 pb-2" id="kategoriMenu">
                                {{-- kategori menu disini --}}
                            </div>
                        </div>
                    </div>
                    <div class="card flex-grow-1 mb-0">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Daftar menu</h4>
                            <div class="d-flex">
                                <input class="form-control me-2" type="search" id="search" placeholder="Cari menu"
                                    aria-label="Search" oninput="debouncedSearch(this)">
                            </div>
                        </div>
                        <div class="card-body w-100">
                            <div class="d-flex flex-row flex-wrap overflow-y-auto gap-3 pb-2" id="menuPrices">
                                {{-- kategori menu disini --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-12 mt-3 mt-sm-3 mt-md-3 m-xl-0">
                    <div class="card h-100">
                        <div class="card-header">
                            <h4 class="card-title">Pesanan</h4>
                        </div>
                        <div class="card-body d-flex flex-column w-100">
                            <div class="row-6 mb-4">
                                <form id="chart" style="height: 180px;" onsubmit="checkout(this)">
                                    <div class="accordion scrollable-accordion" id="chartList">
                                        <p class="fs-5 text-center">--Keranjang kosong--</p>
                                    </div>
                                </form>
                                <hr>
                            </div>
                            <div class="row-6 d-flex justify-content-between flex-column h-100">
                                <div class="d-flex flex-column align-self-start w-100" id="priceInfo">
                                    <div class="d-flex justify-content-between" id="subTotal">
                                        <p class="fs-6">Subtotal: </p>
                                        <p class="fs-6 fw-bolder">Rp0,00</p>
                                    </div>
                                    <div class="d-flex justify-content-between" id="discount">
                                        <p class="fs-6">Diskon: </p>
                                        <p class="fs-6 fw-bolder">Rp0,00</p>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between" id="totalPayment">
                                        <p class="fs-5">Total: </p>
                                        <p class="fs-5 fw-bolder">Rp0,00</p>
                                    </div>
                                </div>
                                <div>
                                    <div class="d-flex flex-column mb-3">
                                        <p class="text-body-secondary mb-3 fw-semibold">Cara pembayaran:</p>
                                        <div class="d-flex gap-2" id="paymentMethods">
                                            <div
                                                class="d-flex flex-column align-items-center justify-content-center w-100">
                                                <button type="button" class="btn btn-outline-primary active w-100 mb-1"
                                                    onclick="changeActivePaymentMethod(this)">
                                                    <i class="bi bi-cash me-1"></i>
                                                </button>
                                                <p class="fw-semibold">Cash</p>
                                            </div>
                                            <div
                                                class="d-flex flex-column align-items-center justify-content-center w-100">
                                                <button type="button" class="btn btn-outline-primary w-100 mb-1"
                                                    onclick="changeActivePaymentMethod(this)">
                                                    <i class="bi bi-qr-code me-1"></i>
                                                </button>
                                                <p class="fw-semibold">QRIS</p>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary w-100" id="buttonProceedOrder" disabled>
                                        Lanjutkan Pembayaran
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="{{ asset('dist/assets/static/js/components/dark.js') }}"></script>
    <script src="{{ asset('dist/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>


    <script src="{{ asset('dist/assets/compiled/js/app.js') }}"></script>
    <script src="{{ asset('dist/assets/extensions/jquery/jquery.min.js') }}"></script>
    {{-- DataTables --}}
    <script src="{{ asset('dist/assets/extensions/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('dist/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('dist/assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
    {{-- Page JS --}}
    <script src="{{ asset('page-js-min/base.js') }}"></script>
    <script src="{{ asset('page-js-min/menu-category.js') }}"></script>
    <script>
        var selectedMenu = [];
        var selectedCategory;

        $(document).ready(function() {
            getProfile();
            getMenuCategory();
            getMenuPrices();
            scrollCategoryMenu();
        });

        function selectCategory(element) {
            selectedCategory = element.dataset.uuid;
            $('.menu-category-card').each(function() {
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
            if (loadMenu === true) {
                return;
            }
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

        function getMenus(menuUuids) {
            loadMenu = true;
            var queryParams = {
                'uuids': menuUuids
            };
            if (menuUuids.length == 0) {
                $('#chartList').empty();
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
                success: function(response) {
                    $.each(response.data, function(index, item) {
                        var accordion = `<div class="accordion-item" data-uuid="${item.price.uuid}">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed fw-bolder" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#panelMenu${item.uuid}">
                                                ${item.name}
                                            </button>
                                        </h2>
                                        <div id="panelMenu${item.uuid}" class="accordion-collapse collapse">
                                            <div class="accordion-body">
                                                <div class="input-group">
                                                    <span class="input-group-text" id="basic-addon1" data-price-uuid="${item.price.uuid}" onclick="decreaseValue(this)" style="cursor: pointer;">-</span>
                                                    <input type="hidden" name="uuid[]" value="${item.price.uuid}" required>
                                                    <input type="number" name="qty[]" class="form-control" min="0" data-price-uuid="${item.price.uuid}" oninput="debouncedvalidateQty(this)" max="${item.price.stock_remaining}" min="0" value="0" required>
                                                    <span class="input-group-text" id="basic-addon2" data-price-uuid="${item.price.uuid}" onclick="increaseValue(this)" style="cursor: pointer;">+</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`
                        if ($(`#panelMenu${item.uuid}`).length === 0) $('#chartList').append(accordion);
                    });
                    refreshStock(selectedCategory);
                    loadMenu = false;
                },
                error: function(xhr, status, error) {
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
                selectedMenu = selectedMenu.filter(item => item !== element.dataset.priceUuid);
                if (selectedMenu.length == 0) $('#chartList').append(
                    `<p class="fs-5 text-center">--Keranjang kosong--</p>`);
            }
        }

        function refreshStock(uuid, q) {
            var data = [];
            var queryParams = {
                'category_uuid': uuid,
                'q': (q) ? q : $('#search').val()
            };
            var idx = 0;
            $.each($('#chart').serializeArray(), function(index, item) {
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
                    query_params: queryParams
                },
                headers: headers,
                success: function(response) {
                    $('#menuPrices').empty();
                    $('#subTotal').find('.fs-6.fw-bolder').html(response.meta.subtotal);
                    $('#discount').find('.fs-6.fw-bolder').html(response.meta.discount);
                    $('#totalPayment').find('.fs-5.fw-bolder').html(response.meta.total);

                    if (selectedMenu.length > 0) {
                        $('#buttonProceedOrder').prop('disabled', false);
                    } else {
                        $('#buttonProceedOrder').prop('disabled', true);
                    }

                    $.each(response.data, function(index, item) {
                        let clicked = (selectedMenu.includes(item.uuid)) ? 'clicked' : '';
                        var card = `<div class="card bg-secondary m-0 text-white menu-card ${clicked}" title="${item.name}" style="cursor: pointer; ${(item.availability !== true) ? 'opacity: 0.5;' : ''}" data-uuid="${item.uuid}" ${(item.availability == true)?`onclick="selectMenu(this)"`:``}>
                                        <img src="${pageHost}${item.image}" class="card-img-top" style="width: 200px; height: 200px; object-fit: cover;">
                                        <div class="card-body">
                                            <h5 class="card-title" style="width:140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${item.name}</h5>
                                            <p class="card-text">${item.price}</p>
                                            ${(item.availability !== true)?`<p class="card-text">Habis</p>`:`<p class="card-text">Stok: ${item.stock_remaining}</p>`}
                                        </div>
                                    </div>`;
                        $('#menuPrices').append(card);
                    });
                    $.each(response.data, function(index, item) {
                        if ($('.accordion-item').hasClass('accordion-item')) {
                            $('input[type="number"][name="qty[]"]').each(function() {
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
                error: function(xhr, status, error) {
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
                success: function(response) {
                    $('#kategoriMenu').empty();
                    var allCard = `<div class="card bg-secondary menu-category-card-selected clicked m-0 text-white menu-category-card" style="cursor: pointer; flex-shrink: 0; min-width: 150px;" onclick="selectCategory(this)">
                                        <div class="card-body">
                                            <p class="card-text">Semua</p>
                                        </div>
                                    </div>`;

                    $('#kategoriMenu').append(allCard);
                    $.each(response.data, function(index, item) {
                        var card = `<div class="card bg-secondary m-0 text-white menu-category-card" style="cursor: pointer; flex-shrink: 0; min-width: 150px;" data-uuid="${item.uuid}" onclick="selectCategory(this)">
                                        <div class="card-body">
                                            <p class="card-text">${item.name}</p>
                                        </div>
                                    </div>`;
                        $('#kategoriMenu').append(card);
                    });
                },
                error: function(xhr, status, error) {
                    console.error(JSON.parse(xhr.responseText).message);
                }
            });
        }

        const debouncedGetMenu = debounce(getMenus, 500);
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
            return function() {
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
                success: function(response) {
                    $('#menuPrices').empty();
                    $.each(response.data, function(index, item) {
                        let clicked = (selectedMenu.includes(item.uuid)) ? 'clicked' : '';
                        var card = `<div class="card bg-secondary m-0 text-white menu-card ${clicked}" title="${item.name}" style="cursor: pointer; ${(item.availability !== true) ? 'opacity: 0.5;' : ''}" data-uuid="${item.uuid}" ${(item.availability == true)?`onclick="selectMenu(this)"`:``}>
                                        <img src="${pageHost}${item.image}" class="card-img-top" style="width: 200px; height: 200px; object-fit: cover;">
                                        <div class="card-body">
                                            <h5 class="card-title" style="width:140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${item.name}</h5>
                                            <p class="card-text">${item.price}</p>
                                            ${(item.availability !== true)?`<p class="card-text">Habis</p>`:`<p class="card-text">Stok: ${item.stock_remaining}</p>`}
                                        </div>
                                    </div>`;
                        $('#menuPrices').append(card);
                    });
                },
                error: function(xhr, status, error) {
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

            $kategoriMenu.on('mousedown', function(e) {
                isDown = true;
                $kategoriMenu.addClass('active');
                startX = e.pageX - $kategoriMenu.offset().left;
                scrollLeft = $kategoriMenu.scrollLeft();
            });

            $kategoriMenu.on('mouseleave', function() {
                isDown = false;
                $kategoriMenu.removeClass('active');
            });

            $kategoriMenu.on('mouseup', function() {
                isDown = false;
                $kategoriMenu.removeClass('active');
            });

            $kategoriMenu.on('mousemove', function(e) {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - $kategoriMenu.offset().left;
                const walk = (x - startX) * 1; //scroll-fast
                $kategoriMenu.scrollLeft(scrollLeft - walk);
            });
        }
    </script>
</body>

</html>
