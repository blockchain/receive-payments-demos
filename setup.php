<?php

//Run this file one to init the database
//Needs write permissions in current directory to create the database file

try 
{
  //create or open the database
  $database = new SQLiteDatabase('db.sqlite', 0666, $error);
}
catch(Exception $e) 
{
  die($error);
}

//add Movie table to database
$query = 'CREATE TABLE Invoices ' .
         '(invoice_id INTEGER, price_in_usd DOUBLE, price_in_btc DOUBLE, product_url TEXT, PRIMARY KEY (invoice_id))';
         
if(!$database->queryExec($query, $error))
{
  echo  $error;
}

//add Movie table to database
$query = 'CREATE TABLE invoice_payments ' .
         '(transaction_hash CHAR(64), value DOUBLE, invoice_id INTEGER, PRIMARY KEY (transaction_hash))';
         
if(!$database->queryExec($query, $error))
{
  echo  $error;
}

//add Movie table to database
$query = 'CREATE TABLE pending_invoice_payments ' .
         '(transaction_hash CHAR(64), value DOUBLE, invoice_id INTEGER, PRIMARY KEY (transaction_hash))';
         
if(!$database->queryExec($query, $error))
{
  echo  $error;
}

?>