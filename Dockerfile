# See https://github.com/composer/docker/blob/2a6335d61cc92b7c43b69072ac107c4afb568f49/2.0/Dockerfile
# The following is identical, except the use of PHP 7.
FROM php:7-alpine AS COMPOSER_CHAIN
MAINTAINER Michael Büchner <m.buechner@dnb.de>
RUN set -eux; \
  apk add --no-cache --virtual .composer-rundeps \
    bash \
    coreutils \
    git \
    make \
    mercurial \
    openssh-client \
    patch \
    subversion \
    tini \
    unzip \
    zip
RUN set -eux; \
  apk add --no-cache --virtual .build-deps \
    libzip-dev \
    zlib-dev; \
  docker-php-ext-install -j "$(nproc)" \
    zip; \
  runDeps="$( \
    scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
      | tr ',' '\n' \
      | sort -u \
      | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
    )"; \
  apk add --no-cache --virtual .composer-phpext-rundeps $runDeps; \
  apk del .build-deps

RUN printf "# composer php cli ini settings\n\
date.timezone=UTC\n\
memory_limit=-1\n\
" > $PHP_INI_DIR/php-cli.ini

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp

RUN set -eux; \
  curl --silent --fail --location --retry 3 --output /tmp/keys.dev.pub --url https://raw.githubusercontent.com/composer/composer.github.io/e7f28b7200249f8e5bc912b42837d4598c74153a/snapshots.pub; \
  curl --silent --fail --location --retry 3 --output /tmp/keys.tags.pub --url https://raw.githubusercontent.com/composer/composer.github.io/e7f28b7200249f8e5bc912b42837d4598c74153a/releases.pub; \
  curl --silent --fail --location --retry 3 --output /tmp/installer.php --url https://raw.githubusercontent.com/composer/getcomposer.org/cb19f2aa3aeaa2006c0cd69a7ef011eb31463067/web/installer; \
  php -r " \
    \$signature = '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5'; \
    \$hash = hash('sha384', file_get_contents('/tmp/installer.php')); \
    if (!hash_equals(\$signature, \$hash)) { \
      unlink('/tmp/installer.php'); \
      echo 'Integrity check failed, installer is either corrupt or worse.' . PHP_EOL; \
      exit(1); \
    }"; \
  php /tmp/installer.php --no-ansi --install-dir=/usr/bin --filename=composer; \
  composer --ansi --version --no-interaction; \
  composer diagnose; \
  rm -f /tmp/installer.php; \
  find /tmp -type d -exec chmod -v 1777 {} +

# the following is for DDBpro
RUN apk add --no-cache libpng libpng-dev libjpeg-turbo-dev libwebp-dev zlib-dev libxpm-dev
RUN docker-php-ext-install gd
COPY / /tmp/ddbpro
WORKDIR /tmp/ddbpro
RUN composer install --no-dev

FROM php:7.4-apache
MAINTAINER Michael Büchner <m.buechner@dnb.de>
RUN set -eux; \
	if command -v a2enmod; then \
		a2enmod rewrite; \
	fi; \
	savedAptMark="$(apt-mark showmanual)"; \
	apt-get update; \
	apt-get install -y --no-install-recommends \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
		libpq-dev \
		libzip-dev; \
	docker-php-ext-configure gd \
		--with-freetype \
		--with-jpeg; \
	docker-php-ext-install -j "$(nproc)" \
		gd \
		opcache \
		pdo_mysql \
		pdo_pgsql \
		zip; \
	pecl install uploadprogress; \
	docker-php-ext-enable uploadprogress; \
	apt-mark auto '.*' > /dev/null; \
	apt-mark manual $savedAptMark; \
	ldd "$(php -r 'echo ini_get("extension_dir");')"/*.so \
		| awk '/=>/ { print $3 }' \
		| sort -u \
		| xargs -r dpkg-query -S \
		| cut -d: -f1 \
		| sort -u \
		| xargs -rt apt-mark manual; \
	apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false;

RUN { \
		echo "opcache.file_update_protection=0"; \
		echo "opcache.validate_timestamps=0"; \
		echo "opcache.interned_strings_buffer=16"; \
		echo "opcache.memory_consumption=128"; \
		echo "opcache.max_accelerated_files=4000"; \
		echo "opcache.max_wasted_percentage=10"; \
		echo "opcache.revalidate_freq=60"; \
	} > /usr/local/etc/php/conf.d/opcache-recommended.ini
RUN { \
		echo "upload_max_filesize = 128M"; \
		echo "post_max_size = 128M"; \
		echo "memory_limit = 512M"; \
		echo "max_execution_time = 600"; \
		echo "max_input_vars = 5000"; \
	} > /usr/local/etc/php/conf.d/0-upload_large_dumps.ini

WORKDIR /var/www/html
COPY --from=COMPOSER_CHAIN /tmp/ddbpro/ .
COPY docker-php-entrypoint-drupal.sh /usr/local/bin/docker-php-entrypoint-drupal
RUN chmod 775 /usr/local/bin/docker-php-entrypoint-drupal
RUN find . -type d -exec chmod 755 {} \;
RUN find . -type f -exec chmod 644 {} \;
RUN chown -R www-data:www-data web/sites web/modules web/themes
RUN chmod +x /var/www/html/vendor/drush/drush/drush /var/www/html/vendor/drush/drush/drush.launcher
RUN echo "LISTEN 8080" > /etc/apache2/ports.conf
RUN { \
		echo "<VirtualHost *:8080>"; \
		echo "  ServerAdmin m.buechner@dnb.de"; \
		echo "  DocumentRoot /var/www/html/web"; \
		echo "  ErrorLog /dev/stderr"; \
		echo "  CustomLog /dev/stdout combined"; \
		echo "</VirtualHost>"; \
	} > /etc/apache2/sites-enabled/000-default.conf

# Add mysql for D7
RUN apt-get install -y --no-install-recommends mariadb-client

# Clean system
RUN apt-get clean
RUN rm -rf /var/lib/apt/lists/*

ENTRYPOINT ["docker-php-entrypoint-drupal"]
HEALTHCHECK --interval=1m --timeout=3s CMD curl --fail http://localhost:8080/ || exit 1
EXPOSE 8080
CMD ["apache2-foreground"]
