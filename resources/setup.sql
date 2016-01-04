CREATE TABLE IF NOT EXISTS invoices (
  invoice_id INTEGER,
  price_in_usd DOUBLE,
  price_in_btc DOUBLE,
  product_url TEXT,
  address CHAR(36),
  PRIMARY KEY (invoice_id)
);

CREATE TABLE IF NOT EXISTS invoice_payments (
  transaction_hash CHAR(64),
  value DOUBLE,
  invoice_id INTEGER,
  PRIMARY KEY (transaction_hash)
);

CREATE TABLE IF NOT EXISTS pending_invoice_payments (
  transaction_hash CHAR(64),
  value DOUBLE,
  invoice_id INTEGER,
  PRIMARY KEY (transaction_hash)
);