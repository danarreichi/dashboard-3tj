<!--primary theme Modal -->
<div class="modal fade text-left" id="primary" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <div class="d-flex flex-row justify-content-center align-items-center">
                    <h5 class="modal-title white" id="myModalLabel160">
                        <i style="font-size: 1.2rem;" class="bi bi-person-plus-fill me-2"></i>Tambah account
                    </h5>
                </div>
            </div>
            <form id="addAccountForm" onsubmit="addAccount(this)">
                <div class="modal-body">
                    <label for="username" class="form-label">Username</label>
                    <div class="form-group">
                        <input id="username" name="username" type="text" placeholder="Masukkan username yang diinginkan"
                            class="form-control" required>
                    </div>
                    <label for="name" class="form-label">Nama</label>
                    <div class="form-group">
                        <input type="text" class="form-control" name="name" placeholder="Masukkan nama Anda"
                            id="name" required>
                    </div>
                    <label for="role" class="form-label">Peran</label>
                    <div class="form-group">
                        <select name="user_role_id" id="role" class="form-control" style="cursor: pointer;">
                            <option value="" style="display: none;" disabled selected>Pilih peran yang diinginkan</option>
                        </select>
                    </div>
                    <label for="password" class="form-label">Password</label>
                    <div class="form-group">
                        <input id="password" name="password" type="password" placeholder="Masukkan password yang kuat"
                            class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal" onclick="clearForm('addAccountForm'); clearInputErrors();">
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
