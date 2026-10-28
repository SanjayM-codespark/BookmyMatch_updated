<!DOCTYPE html>
<html>
<head>
<title>Razorpay Payment Gateway</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<meta name="description" content="">
<meta name="author" content="">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
</head>
<body>
<?php
	$description        = "Pay for Book buy";
	$txnid              = uniqid();     
	$key_id             = "rzp_test_gFGEDl83OLF33h----";
	$currency_code      = $currency_code;            
	$total              = ($amount * 100); // 100 = 1 indian rupees
	$amount             = $amount;
	$merchant_order_id  = session()->get('payorderid');
	$card_holder_name   = session()->get('username');
	$email              = session()->get('email');
	$phone              = trim(session()->get('mobile'));
	$name               = session()->get('username');
?>
<div class="container">
	<div class="page-body">
		<form name="razorpay-form" id="razorpay-form" action="<?php echo $callback_url; ?>" method="POST">
			<input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id" />
			<input type="hidden" name="merchant_order_id" id="merchant_order_id" value="<?php echo $merchant_order_id; ?>"/>
			<!--<input type="hidden" name="merchant_trans_id" id="merchant_trans_id" value="<?php echo $txnid; ?>"/>-->
			<input type="hidden" name="merchant_product_info_id" id="merchant_product_info_id" value="<?php echo $description; ?>"/>
			<input type="hidden" name="merchant_surl_id" id="merchant_surl_id" value="<?php echo $surl; ?>"/>
			<input type="hidden" name="merchant_furl_id" id="merchant_furl_id" value="<?php echo $furl; ?>"/>
			<input type="hidden" name="card_holder_name_id" id="card_holder_name_id" value="<?php echo $card_holder_name; ?>"/>
			<input type="hidden" name="merchant_total" id="merchant_total" value="<?php echo $total; ?>"/>
			<input type="hidden" name="merchant_amount" id="merchant_amount" value="<?php echo $amount; ?>"/>
		</form>
		
	</div>
</div>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
var options = {
	key:            "<?php echo $key_id; ?>",
	amount:         "<?php echo $total; ?>",
	name:           "<?php echo $name; ?>",
	description:    "Order # <?php echo $merchant_order_id; ?>",
	netbanking:     true,
	currency:       "<?php echo $currency_code; ?>", // INR
	prefill: {
		name:       "<?php echo $card_holder_name; ?>",
		email:      "<?php echo $email; ?>",
		contact:    "<?php echo $phone; ?>"
	},
	notes: {
		soolegal_order_id: "<?php echo $merchant_order_id; ?>",
	},
	handler: function (transaction) {
		document.getElementById('razorpay_payment_id').value = transaction.razorpay_payment_id;
		document.getElementById('razorpay-form').submit();
	},
	"modal": {
		"ondismiss": function(){
			window.location.href="<?=base_url()?>";
		}
	}
};
var instance = new Razorpay(options);
instance.on('payment.failed', function (response){
        var code = response.error.code;
        var description = response.error.description;
        var source = response.error.source;
        var step = response.error.step;
        var reason = response.error.reason;
        var order_id = response.error.metadata.order_id;
        var payment_id = response.error.metadata.payment_id;
        var q = "?code="+code+"&description="+description+"&source="+source+"&step="+step+"&reason="+reason+"&order_id="+order_id+"&payment_id="+payment_id;
        window.location.href="<?=base_url('razorpay/failed')?>"+q;
});
    instance.open(); 
</script>
</body>
</html>