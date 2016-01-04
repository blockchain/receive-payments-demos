from flask import Flask

app = Flask(__name__, static_folder='../../resources/static')
app.config.from_object('config')

import receive_demo.views
