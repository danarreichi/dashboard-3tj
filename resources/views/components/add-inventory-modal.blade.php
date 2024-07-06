<!--primary theme Modal -->
<div class="modal fade text-left" id="primary" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <div class="d-flex flex-row justify-content-center align-items-center">
                    <h5 class="modal-title white" id="myModalLabel160">
                        <i style="font-size: 1.2rem;" class="bi bi-person-plus-fill me-2"></i>Tambah inventory
                    </h5>
                </div>
            </div>
            <form id="addInventoryForm" onsubmit="addInventory(this)">
                <div class="modal-body">
                    <label for="name" class="form-label">Nama</label>
                    <div class="form-group">
                        <input id="name" name="name" type="text" placeholder="Masukkan nama barang yang diinginkan" class="form-control" required>
                    </div>
                    <label for="qty" class="form-label">Stock</label>
                    <div class="form-group">
                        <input id="qty" name="qty" type="number" placeholder="Masukkan stok awal"
                            class="form-control" required>
                    </div>
                    <label for="unit" class="form-label">Unit</label>
                    <div class="form-group">
                        <input type="text" class="form-control" name="unit" placeholder="Masukkan satuan yang diinginkan" id="unit" required>
                    </div>
                    <label for="code" class="form-label">Harga Restock</label>
                    <div class="form-group">
                        <input type="number" class="form-control" min="1" name="price" placeholder="Masukkan harga restock awal" id="price" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal" onclick="clearForm('addInventoryForm'); clearInputErrors();">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Tutup</span>
                    </button>
                    <button type="submit" class="btn btn-primary ms-1">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
