#!/bin/bash

echo "Install composer libs - START"

cd /var/www

composer install

echo "Install composer libs - FINISH"

apache2-foreground
