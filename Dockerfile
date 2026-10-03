FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo_pgsql \
    && for module_file in /etc/apache2/mods-enabled/mpm_*.load; do \
        [ -e "${module_file}" ] || continue; \
        module="${module_file##*/}"; \
        module="${module%.load}"; \
        if [ "${module}" != "mpm_prefork" ]; then a2dismod "${module}"; fi; \
    done \
    && a2enmod mpm_prefork headers \
    && enabled_mpm_count="$(find /etc/apache2/mods-enabled -maxdepth 1 -type l -name 'mpm_*.load' | wc -l)" \
    && [ "${enabled_mpm_count}" -eq 1 ] \
    && rm -rf /var/lib/apt/lists/*

COPY docker/apache-security.conf /etc/apache2/conf-available/cgs-security.conf
RUN a2enconf cgs-security
COPY docker/start-apache.sh /usr/local/bin/start-apache
RUN chmod +x /usr/local/bin/start-apache

WORKDIR /var/www/html
COPY . /var/www/html/
COPY users/userimages/noimage.png /var/www/html/users/noimage.png

RUN rm -rf /var/www/html/docker \
    && cp admin/include/config.example.php admin/include/config.php \
    && cp users/includes/config.example.php users/includes/config.php \
    && chown -R www-data:www-data /var/www/html/users/complaintdocs /var/www/html/users/userimages

EXPOSE 8080

CMD ["start-apache"]