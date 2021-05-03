FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
curl \
wget
RUN apt install gnupg -y
RUN apt-get install default-mysql-client -y
RUN wget https://get.symfony.com/cli/installer -O - | bash && \
  mv /root/.symfony/bin/symfony /usr/local/bin/symfony
RUN docker-php-ext-install pdo pdo_mysql

ADD . /usr/src/myapp

EXPOSE 9000

WORKDIR /usr/src/myapp
