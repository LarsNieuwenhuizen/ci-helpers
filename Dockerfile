FROM larsnieuwenhuizen/php-fpm:8.1-dev as install

COPY --chown=docker:dockerlocal . /app

WORKDIR /app

RUN composer install --no-dev --no-interaction -oq; \
    mkdir -p /app/code; \
    chown -R docker:dockerlocal /app;

ENTRYPOINT bin/console
