<?php

include 'include.php';

$db = new mysqli($mysql_host, $mysql_username, $mysql_password) or die(__LINE__ . ' Invalid connect: ' . mysqli_error());
$db->select_db($mysql_database) or die( "Unable to select database. Run setup first.");

$invoice_id = $_GET['invoice_id'];
$transaction_hash = $_GET['transaction_hash'];
$value_in_btc = $_GET['value'] / 100000000;


$stmt = $db->prepare("select address from invoices where invoice_id = ?");
$stmt->bind_param("i",$invoice_id);
$success = $stmt->execute();

if (!$success) {
    die(__LINE__ . ' Invalid query: ' . mysql_error());
}

$result = $stmt->get_result();
while($row = $result->fetch_array()) {
  $my_address = $row['address'];
}

$result->close();
$stmt->close(); 

if ($_GET['address'] != $my_address) {
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