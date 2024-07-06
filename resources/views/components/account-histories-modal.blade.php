<!--primary theme Modal -->
<div class="modal fade text-left" id="infoHistory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <div class="d-flex flex-row flex-grow-1 w-25 justify-content-center align-items-center">
                    <h5 class="modal-title text-black" id="myModalLabel160">
                        <i style="font-size: 1.2rem;" class="bi bi-clock-history me-2"></i>Riwayat Account
                    </h5>
                </div>
                <div class="d-flex flex-grow-3 w-75 justify-content-end">
                    <div class="col-6">
                        <label for="dateRange" class="form-label text-black">Filter tanggal:</label>
                        <input type="text" name="dateRange" id="dateRange" style="cursor: pointer;" class="form-control flatpickr-range w-100 mb-3 flatpickr-input" placeholder="Pilih tanggal.." onchange="getDatepickr(this)">
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <label for="user_name" class="form-label m-0">Nama account: <span class="fw-semibold" id="modalUsername"></span></label>
                <hr>
                <div class="table-responsive datatable-minimal">
                    <table class="table" id="accountHistoryTable">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama Inventory</th>
                                <th>Status</th>
                                <th>Qty</th>
                                <th>Qty terbarui</th>
                                <th>Harga</th>
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
