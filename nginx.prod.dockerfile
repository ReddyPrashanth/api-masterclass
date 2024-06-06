FROM nginx:stable-alpine

ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

# MacOS staff group's gid is 20, so is the dialout group in alpine linux. We're not using it, let's just remove it.
RUN delgroup dialout

# RUN addgroup -g ${GID} --system laravel
# RUN adduser -G laravel --system -D -s /bin/sh -u ${UID} laravel
# RUN sed -i "s/user  nginx/user laravel/g" /etc/nginx/nginx.conf

ADD ./conf/nginx/default.prod.conf /etc/nginx/conf.d/default.conf

RUN mkdir -p /var/www/html

ADD ./conf/nginx/laravel-docker.test.pem /etc/nginx/certs/laravel-docker.test.pem
ADD ./conf/nginx/laravel-docker.test-key.pem /etc/nginx/certs/laravel-docker.test-key.pem