import sqlite3
from contextlib import closing
from flask import g
from receive_demo import app

def connect_db():
    return sqlite3.connect(app.config['DATABASE'])

def setup_db():
    with closing(connect_db()) as db:
        with app.open_resource('../../resources/setup.sql') as fd:
            db.cursor().executescript(fd.read())
        db.commit()

def get_db():
    db = getattr(g, '_database', None)
    if db is None:
        db = g._database = connect_db()
        db.row_factory = sqlite3.Row
    return db

def query_db(query, args=(), one=False):
    with closing(get_db().execute(query, args)) as cur:
        rv = cur.fetchall()
        return (rv[0] if rv else None) if one else rv

def run_db(query, args=()):
    db = get_db()
    db.execute(query, args)
    db.commit()

@app.teardown_appcontext
def close_connection(exception):
    db = getattr(g, '_database', None)
    if db is not None:
        db.close()
