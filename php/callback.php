<?php

include 'include.php';

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

$invoice_id = $_GET['invoice_id'];
$transaction_hash = $_GET['transaction_hash'];
$value_in_btc = $_GET['value'] / 100000000;

//Commented out to test, uncomment when live
if (array_key_exists('test', $_GET) and $_GET['test'] == true) {
  echo 'Ignoring Test Callback';
  return;
}

if ($_GET['address'] != $my_bitcoin_address) {
    echo 'Incorrect Receiving Address';
  return;
}

if ($_GET['secret'] != $secret) {
  echo 'Invalid Secret';
  return;
}

if ($_GET['confirmations'] >= 4) {
  //Add the invoice to the database
  $stmt = $db->prepare("replace INTO invoice_payments (invoice_id, transaction_hash, value) values(?, ?, ?)");
  $stmt->bind_param("isd",$invoice_id, $transaction_hash, $value_in_btc);
  $stmt->execute();

  //Delete from pending
  $stmt = $db->prepare(" delete from pending_invoice_payments where invoice_id = ? limit 1");
  $stmt->bind_param("i",$invoice_id);
  $result = $stmt->execute();

  if($result) {
	   echo "*ok*";
  }
} else {
  //Waiting for confirmations
  //create a pending payment entry
  $stmt = $db->prepare("replace INTO pending_invoice_payments (invoice_id, transaction_hash, value) values(?, ?, ?)");
  $stmt->bind_param("isd",$invoice_id, $transaction_hash, $value_in_btc);
  $stmt->execute();

   echo "Waiting for confirmations";
}

?>