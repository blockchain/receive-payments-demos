<?php

//Run this file one to init the database
//Needs write permissions in current directory to create the database file

include 'include.php';

$result = mysql_connect($mysql_host, $mysql_username, $mysql_password);
if (!$result) {
    die(__LINE__ . ' Invalid connect: ' . mysql_error());
}

$result = mysql_query('CREATE DATABASE IF NOT EXISTS ' . $mysql_database);

if (!$result) {
    die(__LINE__ . ' Invalid query: ' . mysql_error());
}

mysql_select_db($mysql_database) or die( "Unable to select database. Run setup first.");

$result = mysql_query('CREATE TABLE IF NOT EXISTS invoices (invoice_id INTEGER, price_in_usd DOUBLE, price_in_btc DOUBLE, product_url TEXT, PRIMARY KEY (invoice_id))');

if (!$result) {
    die(__LINE__ . ' Invalid query: ' . mysql_error());
}

$result = mysql_query('CREATE TABLE IF NOT EXISTS invoice_payments (transaction_hash CHAR(64), value DOUBLE, invoice_id INTEGER, PRIMARY KEY (transaction_hash))');
     
if (!$result) {
    die(__LINE__ . ' Invalid query: ' . mysql_error());
}

$result = mysql_query('CREATE TABLE IF NOT EXISTS pending_invoice_payments (transaction_hash CHAR(64), value DOUBLE, invoice_id INTEGER, PRIMARY KEY (transaction_hash))');
  
if (!$result) {
    die(__LINE__ . ' Invalid query: ' . mysql_error());
}

?>