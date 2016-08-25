# About

This sample is based on [flask][flask] and uses the [blockchain package][bc.py]
to access the blockchain API.

## Setup

You can install all dependencies with `pip install -r requirements.txt`.
It's recommended that you do this from a [virtual environment][venv].

Flask will fail to respond to requests if the server name setting doesn't match
the server name in the URL you use to access the sample. You can set
`SERVER_NAME` in `config.py`.

By default the sample binds to the loopback interface only, on port 5000,
but you can customise both port and interface bindings with the `BIND` and
`PORT` options in `config.py`.

## Usage

Once the dependencies are installed, you can run with `python app.py`.

[flask]: http://flask.pocoo.org/
[venv]: http://docs.python-guide.org/en/latest/dev/virtualenvs/
[bc.py]: https://pypi.python.org/pypi/blockchain