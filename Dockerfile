FROM php:8.2-apache

RUN apt update
RUN apt install zip unzip libpq-dev -y

RUN docker-php-ext-install pdo pdo_pgsql
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite && /etc/init.d/apache2 restart

COPY . .

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN composer install
RUN chown -R www-data:www-data /var/www/html/
RUN chown -R www-data:www-data /tmp/