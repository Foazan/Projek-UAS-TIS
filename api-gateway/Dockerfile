FROM php:8.3-apache

# Install ekstensi dan dependencies
RUN docker-php-ext-install pdo pdo_mysql

# Copy project ke dalam container
COPY . /var/www/html

# Set permission
RUN chown -R www-data:www-data /var/www/html

# Expose port (optional tergantung kebutuhan)
EXPOSE 8000

# Jalankan server Lumen (jika pakai built-in server, misalnya)
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]