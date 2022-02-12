FROM ubuntu:18.04
MAINTAINER nopdata <nopdata01@gmail.com>

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update
RUN apt-get install vim php7.2 php7.2-dev php-pear zip -y
COPY ./source/Archive_Tar-1.4.10.tgz /srv
COPY ./source/exploit.zip /srv
RUN pear install -f /srv/Archive_Tar-1.4.10.tgz
