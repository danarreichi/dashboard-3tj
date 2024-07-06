<!--primary theme Modal -->
<div class="modal fade text-left" id="infoHistory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <div class="d-flex flex-row flex-grow-1 w-25 justify-content-center align-items-center">
                    <h5 class="modal-title text-black" id="myModalLabel160">
                        <i style="font-size: 1.2rem;" class="bi bi-clock-history me-2"></i>Riwayat Inventory
                    </h5>
                </div>
                <div class="d-flex flex-grow-3 w-75 justify-content-end">
                    <div class="col-6">
                        <label for="dateRange" class="form-label text-black">Filter tanggal:</label>
                        <input type="text" name="dateRange" style="cursor: pointer;" id="dateRange"
                            class="form-control flatpickr-range w-100 mb-3 flatpickr-input" placeholder="Pilih tanggal.."
                            onchange="getDatepickr(this)">
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <label for="modal_inventory_name" class="form-label mb-1 col-6">Nama inventory: <span
                            class="fw-semibold" id="modalInventoryName"></span></label>
                    <label for="modal_inventory_qty" class="form-label col-6">Stock: <span class="fw-semibold"
                            id="modalInventoryQty"></span></label>
                    <div class="collapse" id="collapseExample">
                        <hr>
                        <div class="mb-3">
                            <form id="adjustStockForm" onsubmit="adjustStock(this)">
                                <div class="col-12 mb-1">
                                    <div class="input-group mb-3">
                                        <input type="hidden" name="uuid" id="uuidAdjust">
                                        <input type="hidden" name="status" id="status">
                                        <div class="input-group">
                                            <div class="row w-100">
                                                <div class="col-8">
                                                    <input type="number" name="qty" min="1"
                                                        class="form-control" placeholder="Masukkan qty yang ingin diatur" required>
                                                </div>
                                                <div class="col-2">
                                                    <button class="btn btn-success w-100" data-status="in" onclick="document.getElementById('status').value = this.dataset.status">
                                                        <span>Masuk</span>
                                                    </button>
                                                </div>
                                                <div class="col-2">
                                                    <button class="btn btn-danger w-100" data-status="out" onclick="document.getElementById('status').value = this.dataset.status">
                                                        <span>Keluar</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <hr>
                    </div>
                </div>
                <button class="btn btn-primary collapsed col-12 mt-1 d-flex justify-content-center align-items-center"
                    type="button" data-bs-toggle="collapse" onclick="toggleButtonClass(this)" data-bs-target="#collapseExample" aria-expanded="false"
                    aria-controls="collapseExample">
                    <span>Atur stock</span>
                </button>
                <hr>
                <div class="table-responsive datatable-minimal">
                    <table class="table" id="inventoryHistoryTable">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama akun</th>
                                <th>Status</th>
                                <th>Qty</th>
                                <th>Qty terbarui</th>
                                <th>Tangggal diubah</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- ajax data here --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
