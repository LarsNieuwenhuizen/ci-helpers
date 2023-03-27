FROM larsnieuwenhuizen/php-fpm:8.1-dev as install

COPY . /app

WORKDIR /app

RUN composer install --no-dev --no-interaction -oq; \
    mkdir -p /app/code

USER docker

CMD bin/console
