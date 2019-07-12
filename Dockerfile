# use php 7.2 with mod_php and mpm_prefork
FROM php:7.2-apache

# install dependencies for composer
RUN apt-get update && \
    apt-get install -y --no-install-recommends git zip unzip zlib1g-dev

# install composer and composer packages
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer
COPY composer.json ./
RUN composer install --no-scripts --no-autoloader
RUN composer dump-autoload --optimize

# install grpc extension
RUN pecl install grpc-1.21.3 \
    && docker-php-ext-enable grpc

# install protobuf extension
RUN pecl install protobuf-3.8.0 \
    && docker-php-ext-enable protobuf

# install opcache extension
RUN docker-php-ext-install opcache

# install yac extension for caching bigtable auth tokens
RUN pecl install yac-2.0.2 \
    && docker-php-ext-enable yac

# use production php.ini included with container
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY config/opcache.ini $PHP_INI_DIR/conf.d/

# move apache mpm_preform worker config into place
# note: this can be used to control the worker pool size
COPY config/mpm_prefork.conf /etc/apache2/mods-available/mpm_prefork.conf


# make grpc_php_plugin and protoc available for compiling helloworld.proto
RUN apt-get install -y automake libtool
RUN cd / && git clone -b v1.21.3 https://github.com/grpc/grpc
RUN cd /grpc && git submodule update --init && make grpc_php_plugin
COPY proto/ /proto 
RUN /grpc/bins/opt/protobuf/protoc                          \
    --proto_path=/proto                                     \
    --php_out=/var/www/html                                 \
    --grpc_out=./                                           \
    --plugin=protoc-gen-grpc=/grpc/bins/opt/grpc_php_plugin \
    /proto/helloworld.proto

# override parent container ENTRYPOINT start
ENTRYPOINT []
CMD ["apachectl", "-DFOREGROUND"]

# enable grpc tracing
# ENV GRPC_VERBOSITY "DEBUG"
# ENV GRPC_TRACE "api"
