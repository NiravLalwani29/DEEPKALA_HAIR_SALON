#!/bin/bash
# Run once in a Codespace: bash codespace_setup.sh  (installs mysqli + MariaDB, imports DB, starts site on :8000)
set -e
sudo apt-get update -qq
sudo DEBIAN_FRONTEND=noninteractive apt-get install -y -qq php-mysql mariadb-server
sudo service mariadb start || sudo service mysql start
# config.php connects as root with empty password over TCP; MariaDB root defaults to socket-auth
sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED VIA mysql_native_password USING PASSWORD(''); FLUSH PRIVILEGES;"
mysql -uroot -h127.0.0.1 < deepkala_full.sql
php -S 0.0.0.0:8000
