<?

$invoice_id = $_GET['invoice_id'];
$transaction_hash = $_GET['transaction_hash'];
$value_in_btc = $_GET['value'] / 100000000;
$secret = "CHANGE_TO_RANDOM_SECRET";
$my_bitcoin_address = "138YfXXXqizQHqyrUHZs4KAC6VuaBwSjgv";

//Commented out to test, uncomment when live
if ($_GET['test'] == true) {
    return;
}

if ($_GET['address'] != $my_bitcoin_address)
  return;

if ($_GET['secret'] != $secret)
  return;

try {
  //create or open the database
  $database = new SQLiteDatabase('db.sqlite', 0666, $error);
} catch(Exception $e) {
  die($error);
}

if ($_GET['confirmations'] >= 6 || $_GET['anonymous'] == true) {
  //Add the invoice to the database
  $query = "replace INTO invoice_payments (invoice_id, transaction_hash, value) values($invoice_id, '$transaction_hash', $value_in_btc)";

  if($database->queryExec($query, $error)) {
	 echo "*ok*";
  }
} else {
	
  $query = "replace INTO pending_invoice_payments (invoice_id, transaction_hash, value) values($invoice_id, '$transaction_hash', $value_in_btc)";

  if($database->queryExec($query, $error)) {
	 //Don't acknowledge the callback yet
  }
}

?>