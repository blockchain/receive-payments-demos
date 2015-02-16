For my Entrepreneurship in Engineering course, my team and I are building a wordpress plugin that will allow blog owners to charge small amounts of bitcoin for posts. My end goal for the semester is to be able to charge by the post, but afterwards I'd like to explore other business models, such as pay per minute. Here is the progression of MVPs I am hoping to acheive:

Create a paywall for entire page. This should be easier than a paywall by post. Currently in development. I will start with just a redirect page with a button that will take you to the premium page. Then from there I will integrate the bitcoin software, and you will be redirected once the account's master wallet receives payment. The user will then have unlimited access to all premium content. I'm not sure how long access will be valid though. Ideally it will be saved in a session variable and be valid for 24 hours.

Create paywall by post. This is the fine-grained transaction process I am hoping for. Instead of paying to access the page, you pay to get access to each post. Hopefully this will be a lot of the same code from iteration 2.
Security/ User Experience Enhancements.  Once step 3 is working, I will go back and ensure everything is secure enough for major financial transactions. I would also like to allow users to sign up and save their wallet information, to allow one click transactions. This would definitely entice users to purchase multiple articles in one sitting.

If anyone out there would like to contribute, I am new to Wordpress development, so I am always open to help, suggestions, or just a conversation about the project. 

These are notes from the original fork. 
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
	* http://loclahost/receive_payment_php_demo/setup.php
	* Now the database is intilizaized open the demo
	* http://localhost/receive_payment_php_demo/index.php
