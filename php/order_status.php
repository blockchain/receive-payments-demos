<?php

include 'include.php';

$invoice_id = intval($_GET['invoice_id']);
$product_url = '';
$price_in_usd = 0;
$price_in_btc = 0;
$amount_paid_btc = 0;
$amount_pending_btc = 0;

$db = new mysqli($mysql_host, $mysql_username, $mysql_password) or die(__LINE__ . ' Invalid connect: ' . mysqli_error());
$db->select_db($mysql_database) or die( "Unable to select database. Run setup first.");

$stmt = $db->prepare("select price_in_usd, product_url, price_in_btc from invoices where invoice_id = ?");
$stmt->bind_param("i",$invoice_id);
$success = $stmt->execute();

if (!$success) {
    die(__LINE__ . ' Invalid query: ' . mysql_error());
}

$result = $stmt->get_result();
while($row = $result->fetch_array()) {
	$product_url = $row['product_url'];
	$price_in_usd = $row['price_in_usd'];
	$price_in_btc = $row['price_in_btc'];
}

$result->close();
$stmt->close(); 

$stmt = $db->prepare("select value from pending_invoice_payments where invoice_id = ?");
$stmt->bind_param("i",$invoice_id);
$success = $stmt->execute();

if (!$success) {
    die(__LINE__ . ' Invalid query: ' . mysql_error());
}
$result = $stmt->get_result();
while($row = $result->fetch_array()){
	 $amount_pending_btc += $row['value'];   
}

$result->close();
$stmt->close(); 

//find the confirmed amount paid
$stmt = $db->prepare("select value from invoice_payments where invoice_id = ?");
$stmt->bind_param("i",$invoice_id);
$success = $stmt->execute();

if (!$success) {
    die(__LINE__ . ' Invalid query: ' . mysql_error());
}
$result = $stmt->get_result();
         
while($row = $result->fetch_array()){
	$amount_paid_btc += $row['value']; 
}
$result->close();
$stmt->close(); 
?>

<html>
<head>
</head>
<body>
<img src="invoice.png">

<h2>Invoice <?php echo $invoice_id ?> </h2>
<p>
Amount Due : <?php echo $price_in_usd ?> USD (<?php echo $price_in_btc ?> BTC) 
</p>

<p>
Amount Pending : <?php echo $amount_pending_btc ?> BTC
</p>

<p>
Amount Confirmed : <?php echo $amount_paid_btc ?> BTC
</p>
<?php if ($amount_paid_btc  == 0 && $amount_pending_btc == 0) { ?> 
Payment not received.
<?php } else if ($amount_paid_btc < $price_in_btc) { ?> 
<p>
Waiting for Payment Confirmation: <a href="./order_status.php?invoice_id=<?php echo $invoice_id ?>">Refresh</a>
</p>
<?php } else { ?>
<p>
Thank You for your purchase
</p>
<img src="nutbolt.jpg">
<?php } ?>

</body>
</html>