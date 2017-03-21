FROM php:7.0-alpine

MAINTAINER Marco Araujo <contact@marcojunior.com>

RUN apk update && apk upgrade && \
    apk add --no-cache bash git openssh

ENV PATH $PATH:/root/.composer/vendor/bin

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer
