FROM php:8.1.2-fpm
RUN export OPENSSL_CONF=/dev/null
ENV OPENSSL_CONF=/dev/null
RUN rm -rf /etc/localtime
RUN ln -s /usr/share/zoneinfo/Europe/Warsaw /etc/localtime
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    git \
    zip \
    unzip \
    wget \
    apt-file \
#    libssl1.0.0 \
    libssl-dev \
    libzip-dev \
    librabbitmq-dev \
    libpq-dev \
    libbz2-dev \
    libfontconfig1 \
    fontconfig \
    libfontconfig1-dev \
    && docker-php-ext-install bz2


ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN curl -sS https://getcomposer.org/installer | php \
    -- --install-dir=${COMPOSER_HOME:-/usr/local/bin} \
    --filename=composer
ARG USER_UID=1000
ARG USER_GID=1000
ARG USER_NAME=www-data
ARG USER_GROUP=www-data
RUN groupadd ${USER_GROUP} --gid ${USER_GID}
RUN useradd -g ${USER_GROUP} -u ${USER_UID} ${USER_NAME} -m

RUN mkdir -p /home/${USER_NAME}
WORKDIR /home/${USER_NAME}

COPY . .
COPY docker/php $PHP_INI_DIR/conf.d/
RUN composer install --optimize-autoloader