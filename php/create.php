<?php

//Proxy to the api/receive method in order to not reveal the callback URL

include 'include.php';

$invoice_id = $_GET['invoice_id'];
$callback_url = $mysite_root . "callback.php?invoice_id=" . $invoice_id . "&secret=" . $secret;

$resp = file_get_contents($blockchain_receive_root . "v2/receive?key=" . $my_api_key . "&callback=" . urlencode($callback_url) . "&xpub=" . $my_xpub);
$response = json_decode($resp);

print json_encode(array('input_address' => $response->address ));


?>