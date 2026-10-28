<?php
$page_title    = 'Complete Payment';
$page_subtitle = 'Secure checkout powered by Razorpay';
?>
<?= $this->extend('organizer_base'); ?>

<?= $this->section('pageCss'); ?>
<style>
.checkout-wrap {
    display: flex; justify-content: center; margin-top: 10px;
}
.checkout-card {
    background: #fff; border: 1px solid #E5E7EB; border-radius: 14px;
    padding: 30px 28px; max-width: 440px; width: 100%;
}
.checkout-pkg-name { font-size: 17px; font-weight: 700; color: #111827; margin: 0 0 4px; }
.checkout-pkg-desc { font-size: 13px; color: #6B7280; margin: 0 0 22px; }
.checkout-rows { list-style: none; margin: 0 0 22px; padding: 0; }
.checkout-rows li {
    display: flex; justify-content: space-between; align-items: center;
    font-size: 13px; color: #374151; padding: 10px 0; border-bottom: 1px dashed #F3F4F6;
}
.checkout-rows li:last-child { border-bottom: none; }
.checkout-rows li.total { padding-top: 16px; }
.checkout-rows li.total span:first-child { font-size: 14px; font-weight: 700; color: #111827; }
.checkout-rows li.total span:last-child { font-size: 22px; font-weight: 800; color: #111827; }
.checkout-pay-btn {
    border: none; background: #2563EB; color: #fff; border-radius: 8px;
    padding: 12px 18px; font-size: 14px; font-weight: 600; cursor: pointer;
    text-align: center; width: 100%; display: block; transition: background 0.15s;
}
.checkout-pay-btn:hover { background: #1D4ED8; color: #fff; }
.checkout-pay-btn:disabled { background: #93B4F8; cursor: not-allowed; }
.checkout-cancel-link {
    display: block; text-align: center; margin-top: 14px;
    font-size: 13px; color: #6B7280; text-decoration: none;
}
.checkout-cancel-link:hover { color: #374151; text-decoration: underline; }
.checkout-secure-note {
    display: flex; align-items: center; justify-content: center; gap: 6px;
    font-size: 12px; color: #9CA3AF; margin-top: 18px;
}
.org-alert {
    border-radius: 8px; padding: 12px 16px; margin-bottom: 16px;
    font-size: 14px; display: flex; align-items: flex-start; gap: 10px;
}
.org-alert.danger  { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
</style>
<?= $this->endSection(); ?>

<?= $this->section('main_content'); ?>

<div class="checkout-wrap">
    <div class="checkout-card">

        <div id="payment-error" class="org-alert danger" style="display:none;"></div>

        <h6 class="checkout-pkg-name"><?= esc($package->package_name) ?></h6>
        <p class="checkout-pkg-desc"><?= esc($package->package_description ?? '') ?></p>

        <ul class="checkout-rows">
            <li>
                <span>Duration</span>
                <span>
                    <?= $package->package_duration_type === 'lifetime'
                        ? 'Lifetime'
                        : esc($package->package_duration_days) . ' days' ?>
                </span>
            </li>
            <li>
                <span>Plan type</span>
                <span style="text-transform:capitalize;"><?= esc($package->package_duration_type) ?></span>
            </li>
            <li class="total">
                <span>Amount Payable</span>
                <span>₹<?= number_format($package->package_price, 2) ?></span>
            </li>
        </ul>

        <button id="rzp-pay-btn" class="checkout-pay-btn">
            Pay ₹<?= number_format($package->package_price, 2) ?> Securely
        </button>

        <a href="<?= base_url('organizer/packages') ?>" class="checkout-cancel-link">
            Cancel and go back
        </a>

        <div class="checkout-secure-note">
            <i class="fa-solid fa-lock"></i> Payments are encrypted and processed securely by Razorpay
        </div>

    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

var payBtn = document.getElementById('rzp-pay-btn');
if (!payBtn) {
    console.error('rzp-pay-btn not found in DOM');
    return;
}

payBtn.addEventListener('click', function (e) {
    e.preventDefault();

    var btn = this;
    var originalLabel = btn.innerText;
    btn.disabled = true;
    btn.innerText = 'Processing...';

    var options = {
        key: "<?= esc($razorpay_key, 'js') ?>",
        amount: "<?= esc($amount_paise, 'js') ?>",
        currency: "INR",
        name: "Tournament Organizer",
        description: "<?= esc($package->package_name, 'js') ?> Package",
        order_id: "<?= esc($razorpay_order_id, 'js') ?>",
        prefill: {
            name: "<?= esc($user->user_name ?? '', 'js') ?>",
            email: "<?= esc($user->user_email ?? '', 'js') ?>",
            contact: "<?= esc($user->user_phone ?? '', 'js') ?>"
        },
        theme: {
            color: "#2563EB"
        },
        handler: function (response) {
            var formData = new FormData();
            formData.append('package_id', "<?= esc($package->package_id, 'js') ?>");
            formData.append('razorpay_order_id', response.razorpay_order_id);
            formData.append('razorpay_payment_id', response.razorpay_payment_id);
            formData.append('razorpay_signature', response.razorpay_signature);
            formData.append('<?= csrf_token() ?>', "<?= csrf_hash() ?>");

            // Verify server-side before redirecting — the handler firing
            // only means Razorpay accepted the payment, not that it's genuine.
            fetch("<?= base_url('organizer/razorpay/verify') ?>", {
                method: 'POST',
                body: formData
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.status) {
                    window.location.href = data.redirect;
                } else {
                    showError(data.message || 'Payment verification failed.');
                    resetBtn();
                }
            })
            .catch(function () {
                showError('Something went wrong while verifying payment.');
                resetBtn();
            });
        },
        modal: {
            ondismiss: function () {
                resetBtn();
            }
        }
    };

    var rzp = new Razorpay(options);

    rzp.on('payment.failed', function (response) {
        showError(response.error.description || 'Payment failed. Please try again.');
        resetBtn();
    });

    rzp.open();

    function resetBtn() {
        btn.disabled = false;
        btn.innerText = originalLabel;
    }
});

}); // end DOMContentLoaded

function showError(msg) {
    var el = document.getElementById('payment-error');
    el.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i><div>' + msg + '</div>';
    el.style.display = 'flex';
}
</script>

<?= $this->endSection(); ?>