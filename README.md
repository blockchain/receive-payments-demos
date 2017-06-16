# Notes
This repository contains simple examples of how to use the blockchain.info receive payments API V2 to process bitcoin payments.

To use Receive Payments v2 you'll need an xPub address corresponding to an HD wallet. If you don't already have this, you can obtain one by [creating a new wallet](https://blockchain.info/wallet/#/signup). You'll also need a [V2 API Key](https://api.blockchain.info/v2/apikey/request/) — please note that this new API key is mandatory for Receive API v2, and is distinct from the (optional) API key you might already use elsewhere in the Blockchain API.

Further documentation & explanation can be found at: https://blockchain.info/api/api_receive

You'll find samples written in several languages in this repository. The workflow implemented in all these samples is:

 - Show the user an invoice  with a javascript payment button.
 - On receiving payment, redirect to a status page
 - When the payment is fully confirmed, give the user the product (in this case, show the user nutsandbolts.jpg)

This code is intended as educational reference material and is not written for production use.
