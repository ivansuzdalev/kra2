
import string
from xml.dom.minidom import TypeInfo
import requests
import sqlite3
import json
import base64
from collections.abc import Iterable
import mysql.connector
import collections.abc

conn_cities = sqlite3.connect('cities.db')
cur_cities = conn_cities.cursor()
cur_cities.execute('SELECT * FROM cities')
cnx = mysql.connector.connect(user='root', password='Admin1973++',
                              host='127.0.0.1',
                              database='app')
cursor = cnx.cursor()
db_dict = {}
db_dict['city_id']= ''
db_dict['title']= ''
db_dict['area']= ''
db_dict['region']= ''

for row in cur_cities:

    db_dict['city_id']= row[1]
    db_dict['title']= row[2]
    db_dict['area']= row[3]
    db_dict['region']= row[4]

    db_list = list(db_dict.values()) 

    table = "cities"
    
    placeholders = ', '.join(['%s'] * len(db_dict))
    columns = ', '.join(db_dict.keys())
    
    sql = "INSERT INTO %s ( %s ) VALUES ( %s )" % (table, columns, placeholders)
    #print(db_dict)
    # valid in Python 3
    cursor.execute(sql, db_list)
    cnx.commit()
        # Insert new employee
cur_users.close()
cnx.close()

