#!/bin/bash
sudo rm -R /var/www/html
sudo mkdir /var/www/html
sudo chown -R ubuntu:www-data /var/www/html
cd /var/www/html
git clone git@github.com:SnackCrate/snackbar.git .
sudo mkdir /var/www/html/wp-content/uploads
sudo mount -t efs fs-027d02dd8e2494042 /var/www/html/wp-content/uploads/
sudo chown -R ubuntu:www-data /var/www/html