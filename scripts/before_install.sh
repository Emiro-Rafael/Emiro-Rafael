#!/bin/bash
if grep -qs '/var/www/html/wp-content/uploads ' /proc/mounts; then
    sudo umount /var/www/html/wp-content/uploads    
else
    if test -d /var/www/html; then
        sudo rm -R '/var/www/html'
    fi   
fi
if grep -qs '/var/www/html/wp-content/uploads ' /proc/mounts; then
    sudo umount /var/www/html/wp-content/uploads    
else
    if test -d /var/www/html; then
        sudo rm -R '/var/www/html'
    fi   
fi