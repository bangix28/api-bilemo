FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
curl
RUN apt install gnupg -y
RUN apt-get install default-mysql-client -y

ADD . /app/myapp

EXPOSE 8000

WORKDIR /myapp
CMD symfony server:start



