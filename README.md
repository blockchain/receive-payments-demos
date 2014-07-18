# Notes
Simple PHP example of using the blockchain.info receive payments API to process payments in PHP.

https://blockchain.info/api/api_receive

Show an invoice the the User with a javascript payment button, on payment received redirects to a status page. When the payment is fully confirmed shows the user nutsandbolts.jpg i.e. the product.

Do not use in production as is.

# Instructions
	* Clone the git repository into the ROOT of your web server.
	* cd /www/receive_payment_php_demo
	* chmod 755 ./
	* Navigate to setup.php in your browser
	* http://localhost/receive_payment_php_demo/setup.php
	* Now the database is intilizaized open the demo
	* http://localhost/receive_payment_php_demo/index.php
