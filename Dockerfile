FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
curl \
wget
RUN apt install gnupg -y
RUN apt-get install default-mysql-client -y
RUN wget https://get.symfony.com/cli/installer -O - | bash && \
  mv /root/.symfony/bin/symfony /usr/local/bin/symfony


ADD . /usr/src/myapp

EXPOSE 8000

WORKDIR /usr/src/myapp
CMD php bin/console cache:clear && symfony serve --allow-http --no-tls --port=8000


