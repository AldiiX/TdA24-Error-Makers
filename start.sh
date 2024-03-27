#!/bin/sh

# Start MariaDB
service mariadb start

# Create the database
echo "CREATE DATABASE IF NOT EXISTS db;" | mysql
echo "USE db; CREATE USER 'admin'@'localhost' IDENTIFIED BY 'password';" | mysql
echo "USE db; GRANT CREATE, ALTER, DROP, INSERT, UPDATE, DELETE, SELECT, REFERENCES, RELOAD on *.* TO 'admin'@'localhost' WITH GRANT OPTION;" | mysql

# Import the database
mysql db < /app/database.sql

# Start Apache in the foreground
apache2ctl -D FOREGROUND