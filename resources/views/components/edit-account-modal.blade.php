<!--primary theme Modal -->
<div class="modal fade text-left" id="secondaryEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <div class="d-flex flex-row justify-content-center align-items-center">
                    <h5 class="modal-title white" id="myModalLabel160">
                        <i style="font-size: 1.2rem;" class="bi bi-pencil-square me-2"></i>Ubah account
                    </h5>
                </div>
            </div>
            <form id="editAccountForm" onsubmit="editAccount(this)">
                <div class="modal-body">
                    <input type="hidden" name="uuid" id="uuidEdit">
                    <label for="username" class="form-label">Username</label>
                    <div class="form-group">
                        <input id="usernameEdit" name="username" type="text" placeholder="Masukkan username baru yang diinginkan"
                            class="form-control" required>
                    </div>
                    <label for="name" class="form-label">Nama</label>
                    <div class="form-group">
                        <input type="text" class="form-control" name="name" placeholder="Masukkan nama Anda"
                            id="nameEdit" required>
                    </div>
                    <label for="role" class="form-label">Peran</label>
                    <div class="form-group">
                        <select name="user_role_id" id="roleEdit" class="form-control" style="cursor: pointer;">
                            <option value="" style="display: none;" disabled selected>Pilih peran yang diinginkan</option>
                        </select>
                    </div>
                    <label for="password" class="form-label">Password</label>
                    <div class="form-group">
                        <input id="passwordEdit" name="password" type="password" placeholder="Masukkan password baru yang kuat (opsional)"
                            class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="delete" class="btn btn-danger me-auto" onclick="deleteAccount(this)" style="display: none;">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Ban</span>
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="clearForm('editAccountForm'); clearInputErrors();">
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
