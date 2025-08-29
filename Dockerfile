FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www/html
RUN echo "deb http://deb.debian.org/debian/ bullseye main" > /etc/apt/sources.list
# Install dependencies
RUN apt-get update --fix-missing && apt-get install -y \
    libaio1\
    wget \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    libxml2-dev \
    libxslt1-dev \
    libfontconfig1-dev \
    supervisor

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install zip \
    && docker-php-ext-install exif \
    && docker-php-ext-install pcntl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install xml \
    && docker-php-ext-install simplexml \
    && docker-php-ext-install fileinfo \
    && pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

# Install Oracle Instantclient
RUN mkdir /opt/oracle \
    && wget https://download.oracle.com/otn_software/linux/instantclient/216000/instantclient-basic-linux.x64-21.6.0.0.0dbru.zip \
    && wget https://download.oracle.com/otn_software/linux/instantclient/216000/instantclient-sdk-linux.x64-21.6.0.0.0dbru.zip \
    && wget https://download.oracle.com/otn_software/linux/instantclient/216000/instantclient-sqlplus-linux.x64-21.6.0.0.0dbru.zip \
    && unzip instantclient-basic-linux.x64-21.6.0.0.0dbru.zip -d /opt/oracle \
    && unzip instantclient-sdk-linux.x64-21.6.0.0.0dbru.zip -d /opt/oracle \
    && unzip instantclient-sqlplus-linux.x64-21.6.0.0.0dbru.zip -d /opt/oracle \
    && rm -rf *.zip \
    && mv /opt/oracle/instantclient_21_6 /opt/oracle/instantclient

# Add Oracle Instantclient path to environment
ENV LD_LIBRARY_PATH /opt/oracle/instantclient/
RUN ldconfig

# Install Oracle extensions
RUN docker-php-ext-configure pdo_oci --with-pdo-oci=instantclient,/opt/oracle/instantclient,21.1 \
    && echo 'instantclient,/opt/oracle/instantclient/' | pecl install oci8-3.2.1 \
    && docker-php-ext-install pdo_oci \
    && docker-php-ext-enable oci8
# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar backend paes
COPY ./ /var/www/html/

# Copy composer.lock and composer.json
COPY ./composer.json /var/www/html/
COPY ./composer.lock /var/www/html/

# Run composer install
RUN composer install --no-dev --optimize-autoloader

# Add user for Laravel application
RUN groupadd -g 1000 www \
    && useradd -u 1000 -ms /bin/bash -g www www


# Ajustar permisos de las carpetas storage y bootstrap/cache
RUN chown -R www:www /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Crear carpeta de logs y asignar permisos
RUN mkdir -p /var/log/supervisor \
    && chown -R www:www /var/log/supervisor \
    && chmod -R 775 /var/log/supervisor

# Crear carpeta de PID para Supervisor
RUN mkdir -p /var/run \
    && chown -R www:www /etc/supervisor \
    && chmod -R 775 /etc/supervisor

# Remove existing storage link
RUN rm -rf /var/www/html/storage/public

RUN php artisan optimize:clear

RUN php artisan route:cache

RUN php artisan config:cache
# Execute storage link
RUN php artisan storage:link
# Copiar configuración de Supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
# Change current user to www
USER www

# Expose port 80 and start php-fpm server
EXPOSE 9000
#CMD ["php-fpm"]
CMD ["supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
