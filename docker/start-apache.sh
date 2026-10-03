#!/bin/sh
set -eu

cd /var/www/html

for module_file in /etc/apache2/mods-enabled/mpm_*.load; do
    [ -e "${module_file}" ] || continue
    module="${module_file##*/}"
    module="${module%.load}"
    if [ "${module}" != "mpm_prefork" ]; then
        a2dismod "${module}"
    fi
done
a2enmod mpm_prefork

enabled_mpm_count="$(find /etc/apache2/mods-enabled -maxdepth 1 -type l -name 'mpm_*.load' | wc -l)"
if [ "${enabled_mpm_count}" -ne 1 ]; then
    echo "Expected exactly one Apache MPM; found ${enabled_mpm_count}." >&2
    exit 1
fi

mkdir -p users/complaintdocs users/userimages

if [ ! -f users/userimages/noimage.png ]; then
    cp users/noimage.png users/userimages/noimage.png
fi

chown -R www-data:www-data users/complaintdocs users/userimages

port="${PORT:-8080}"
sed -i "s/Listen 80/Listen ${port}/" /etc/apache2/ports.conf
sed -i "s/:80/:${port}/g" /etc/apache2/sites-available/000-default.conf

apache2ctl -M 2>&1 | grep 'mpm_'

exec apache2-foreground