FROM larsnieuwenhuizen/php-fpm:8.1-dev as install

COPY . /app

WORKDIR /app

RUN composer install --no-dev --no-interaction -oq

USER docker

CMD bin/console
