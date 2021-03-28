FROM php:7.3-fpm

RUN rm -rf /var/lib/apt/lists/* && apt-get update && apt-get upgrade -y \
    && apt-get install -y \
    curl \
    wget \
    git \
    zip \
    unzip \
    libicu-dev \
    zlib1g-dev libzip-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_mysql mysqli zip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

RUN docker-php-ext-install gd

RUN echo "$(curl -sS https://composer.github.io/installer.sig) -" > composer-setup.php.sig \
        && curl -sS https://getcomposer.org/installer | tee composer-setup.php | sha384sum -c composer-setup.php.sig \
        && php composer-setup.php && rm composer-setup.php* \
        && chmod +x composer.phar && mv composer.phar /usr/bin/composer

COPY symfony.ini /etc/php7/conf.d/
COPY symfony.ini /etc/php7/cli/conf.d/

COPY symfony.pool.conf /etc/php7/php-fpm.d/

WORKDIR /var/www/symfony
