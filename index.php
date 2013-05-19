<?php

$invoice_id = 9001;
$price_in_usd = 0.15;
$product_url = 'nutbolt.jpg';
$my_bitcoin_address = "1A8JiWcwvpY7tAopUkSnGuEYHmzGYfZPiq";
$secret = "CHANGE_TO_RANDOM_SECRET";
$callback_url = "http://mysite.com?invoice_id=" . $invoice_id . "&secret=" . $secret;
$root = "https://blockchain.info/"; 
$price_in_btc = file_get_contents($root . "tobtc?currency=USD&value=" . $price_in_usd);

try 
{
  //create or open the database
  $database = new SQLiteDatabase('db.sqlite', 0666, $error);
}
catch(Exception $e) 
{
  die($error);
}

//Add the invoice to the database
$query = "replace INTO Invoices (invoice_id, price_in_usd, price_in_btc, product_url) values($invoice_id,$price_in_usd,$price_in_btc,'$product_url')";
         
if(!$database->queryExec($query, $error))
{
  die($error);
}

?>

<html>
<head>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
    <script type="text/javascript" src="bitcoin.js"></script>
    
    <script type="text/javascript">
	$(document).ready(function() {
		$('.stage-paid').on('show', function() {
			window.location.href = './order_status.php?invoice_id=<?php echo $invoice_id; ?>';
		});
	});
	</script>
</head>
    <body>
       <img src="nutbolt.jpg">
        <div id="paydiv" style="font-size:16px;margin:10px;width:300px;cursor:pointer;margin-left:750px;margin-top:20px" class="blockchain-btn"
             data-address="<?php echo $my_bitcoin_address ?>"
             data-anonymous="false"
             data-callback="<?php echo $$callback_url; ?>"
			       data-btcprice="<?=$price_in_btc?>"> 
            <div class="blockchain stage-begin">
                <img src="<?php echo $root ?>Resources/buttons/pay_now_64.png">
            </div>
            <div class="blockchain stage-loading" style="text-align:center">
                <img src="<?php echo $root ?>Resources/loading-large.gif">
            </div>
            <div class="blockchain stage-ready">
                <p align="center">Please send <?php echo $price_in_btc ?> BTC to <br /> <b>[[address]]</b> <img style="margin:5px" id="qrsend" src="<?=$root?>qr?data=bitcoin:<?=$my_bitcoin_address?>%3Famount=<?=$price_in_btc?>%26label=Pay-Demo&size=125" alt=""/></p>
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