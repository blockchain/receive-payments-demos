<?php

//Run this file one to init the database
//Needs write permissions in current directory to create the database file

include 'include.php';

$mysqli = new mysqli($mysql_host, $mysql_username, $mysql_password);
if ($mysqli->connect_error) {
    die(__LINE__ . ' Invalid connect: ' . $mysqli->connect_errno);
}

$result = $mysqli->query('CREATE DATABASE IF NOT EXISTS `' . $mysql_database . '`');

if (!$result) {
    die(__LINE__ . ' Invalid query: ' . $mysqli->errno);
}

$mysqli->select_db($mysql_database);
if (!$result) {
    die(__LINE__ . ' Unable to select database. Run setup first: ' . $mysqli->errno);
}

$result = $mysqli->query('CREATE TABLE IF NOT EXISTS invoices (invoice_id INTEGER, price_in_usd DOUBLE, price_in_btc DOUBLE, product_url TEXT, PRIMARY KEY (invoice_id))');

if (!$result) {
    die(__LINE__ . ' Invalid query: ' . $mysqli->errno);
}

$result = $mysqli->query('CREATE TABLE IF NOT EXISTS invoice_payments (transaction_hash CHAR(64), value DOUBLE, invoice_id INTEGER, PRIMARY KEY (transaction_hash))');
     
if (!$result) {
    die(__LINE__ . ' Invalid query: ' . $mysqli->errno);
}

$result = $mysqli->query('CREATE TABLE IF NOT EXISTS pending_invoice_payments (transaction_hash CHAR(64), value DOUBLE, invoice_id INTEGER, PRIMARY KEY (transaction_hash))');
  
if (!$result) {
    die(__LINE__ . ' Invalid query: ' . $mysqli->errno);
}

