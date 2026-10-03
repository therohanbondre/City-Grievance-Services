#!/bin/sh
set -eu

cd /var/www/html
mkdir -p users/complaintdocs users/userimages

if [ ! -f users/userimages/noimage.png ]; then
    cp users/noimage.png users/userimages/noimage.png
fi

chown -R www-data:www-data users/complaintdocs users/userimages

port="${PORT:-8080}"
sed -i "s/Listen 80/Listen ${port}/" /etc/apache2/ports.conf
sed -i "s/:80/:${port}/g" /etc/apache2/sites-available/000-default.conf

exec apache2-foreground