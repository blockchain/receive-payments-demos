import json

from blockchain.v2.receive import receive
from blockchain.exchangerates import to_btc
from flask import Flask, request, url_for, render_template

from receive_demo import app
from receive_demo.db import query_db, run_db

INVOICE_ID = 9001
PRICE_IN_USD = 0.15
PRODUCT_URL = 'nutbolt.jpg'

@app.route("/")
def root():
    price_in_btc = to_btc('USD', PRICE_IN_USD)
    run_db("""INSERT OR IGNORE INTO invoices
              (invoice_id, price_in_usd, price_in_btc, product_url)
              VALUES(?,?,?,?)""",
           [INVOICE_ID, PRICE_IN_USD, price_in_btc, PRODUCT_URL])
    return render_template('invoice.html',
                           blockchain_root=app.config['BLOCKCHAIN_ROOT'],
                           invoice_id=INVOICE_ID,
                           price_in_btc=price_in_btc)

@app.route("/create/<int:invoice_id>")
def create_handler(invoice_id):
    callback_url = url_for('payment_handler',
                           invoice_id=invoice_id,
                           secret=app.config['SECRET_KEY'],
                           _external=True)
    recv = receive(app.config['XPUB'], callback_url, app.config['API_KEY'])
    run_db("""UPDATE invoices
              SET address = ?
              WHERE invoice_id = ?""",
           [recv.address, invoice_id])
    return json.dumps({'input_address': recv.address})

@app.route("/payment/<int:invoice_id>")
def payment_handler(invoice_id):
    address = request.args.get('address')
    secret = request.args.get('secret')
    confirmations = request.args.get('confirmations')
    tx_hash = request.args.get('transaction_hash')
    value = float(request.args.get('value')) / 100000000
    order = query_db("""SELECT address
                        FROM invoices
                        WHERE invoice_id = ?""",
                     [invoice_id],
                     one=True)
    if address != order['address']:
        return 'Incorrect Receiving Address', 400
    if secret != app.config['SECRET_KEY']:
        return 'invalid secret', 400
    if confirmations >= 4:
        run_db("""INSERT INTO invoice_payments
                  (invoice_id, transaction_hash, value)
                  VALUES (?, ?, ?)""",
               [invoice_id, tx_hash, value])
        run_db("""DELETE FROM pending_invoice_payments
                  WHERE invoice_id = ?""",
               [invoice_id])
        return '*ok*'
    else:
        run_db("""INSERT INTO pending_invoice_payments
                  (invoice_id, transaction_hash, value)
                VALUES (?, ?, ?)""",
               [invoice_id, tx_hash, value])
        return 'Waiting for confirmations'
    # should never reach here!
    return 'something went wrong', 500

@app.route("/order_status/<int:invoice_id>")
def order_status(invoice_id):
    order = query_db("""SELECT price_in_usd, product_url, price_in_btc
                        FROM invoices
                        WHERE invoice_id = ?""",
                     [invoice_id],
                     one=True)
    pending_txs = query_db("""SELECT value
                              FROM pending_invoice_payments
                              WHERE invoice_id = ?""",
                           [invoice_id]) or [{'value': 0}]
    confirmed_txs = query_db("""SELECT value
                                FROM invoice_payments
                                WHERE invoice_id = ?""",
                             [invoice_id]) or [{'value': 0}]

    confirmed = sum(t['value'] for t in confirmed_txs)
    pending = sum(t['value'] for t in pending_txs)
    return render_template('order_status.html',
                           invoice_id=invoice_id,
                           order=order,
                           pending=pending,
                           confirmed=confirmed)
