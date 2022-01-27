FROM php:7.4-apache

COPY php.ini /usr/local/etc/php/

RUN a2enmod rewrite expires

# install the PHP extensions we need
RUN apt-get update && apt-get install -y wget libpng-dev libjpeg-dev gnupg  default-mysql-client nano less libfreetype6-dev && rm -rf /var/lib/apt/lists/* \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install gd mysqli

# blackfire agent
RUN wget -q -O - https://packages.blackfire.io/gpg.key | apt-key add - 
RUN echo "deb http://packages.blackfire.io/debian any main" | tee /etc/apt/sources.list.d/blackfire.list
RUN apt-get update && apt-get install -y blackfire-agent blackfire-php

VOLUME /var/www/html

RUN curl -o /bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
	&& chmod +x /bin/wp \
	&& wp --info --allow-root

ENV WP_VERSION 5.4.2
ENV TZ Europe/Paris

COPY entrypoint.sh /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
# ENTRYPOINT resets CMD now
CMD ["apache2-foreground"]
