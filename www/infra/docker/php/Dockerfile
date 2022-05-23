FROM php:8.0-fpm-buster
LABEL maintainer="jinqugan <jinqgan@gmail.com>"
SHELL ["/bin/bash", "-oeux", "pipefail", "-c"]

# timezone environment
ENV TZ=Asia/Kuala_Lumpur \
  # locale
  LANG=en_US.UTF-8 \
  LANGUAGE=en_US:en \
  LC_ALL=en_US.UTF-8 \
  # composer environment
  COMPOSER_ALLOW_SUPERUSER=1 \
  COMPOSER_HOME=/composer

COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer

RUN apt-get update && \
  apt-get -y install git curl libicu-dev libonig-dev libzip-dev unzip locales && \
  apt-get clean && \
  rm -rf /var/lib/apt/lists/* && \
  locale-gen en_US.UTF-8 && \
  localedef -f UTF-8 -i en_US en_US.UTF-8 && \
  mkdir /var/run/php-fpm && \
  docker-php-ext-install intl pdo_mysql zip bcmath && \
  composer config -g process-timeout 3600 && \
  composer config -g repos.packagist composer https://packagist.org

COPY ./infra/docker/php/php-fpm.d/zzz-www.conf /usr/local/etc/php-fpm.d/zzz-www.conf
COPY ./infra/docker/php/php.ini /usr/local/etc/php/php.ini

WORKDIR /var/www/backend

# # Add user for laravel application
# RUN groupadd -g 1000 www
# RUN useradd -u 1000 -ms /bin/bash -g www www

# # Copy existing application directory permissions
# COPY --chown=www:www ./backend /var/www/backend

# # Change current user to www
# USER www

# EXPOSE 3306
EXPOSE 9000

# COPY ./docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
# RUN chmod +x /usr/local/bin/docker-entrypoint.sh 
# ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

# Add wait-for-it
COPY ./wait-for-it.sh /usr/local/bin/wait-for-it.sh 
RUN chmod +x /usr/local/bin/wait-for-it.sh

COPY ./php-fpm.sh /usr/local/bin/php-fpm.sh
RUN chmod +x /usr/local/bin/php-fpm.sh 
# CMD ["sh", "/usr/local/bin/php-fpm.sh"]

CMD bash -c "/usr/local/bin/wait-for-it.sh --timeout=0 db:3306 -- /usr/local/bin/php-fpm.sh"