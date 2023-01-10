ARG PHP_VERSION
FROM php:${PHP_VERSION}-fpm-alpine
# 注意！FROM之前声明的ARG，是在构建阶段之外的，所以不能在FROM之后的任何指令中使用它。要使用在第一个FROM之前声明的ARG的默认值，需要在构建阶段内使用一个没有值的ARG指令

ARG CONTAINER_PACKAGE_URL

COPY php.ini /usr/local/etc/php

# 要安装的预先下载的扩展包
ARG SWOOLE=swoole-4.8.12.tgz
ARG REDIS=redis-5.3.7.tgz
ARG AMQP=amqp-1.11.0.tgz
ARG MCRYPT=mcrypt-1.0.5.tgz
ARG IMAGICK=imagick-3.7.0.tgz

# 下载方式安装yasd调试扩展，需要同时安装boost boost-dev
ARG YASD_VERSION=0.3.9

RUN sed -i "s/dl-cdn.alpinelinux.org/${CONTAINER_PACKAGE_URL}/g" /etc/apk/repositories

RUN cd ~ \
    && apk add wget \
    curl \
    --no-cache --virtual .build-deps autoconf g++ gcc libtool make linux-headers \
    bzip2-dev \
    gettext-dev \
    libzip-dev \
    imagemagick-dev \
    libmcrypt-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    file-dev \
    libxml2-dev \
    zlib-dev \
    libpq-dev \
    libmemcached-dev \
    rabbitmq-c-dev \
    && docker-php-ext-configure gd \
    --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli pdo_mysql pcntl sockets bz2 zip soap pgsql pdo_pgsql bcmath gettext sysvmsg sysvsem sysvshm xmlrpc opcache

COPY ./extensions /tmp/extensions
WORKDIR /tmp/extensions
# 用 docker-php-ext-install 安装扩展
# 可以先下载扩展包到extensions目录，然后用pecl安装，这样可以省去下载的时间
RUN cd ~ \
    && pecl install /tmp/extensions/${SWOOLE} \
    && echo "extension=swoole.so" > /usr/local/etc/php/conf.d/swoole.ini \
    && pecl install /tmp/extensions/${REDIS} \
    && echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini \
    && pecl install /tmp/extensions/${AMQP} \
    && echo "extension=amqp.so" > /usr/local/etc/php/conf.d/amqp.ini \
    && pecl install /tmp/extensions/${MCRYPT} \
    && echo "extension=mcrypt.so" > /usr/local/etc/php/conf.d/mcrypt.ini \
    && pecl install /tmp/extensions/${IMAGICK} \
    && echo "extension=imagick.so" > /usr/local/etc/php/conf.d/imagick.ini \
    && rm -rf /tmp/extensions \
    && apk add boost boost-dev \
    && wget https://github.com/swoole/yasd/archive/v${YASD_VERSION}.tar.gz -O yasd-${YASD_VERSION}.tar.gz \
    && tar -zxvf yasd-${YASD_VERSION}.tar.gz \
    && mkdir -p /usr/src/php/ext \
    && mv yasd-${YASD_VERSION} /usr/src/php/ext/yasd \
    && docker-php-ext-install yasd \
    && rm -f /usr/local/etc/php/conf.d/docker-php-ext-yasd.ini
# yasd的配置直接在php.ini配置

# 安装composer，并且换源
RUN curl -o /usr/bin/composer https://mirrors.aliyun.com/composer/composer.phar \
    && chmod +x /usr/bin/composer \
    && composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
ENV COMPOSER_HOME=/tmp/composer

WORKDIR /var/www

EXPOSE 9000
EXPOSE 9501

# 安装ssh，用于远程调试，不需要的可以注释掉
RUN cd ~ \
    && apk add --no-cache openssh-server openssh \
    && sed -i "s/#PermitRootLogin.*/PermitRootLogin yes/g" /etc/ssh/sshd_config \
    && sed -i "s/#Port.*/Port 22/g" /etc/ssh/sshd_config \
    && ssh-keygen -t dsa -P "" -f /etc/ssh/ssh_host_dsa_key \
    && ssh-keygen -t rsa -P "" -f /etc/ssh/ssh_host_rsa_key \
    && ssh-keygen -t ecdsa -P "" -f /etc/ssh/ssh_host_ecdsa_key \
    && ssh-keygen -t ed25519 -P "" -f /etc/ssh/ssh_host_ed25519_key \
    && echo "root:root" | chpasswd # 设置ssh账号:密码

# 开放22端口 用于ssh
EXPOSE 22

# 容器启动时执行ssh启动命令
CMD ["/usr/sbin/sshd", "-D"]