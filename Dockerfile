FROM alpine:3.13

# php install
RUN \
    sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g' /etc/apk/repositories && \
    apk add --update --no-cache --allow-untrusted \
    wget \
    nginx \
    tzdata \
    git \
    php7 \
    php7-fpm \
    php7-bcmath \
    php7-ctype \
    php7-curl \
    php7-dom \
    php7-fileinfo \
    php7-json \
    php7-mbstring \
    php7-openssl \
    php7-opcache \
    php7-pdo_mysql \
    php7-pdo_sqlite \
    php7-phar \
    php7-session \
    php7-sqlite3 \
    php7-tokenizer \
    php7-xml \
    php7-xmlwriter \
    php7-zip && \
    wget -q https://mirrors.aliyun.com/composer/composer.phar -O /usr/local/bin/composer && \
    chmod a+x /usr/local/bin/composer && \
    COMPOSER_ALLOW_SUPERUSER=1 composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/


# php config
RUN php_vars='/etc/php7/conf.d/docker_vars.ini' && \
    echo "cgi.fix_pathinfo=0" > ${php_vars} && \
    echo "upload_max_filesize = 100M" >> ${php_vars} && \
    echo "post_max_size = 100M" >> ${php_vars} && \
    echo "variables_order = \"EGPCS\"" >> ${php_vars} && \
    echo "memory_limit = 128M" >> ${php_vars} && \
    echo "expose_php = Off" >> ${php_vars} && \
    echo "max_execution_time = 300" >> ${php_vars} && \
    sed -i \
        -e "s/;catch_workers_output\s*=\s*yes/catch_workers_output = yes/g" \
        -e "s/pm.max_children = 5/pm.max_children = 20/g" \
        -e "s/pm.start_servers = 2/pm.start_servers = 5/g" \
        -e "s/pm.min_spare_servers = 1/pm.min_spare_servers = 5/g" \
        -e "s/pm.max_spare_servers = 3/pm.max_spare_servers = 8/g" \
        -e "s/;pm.max_requests = 500/pm.max_requests = 500/g" \
        -e "s/user = nobody/user = nginx/g" \
        -e "s/group = nobody/group = nginx/g" \
        -e "s/;listen.mode = 0660/listen.mode = 0666/g" \
        -e "s/;listen.owner = nobody/listen.owner = nginx/g" \
        -e "s/;listen.group = nobody/listen.group = nginx/g" \
        -e "s/listen = 127.0.0.1:9000/listen = \/var\/run\/php-fpm.sock/g" \
        -e "s/^;clear_env = no$/clear_env = no/" \
        /etc/php7/php-fpm.d/www.conf

ADD . /var/www/

# nginx config
RUN rm -Rf /etc/nginx/nginx.conf /etc/nginx/mime.types /etc/nginx/conf.d/default.conf && \
    ln -s /var/www/docker_conf/nginx.conf /etc/nginx/nginx.conf && \
    ln -s /var/www/docker_conf/mime.types /etc/nginx/mime.types && \
    ln -s /var/www/docker_conf/nginx-site.conf /etc/nginx/conf.d/default.conf

# Change the owner of the storage directory
RUN chown -Rf nginx.nginx /var/www/storage && \
    chown -Rf nginx.nginx /var/www/bootstrap/cache && \
    # Change timezone to CST
    cp /usr/share/zoneinfo/Asia/Shanghai /etc/localtime && \
    echo "Asia/Shanghai" > /etc/timezone && \
    # Composer install
    cd /var/www && \
    COMPOSER_ALLOW_SUPERUSER=1 composer install --no-ansi --no-dev --prefer-dist --no-progress --no-interaction --optimize-autoloader 2>&1 && \
    COMPOSER_ALLOW_SUPERUSER=1 composer clear-cache 2>&1 && \
    rm -f /var/www/.env /var/www/storage/logs/* && \
    ln -s /var/www/storage/app/public /var/www/public/storage && \
    # Frontend compile
    apk add --update --no-cache yarn && \
    cd /var/www && \
    yarn config set registry 'https://registry.npm.taobao.org' && \
    yarn install && \
    FORCE_COLOR=0 yarn run production --no-progress --non-interactive  && \
    rm -rf node_modules && \
    yarn cache clean && \
    apk del --no-cache --purge yarn

RUN touch /var/www/start_serve && \
    echo "php-fpm7" >> /var/www/start_serve && \
    echo "nginx" >> /var/www/start_serve && \
    chmod a+x /var/www/start_serve

VOLUME ["/var/www/.env", "/var/www/storage"]

WORKDIR /var/www

EXPOSE 80

ENTRYPOINT /var/www/start_serve
