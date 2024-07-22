<!--primary theme Modal -->
<div class="modal fade text-left" id="primary" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <div class="d-flex flex-row justify-content-center align-items-center">
                    <h5 class="modal-title white" id="myModalLabel160">
                        <i style="font-size: 1.2rem;" class="bi bi-cash-coin me-2"></i><span id="checkoutTotalPayment">Total Pembayaran: Rp0,00</span>
                    </h5>
                </div>
            </div>
            <form id="moneyExchangeForm" onsubmit="continuePay()">
                <div class="modal-body">
                    <input type="hidden" name="checkout_total" id="checkoutTotal" required>
                    <label for="name" class="form-label">Uang Pelanggan</label>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input id="customerMoney" name="customer_money" oninput="setExchangeValue(this)" inputmode="numeric" type="text" min="100" placeholder="Masukkan uang pelanggan" class="form-control" required>
                        </div>
                    </div>
                    <label for="name" class="form-label">Kembalian</label>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input id="customerExchangeMoney" name="customer_exchange_money" type="text" min="100" step="100" placeholder="Kembalian pelanggan" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal" onclick="clearForm('moneyExchangeForm'); clearInputErrors();">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Batal</span>
                    </button>
                    <button type="submit" class="btn btn-primary ms-1" id="continueCheckoutButton" disabled>
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Lanjutkan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
