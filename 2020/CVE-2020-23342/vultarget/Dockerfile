FROM ubuntu:xenial 
# 基础镜像是Ubuntu16.04-xenial
MAINTAINER dxy
# 设置生成这个镜像的作者

ADD sources.list /etc/apt/ 
# 换源
ENV OS_LOCALE="en_US.UTF-8"

RUN apt-get update && apt-get install -y locales && locale-gen ${OS_LOCALE} && apt-get install -y apt-transport-https
# xenial系统区域语言设置为中文

ENV LANG=${OS_LOCALE} \
    LANGUAGE=${OS_LOCALE} \
    LC_ALL=${OS_LOCALE} \
    DEBIAN_FRONTEND=noninteractive
# DEBIAN_FRONTEND告知操作系统应该从哪儿获得用户输入。设置为”noninteractive”意味着可以直接运行命令，无需向用户请求输入
#（所有操作都是非交互式的）这在运行apt-get命令的时候格外有用，因为它会不停的提示用户进行到了哪步并且需要不断确认。
# 非交互模式会选择默认的选项并以最快的速度完成构建。
# https://blog.csdn.net/oguro/article/details/102840215

ENV APACHE_CONF_DIR=/etc/apache2 \
    PHP_CONF_DIR=/etc/php/7 \
    PHP_DATA_DIR=/var/lib/php

COPY entrypoint.sh /sbin/entrypoint.sh
# 将本地的文件or文件夹复制到镜像中的指定路径下

RUN	\
# 在xenial系统中进行基础LAMP环境安装
    BUILD_DEPS='software-properties-common python-software-properties' \
	&& apt-get install --no-install-recommends -y $BUILD_DEPS \
	&& add-apt-repository -y ppa:ondrej/php \
	&& add-apt-repository -y ppa:ondrej/apache2 \
    # PPA是Personal Package Archives个人软件包文档,只有Ubuntu用户可以用，所有的PPA都是寄存在 launchpad.net网站上。
    # add-apt-repository向apt-get的source.list中添加新的下载源
    && find /etc/apt/sources.list.d/ -type f -name "ondrej-ubuntu-php-xenial.list" -exec sed -i.bak -r 's#deb(-src)?\s*http(s)?://ppa.launchpad.net#deb\1 https\2://launchpad.proxy.ustclug.org#ig' {} \;\	
    && find /etc/apt/sources.list.d/ -type f -name "ondrej-ubuntu-apache2-xenial.list" -exec sed -i.bak -r 's#deb(-src)?\s*http(s)?://ppa.launchpad.net#deb\1 https\2://launchpad.proxy.ustclug.org#ig' {} \;\
    && apt-get update \
    && apt-get install -y git vim unzip curl apache2 php7.1-mbstring php7.1-mcrypt php7.1-curl php7.1-json php7.1-pdo php7.1-gd php7.1-intl php7.1-opcache libapache2-mod-php7.1 php7.1-cli php7.1-readline php7.1-pgsql php7.1-mysql php-pear \
# Apache settings 网页服务器 设置
    && cp /dev/null ${APACHE_CONF_DIR}/conf-available/other-vhosts-access-log.conf \
    # 清空 other-vhosts-access-log.conf
    && rm ${APACHE_CONF_DIR}/sites-enabled/000-default.conf ${APACHE_CONF_DIR}/sites-available/000-default.conf \
    #　删除000-default.conf
    && a2enmod rewrite php7.1 \
    # 重启php7
# PHP settings
	&& phpenmod mcrypt \
    && curl -sS https://getcomposer.org/installer | php -- --version=1.6.4 --install-dir=/usr/local/bin --filename=composer \
# Cleaning 主要是为了减少最终镜像的大小
	&& apt-get purge -y --auto-remove $BUILD_DEPS \
	&& apt-get autoremove -y \
    # apt-get purge 会同时清除软件包和软件的配置文件
    # 删除为了满足其他软件包的依赖而安装的，但现在不再需要的软件包。
	&& rm -rf /var/lib/apt/lists/* \
    # 安装软件包和清理缓存需要在同一条RUN语句中执行，因为每一条RUN语句都会增加一层，
    # 把apt-get和rm -rf /var/lib/apt/lists/*放在同一条RUN 清理apt-get产生的缓存
# Forward request and error logs to docker log collector
	&& ln -sf /dev/stdout /var/log/apache2/access.log \
	&& ln -sf /dev/stderr /var/log/apache2/error.log \
#　用户权限设置
    #chmod 755 设置用户的权限为：
    #1.文件所有者可读可写可执行 2.与文件所有者同属一个用户组的其他用户可读可执行 3.其它用户组可读可执行
	&& chmod 755 /sbin/entrypoint.sh \
    && chown www-data:www-data ${PHP_DATA_DIR} -Rf
    # 将目录下的所有文件、子目录的所有者改成www-data

COPY ./configs/apache2.conf ${APACHE_CONF_DIR}/apache2.conf
COPY ./configs/app.conf ${APACHE_CONF_DIR}/sites-enabled/app.conf
COPY ./configs/php.ini  ${PHP_CONF_DIR}/apache2/conf.d/custom.ini

COPY ./anchor-cms/ /var/www/app/
RUN chmod 777 /var/www/app
RUN chmod 777 /var/www/app/anchor
RUN chmod 777 /var/www/app/anchor/config
RUN chmod 777 /var/www/app/content
RUN chmod 777 /var/www/app/install

WORKDIR /var/www/app/

EXPOSE 80

CMD ["/sbin/entrypoint.sh"]