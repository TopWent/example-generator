FROM php:7.3-fpm

RUN rm -rf /var/lib/apt/lists/* && apt-get update && apt-get upgrade -y \
    && apt-get install -y \
    libicu-dev \
    curl \
    wget \
    git \
    zip \
    unzip \
    zlib1g-dev libzip-dev libyaml-dev libpq-dev libssh2-1 libssh2-1-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_mysql mysqli zip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

RUN docker-php-ext-install gd pgsql

RUN if ! pecl list | grep yaml; \
           then \
               pecl install yaml \
               && echo "extension=yaml.so" > /usr/local/etc/php/conf.d/ext-yaml.ini \
               && docker-php-ext-enable yaml; \
           fi

RUN if ! pecl list | grep ssh2; \
           then \
               pecl install ssh2-1.2 \
               && docker-php-ext-enable ssh2; \
           fi

RUN if ! pecl list | grep xdebug; \
           then \
               pecl install xdebug \
               && docker-php-ext-enable xdebug; \
           fi

RUN echo "$(curl -sS https://composer.github.io/installer.sig) -" > composer-setup.php.sig \
        && curl -sS https://getcomposer.org/installer | tee composer-setup.php | sha384sum -c composer-setup.php.sig \
        && php composer-setup.php --version=1.6.3 && rm composer-setup.php* \
        && chmod +x composer.phar && mv composer.phar /usr/bin/composer

COPY symfony.ini /usr/local/etc/php/php.ini
COPY symfony.ini /etc/php7/cli/conf.d/
COPY xdebug.ini  /etc/php7/conf.d/

COPY symfony.pool.conf /etc/php7/php-fpm.d/

WORKDIR /var/www/symfony
