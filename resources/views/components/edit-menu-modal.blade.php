<!--primary theme Modal -->
<div class="modal fade text-left" id="secondaryEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <div class="d-flex flex-row justify-content-center align-items-center">
                    <h5 class="modal-title white" id="myModalLabel160">
                        <i style="font-size: 1.2rem;" class="bi bi-pencil-square me-2"></i>Ubah menu
                    </h5>
                </div>
            </div>
            <form id="editMenuForm" onsubmit="editMenu(this)">
                <div class="modal-body">
                    <input type="hidden" name="uuid" id="uuidEdit" required>
                    <label for="name" class="form-label">Nama</label>
                    <div class="form-group">
                        <input id="nameEdit" name="name" type="text" placeholder="Masukkan nama menu yang diinginkan" class="form-control" required>
                    </div>
                    <label for="unit" class="form-label">Kategori menu</label>
                    <div class="form-group">
                        <select name="menu_category_id" id="menuCategoryIdEdit" class="form-control">
                            <option value="" style="display: none;" selected>Pilih kategori menu</option>
                        </select>
                    </div>
                    <label for="code" class="form-label">Gambar produk</label>
                    <div class="form-group">
                        <input type="file" class="form-control" name="image" id="image" accept="image/png, image/jpeg">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="delete" class="btn btn-danger me-auto" onclick="deleteMenu(this)" style="display: none;">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Hapus</span>
                    </button>
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal" onclick="clearForm('addMenuForm'); clearInputErrors();">
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
