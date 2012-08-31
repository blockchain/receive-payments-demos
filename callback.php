<?

$invoice_id = $_GET['invoice_id'];
$transaction_hash = $_GET['transaction_hash'];
$value_in_btc = $_GET['value'] / 100000000;

//Commented out to test, uncomment when live
if ($_GET['test'] == true)
    return;

try {
  //create or open the database
  $database = new SQLiteDatabase('db.sqlite', 0666, $error);
} catch(Exception $e) {
  die($error);
}

$hosts = gethostbynamel('blockchain.info');
foreach ($hosts as $ip) {
    if ($_SERVER['REMOTE_ADDR'] == $ip) {

    	//Add the invoice to the database
		$query = "replace INTO invoice_payments (invoice_id, transaction_hash, value) values($invoice_id, '$transaction_hash', $value_in_btc)";


		if($database->queryExec($query, $error))
		{
		   echo "*ok*";
		}

      break;
    }
}


?>