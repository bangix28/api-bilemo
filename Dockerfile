FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
curl
RUN apt install gnupg -y
RUN apt-get install default-mysql-client -y

ADD . /usr/src/myapp

EXPOSE 8000

WORKDIR /usr/src/myapp


