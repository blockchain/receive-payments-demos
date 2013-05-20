<?php

//Proxy to the api/receive method in order to not reveal the callback URL

include 'include.php';

$invoice_id = $_GET['invoice_id'];
$callback_url = $mysite_root . "callback.php?invoice_id=" . $invoice_id . "&secret=" . $secret;

echo file_get_contents($blockchain_root . "api/receive?method=create&callback=" . urlencode($callback_url) . "&address=" . $my_bitcoin_address);

?>