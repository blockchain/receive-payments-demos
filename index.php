<?php

include 'include.php';

$invoice_id = 9001;
$price_in_usd = 0.15;
$product_url = 'nutbolt.jpg';
$price_in_btc = file_get_contents($blockchain_root . "tobtc?currency=USD&value=" . $price_in_usd);

mysql_connect($mysql_host, $mysql_username, $mysql_password) or die(__LINE__ . ' Invalid connect: ' . mysql_error());

mysql_select_db($mysql_database) or die( "Unable to select database. Run setup first.");

//Add the invoice to the database
$result = mysql_query("replace INTO invoices (invoice_id, price_in_usd, price_in_btc, product_url) values($invoice_id,'$price_in_usd','$price_in_btc','$product_url')");
    
if (!$result) {
    die(__LINE__ . ' Invalid query: ' . mysql_error());
}

?>

<html>
<head>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
    <script type="text/javascript" src="<? echo $blockchain_root ?>Resources/wallet/pay-now-button-v2.js"></script>
    
    <script type="text/javascript">
	$(document).ready(function() {
		$('.stage-paid').on('show', function() {
			window.location.href = './order_status.php?invoice_id=<?php echo $invoice_id; ?>';
		});
	});
	</script>
</head>
    <body>
       <h1>Buy Nuts and Bolts</h1>

        <div class="blockchain-btn" style="width:auto" data-create-url="create.php"> 
            <div class="blockchain stage-begin">
                <img src="<?php echo $blockchain_root ?>Resources/buttons/pay_now_64.png">
            </div>
            <div class="blockchain stage-loading" style="text-align:center">
                <img src="<?php echo $blockchain_root ?>Resources/loading-large.gif">
            </div>
            <div class="blockchain stage-ready" style="text-align:center">
                Please send <?php echo $price_in_btc ?> BTC to <br /> <b>[[address]]</b> <br /> 
                <img style="margin:5px" id="qrsend" src="<?=$blockchain_root?>qr?data=bitcoin:<?=$my_bitcoin_address?>%3Famount=<?=$price_in_btc?>%26label=Pay-Demo&size=125" alt=""/>
            </div>
            <div class="blockchain stage-paid">
                Payment Received <b>[[value]] BTC</b>. Thank You.
            </div>
            <div class="blockchain stage-error">
                <font color="red">[[error]]</font>
            </div>
        </div>
    </body>
</html>