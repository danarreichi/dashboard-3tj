<!--primary theme Modal -->
<div class="modal fade text-left" id="infoHistory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <div class="d-flex flex-row flex-grow-1 w-25 justify-content-center align-items-center">
                    <h5 class="modal-title text-black" id="myModalLabel160">
                        <i style="font-size: 1.2rem;" class="bi bi-clock-history me-2"></i>Riwayat Penjualan
                    </h5>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6">
                        <label for="modal_inventory_name" class="form-label mb-1">Nama produk: <span class="fw-semibold" id="productName"></span></label>
                    </div>
                    <div class="col-6">
                        <label class="form-label mb-1">Total penjualan: <span class="fw-semibold" id="productSalesQty"></span></label>
                        <br>
                        <label class="form-label mb-1">Jumlah pendapatan: <span class="fw-semibold" id="productSales"></span></label>
                    </div>
                </div>
                <hr>
                <div class="table-responsive datatable-minimal">
                    <table class="table" id="saleHistoryTable">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Jumlah terjual</th>
                                <th>Harga produk</th>
                                <th>Pendapatan</th>
                                <th>Tanggal transaksi</th>
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
