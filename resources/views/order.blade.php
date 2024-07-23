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
            max-height: 200px;
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
                            <div class="row-6">
                                <form id="chart" class="d-flex justify-content-center align-items-center"
                                    style="height: 200px;" onsubmit="checkout(this)">
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
                                        <p class="fs-6 mb-0">Diskon: </p>
                                        <p class="fs-6 mb-0 fw-bolder">Rp0,00</p>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between" id="totalPayment">
                                        <p class="fs-5">Total: </p>
                                        <p class="fs-5 fw-bolder">Rp0,00</p>
                                    </div>
                                </div>
                                <div>


                                    <div class="mb-4" id="discountDiv" style="display: none;">
                                        <p class="text-body-secondary fw-semibold">Diskon:</p>
                                        <div class="input-group">
                                            <button class="btn btn-primary dropdown-toggle" type="button"
                                                id="dropdownDiscountType" data-bs-toggle="dropdown"
                                                aria-expanded="false">Nominal</button>
                                            <ul class="dropdown-menu">
                                                <li style="cursor: pointer;"><a class="dropdown-item"
                                                        data-type="Nominal"
                                                        onclick="changeDiscountType(this)">Nominal</a></li>
                                                <li style="cursor: pointer;"><a class="dropdown-item"
                                                        data-type="Persentase"
                                                        onclick="changeDiscountType(this)">Persentase</a></li>
                                            </ul>
                                            <input type="hidden" name="discountType" value="nominal" required>
                                            <input type="number" class="form-control" name="discount" min="0"
                                                oninput="debouncedValidateDiscount(this)" placeholder="Rp">
                                        </div>
                                    </div>

                                    <div class="mb-3" id="paymentMethodDiv">
                                        <div class="d-flex flex-column">
                                            <p class="text-body-secondary fw-semibold">Cara pembayaran:</p>
                                            <div class="d-flex gap-2" id="paymentMethods">
                                                <div
                                                    class="d-flex flex-column align-items-center justify-content-center w-100">
                                                    <button type="button" data-method="cash"
                                                        class="btn btn-outline-primary active w-100 mb-1"
                                                        onclick="changeActivePaymentMethod(this)">
                                                        <i class="bi bi-cash me-1"></i>
                                                    </button>
                                                    <p class="fw-semibold mb-0">Cash</p>
                                                </div>
                                                <div
                                                    class="d-flex flex-column align-items-center justify-content-center w-100">
                                                    <button type="button" data-method="qris"
                                                        class="btn btn-outline-primary w-100 mb-1"
                                                        onclick="changeActivePaymentMethod(this)">
                                                        <i class="bi bi-qr-code me-1"></i>
                                                    </button>
                                                    <p class="fw-semibold mb-0">QRIS</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button class="btn btn-primary w-100" id="buttonProceedOrder"
                                        onclick="checkoutConfirmation()" disabled>
                                        Lanjutkan Pembayaran
                                    </button>

                                    <p class="fw-semibold fs-6 text-center mb-0 mt-3" id="formToggleButton"
                                        style="cursor: pointer;">Tambahkan diskon?</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-order-confirmation-modal></x-order-confirmation-modal>
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
    <script src="{{ asset('page-js-min/order.js') }}"></script>
</body>

</html>
