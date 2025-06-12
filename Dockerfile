FROM webdevops/php-nginx:8.1-alpine

# Copy the configuration files
COPY ./php.ini /opt/docker/etc/php/php.ini
COPY ./vhost.conf /opt/docker/etc/nginx/vhost.conf

# Set the working directory to /app
WORKDIR /app

# Copy the composer files (composer.json and composer.lock) to /app
COPY composer.json composer.lock /app/

# Install Composer dependencies
RUN composer install --no-interaction --no-scripts --no-suggest

# Copy the rest of the application files into /app
COPY . /app
