<!--primary theme Modal -->
<div class="modal fade text-left" id="successPrices" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <div class="d-flex flex-row flex-grow-1 w-25 justify-content-center align-items-center">
                    <h5 class="modal-title text-white" id="myModalLabel160">
                        <i style="font-size: 1.2rem;" class="bi bi-cash-coin me-2"></i>Daftar Harga & Bahan
                    </h5>
                </div>
            </div>
            <div class="modal-body">
                <div class="col d-flex align-items-center">
                    <label for="modal_menu_name" class="form-label mb-1 col-9">Nama menu: <span class="fw-semibold"
                            id="modalMenuName"></span></label>
                    <button class="btn btn-success col-3" onclick="addPrice(this)"> Tambah harga </button>
                </div>
                <hr>
                <div class="table-responsive datatable-minimal">
                    <table class="table" id="menuPricesTable">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody class="accordion">
                            {{-- ajax data here --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
