FROM larsnieuwenhuizen/php-fpm:8.1-dev as install

COPY . /app
WORKDIR /app
RUN composer install --no-dev --no-interaction -oq

FROM larsnieuwenhuizen/php-fpm:8.1 as result

COPY --chown=docker:dockerlocal --from=install /app /app
WORKDIR /app
USER docker
CMD bin/console
