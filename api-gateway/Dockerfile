FROM php:8.3-apache

RUN docker-php-ext-install pdo pdo_mysql

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]