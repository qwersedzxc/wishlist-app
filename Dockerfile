
FROM php:8.2-apache
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && docker-php-ext-enable pdo_pgsql \
    && a2enmod rewrite
WORKDIR /var/www/html
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && mkdir -p /var/www/html/public/uploads \
    && chown -R www-data:www-data /var/www/html/public/uploads \
    && chmod -R 777 /var/www/html/public/uploads


RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf


COPY docker/apache-config.conf /etc/apache2/sites-available/000-default.conf
EXPOSE 80


CMD ["apache2-foreground"]
