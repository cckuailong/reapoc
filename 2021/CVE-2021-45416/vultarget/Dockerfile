# Dockerfile for RosarioSIS
# https://www.rosariosis.org/
# Best Dockerfile practices: http://crosbymichael.com/dockerfile-best-practices.html

# https://hub.docker.com/_/php?tab=tags&page=1&name=apache
# TODO When moving to PHP8.0, remove xmlrpc extension!
FROM php:7.4-apache

LABEL maintainer="François Jacquet <francoisjacquet@users.noreply.github.com>"

ENV PGHOST=db \
    PGUSER=rosario \
    PGPASSWORD=rosariopwd \
    PGDATABASE=rosariosis \
    PGPORT=5432 \
    ROSARIOSIS_YEAR=2021

# Upgrade packages.
# Install git, Apache2 + PHP + PostgreSQL webserver, sendmail, wkhtmltopdf & others utilities.
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install postgresql-client wkhtmltopdf libpq-dev libpng-dev libxml2-dev libzip-dev libonig-dev sendmail nano locales  -y;

# Install PHP extensions.
RUN docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr; \
    docker-php-ext-install -j$(nproc) gd mbstring xml pgsql gettext xmlrpc zip

# Download and extract rosariosis
ENV ROSARIOSIS_VERSION 'v8.2.1'
COPY rosariosis-v8.2.1.tar.gz /var/www/html
WORKDIR /var/www/html

RUN mkdir /usr/src/rosariosis && \
    tar xvf rosariosis-v8.2.1.tar.gz && \
    mv ./rosariosis-v8.2.1/* /usr/src/rosariosis && \
    rm -rf /var/www/html && mkdir -p /var/www && \
    ln -s /usr/src/rosariosis/ /var/www/html && chmod 777 /var/www/html &&\
    chown -R www-data:www-data /usr/src/rosariosis

# Copy our configuration files.
COPY conf/config.inc.php /usr/src/rosariosis/config.inc.php
COPY conf/.htaccess /usr/src/rosariosis/.htaccess
COPY bin/init /init

EXPOSE 80

ENTRYPOINT ["/init"]
CMD ["apache2-foreground"]