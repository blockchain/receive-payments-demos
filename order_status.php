<?php

$invoice_id = intval($_GET['invoice_id']);
$product_url = '';
$price_in_usd = 0;
$price_in_btc = 0;
$amount_paid_btc = 0;
$amount_pending_btc = 0;

try {
  //create or open the database
  $database = new SQLiteDatabase('db.sqlite', 0666, $error);
} catch(Exception $e) {
  die($error);
}


//find the invoice form the database
$query = "select price_in_usd, product_url, price_in_btc from Invoices where invoice_id = $invoice_id";
         
if($result = $database->query($query, SQLITE_BOTH, $error))
{
  if($row = $result->fetch())
  {
	$product_url = $row['product_url'];  
	$price_in_usd = $row['price_in_usd'];
	$price_in_btc = $row['price_in_btc'];  
  } else {
	  die('Invoice not found');
  }
}
else
{
  die($error);
}

//find the pending amount paid
$query = "select SUM(value) as value from pending_invoice_payments where invoice_id = $invoice_id";
         
if($result = $database->query($query, SQLITE_BOTH, $error))
{
  if($row = $result->fetch())
  {
	$amount_pending_btc = $row['value'];  
  } 
}
else
{
  die($error);
}

//find the confirmed amount paid
$query = "select SUM(value) as value from invoice_payments where invoice_id = $invoice_id";
         
if($result = $database->query($query, SQLITE_BOTH, $error))
{
  if($row = $result->fetch())
  {
	$amount_paid_btc = $row['value'];  
  } 
}
else
{
  die($error);
}


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

<?php if ($amount_paid_btc < $price_in_btc) { ?> 
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