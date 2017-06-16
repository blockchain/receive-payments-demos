require 'sinatra/base'
require 'config'
require 'blockchain'
require 'sqlite3'

def db
  SQLite3::Database.new Settings.db.path
end

class Demo < Sinatra::Base

  set :root, File.join(File.dirname(__FILE__), '..')
  register Config
  set :static, true
  set :public_folder, Proc.new { File.join(root, Settings.app.static_path) }

  get '/' do
    invoice_id = Settings.app.invoice_id
    price_in_usd = Settings.app.price_in_usd
    price_in_btc = Blockchain::ExchangeRateExplorer.new.to_btc('USD', Settings.app.price_in_usd)
    db.execute %{
      INSERT OR IGNORE INTO invoices
      (invoice_id, price_in_usd, price_in_btc, product_url)
      VALUES(?,?,?,?)
      }, invoice_id, price_in_usd, price_in_btc, Settings.app.product_url

    erb :invoice, locals: {
      blockchain_root: Settings.blockchain_root,
      invoice_id: Settings.app.invoice_id,
      price_in_btc: "%f" % Blockchain::ExchangeRateExplorer.new.to_btc('USD', Settings.app.price_in_usd)
    }
  end

  get '/create_handler/:invoice_id' do |invoice_id|
    callback_url = Settings.app.base_url
    puts callback_url
    resp = Blockchain::V2::Receive.new().receive(Settings.xpub, callback_url, Settings.api_key, 50)
    db.execute %{
      UPDATE invoices
      SET address = ?
      WHERE invoice_id = ?
    }, resp.address, invoice_id
    JSON.dump({ input_address: resp.address })
  end

  get '/order_status/:invoice_id' do |invoice_id|
    handle = db()
    order = handle.execute(%{
      SELECT price_in_btc, price_in_usd
      FROM invoices
      WHERE invoice_id = ?
    }, invoice_id)[0]
    confirmed = handle.execute(%{
      SELECT value
      FROM invoice_payments
      WHERE invoice_id = ?
    }, invoice_id)
      .inject(0) { |acc,x| acc + x[0] }
    pending = handle.execute(%{
      SELECT value
      FROM pending_invoice_payments
      WHERE invoice_id = ?
    }, invoice_id)
      .inject(0) { |acc,x| acc + x[0] }

    erb :order_status, locals: {
      invoice_id: invoice_id,
      price_in_btc: "%f" % order[0],
      price_in_usd: order[1],
      pending: (pending or 0),
      confirmed: (confirmed or 0)
    }
  end

  get '/payment/:invoice_id' do |invoice_id|
    address = params[:address]
    secret = params[:secret]
    confirmations = params[:confirmations].to_i
    tx_hash = params[:transaction_hash]
    value = params[:value].to_f / 100000000

    handle = db()
    my_address = handle.execute(%{
      SELECT address
      FROM invoices
      WHERE invoice_id = ?
    }, invoice_id)[0][0]

    return 400, 'Incorrect Receiving Address' unless my_address == address
    return 400, 'Invalid Secret' unless secret == Settings.secret
    if confirmations >= 4
      handle.execute %{
        INSERT INTO invoice_payments
        (invoice_id, transaction_hash, value)
        VALUES (?, ?, ?)
      }, invoice_id, tx_hash, value
      handle.execute %{
        DELETE FROM pending_invoice_payments WHERE invoice_id = ?
      }, invoice_id
      return 200, '*ok*'
    else
      handle.execute %{
        INSERT INTO pending_invoice_payments
        (invoice_id, transaction_hash, value)
        VALUES (?, ?, ?)
      }, invoice_id, tx_hash, value
      return 200, 'Waiting for confirmations'
    end
    # shouldn't ever reach this
    return 500, 'something went wrong'
  end

end

handle = db()
IO.read('../resources/setup.sql').split(';').each do |q|
  handle.execute q
end
