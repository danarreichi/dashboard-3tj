<!--primary theme Modal -->
<div class="modal fade text-left" id="successAddMenuPrice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <div class="d-flex flex-row flex-grow-1 w-25 justify-content-center align-items-center">
                    <h5 class="modal-title text-white" id="myModalLabel160">
                        <i style="font-size: 1.2rem;" class="bi bi-clock-history me-2"></i>Tambah harga & atur bahan
                    </h5>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form id="addRecipeForm" onsubmit="addRecipeTemp(this)">
                        <div class="col-12 mb-1">
                            <div class="input-group mb-3">
                                <div class="input-group">
                                    <div class="row w-100">
                                        <div class="col-5">
                                            <label for="qty" class="form-label">Bahan</label>
                                            <select name="inventory" id="inventories" class="form-control" onchange="changeUnitPlaceholder(this)" required>
                                                <option value="" style="display: none;" selected>Pilih bahan yang
                                                    dibutuhkan</option>
                                            </select>
                                        </div>
                                        <div class="col-5">
                                            <label for="price" class="form-label">Jumlah</label>
                                            <div class="input-group">
                                                <input type="number" name="price" min="1" class="form-control" placeholder="Masukkan jumlah yang dibutuhkan" required>
                                                <span class="input-group-text" id="unitPlaceholder"></span>
                                            </div>
                                        </div>
                                        <div class="col-2 d-grid align-items-end">
                                            <button class="btn btn-success w-100">
                                                <span>Tambahkan</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <hr>
                <div class="table-responsive datatable-minimal">
                    <form id="tempRecipeForm" onsubmit="saveMenuPriceForm(this)">
                        <div class="row mb-2 m-0">
                            <div class="col-4 d-flex align-items-center">
                                <span class="fw-semibold">HPP: Rp0</span>
                            </div>
                            <div class="col-8">
                                <div class="row">
                                    <div class="col-4 d-flex align-items-center justify-content-end">
                                        <label for="price" class="mb-0">Harga Menu</label>
                                    </div>
                                    <div class="col-8">
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" min="1" name="price" id="price" class="form-control" placeholder="Masukkan harga" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <table class="table" id="addMenuPriceTable">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Bahan</th>
                                    <th>Qty</th>
                                    <th>Harga restock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- ajax data here --}}
                            </tbody>
                        </table>
                        <hr>
                        <button class="btn btn-success w-100" id="saveMenuPriceButton" style="display: none;">
                            <span>Simpan</span>
                        </button>
                    </form>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-start">
                <button class="btn btn-secondary" onclick="backToPriceModal()">
                    <span>Kembali</span>
                </button>
            </div>
        </div>
    </div>
</div>
