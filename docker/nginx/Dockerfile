FROM nginx
ARG APP_SERVER_NAME=localhost
ARG APP_SERVER_ROOT=/var/www/html
ARG PHP_FPM_HOST=php
ARG PHP_FPM_PORT=9000

COPY bin/substitute-env-vars.sh substitute-env-vars.sh
COPY nginx.conf nginx.conf.tmp
COPY www.conf www.conf.tmp
COPY upstream.conf upstream.conf.tmp

RUN ./substitute-env-vars.sh nginx.conf.tmp > /etc/nginx/nginx.conf
RUN ./substitute-env-vars.sh www.conf.tmp > /etc/nginx/www.conf
RUN ./substitute-env-vars.sh upstream.conf.tmp > /etc/nginx/upstream.conf
