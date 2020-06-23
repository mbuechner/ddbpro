FROM composer:1 AS COMPOSER_CHAIN
MAINTAINER Michael Büchner <m.buechner@dnb.de>
RUN apk add --no-cache libpng libpng-dev libjpeg-turbo-dev libwebp-dev zlib-dev libxpm-dev
RUN docker-php-ext-install gd
COPY / /tmp/ddbpro
WORKDIR /tmp/ddbpro
RUN composer install --no-dev || true && composer install --no-dev

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
RUN { \
		echo "<VirtualHost *:80>"; \
		echo "  ServerAdmin mbuechner@dnb.de"; \
		echo "  DocumentRoot /var/www/html/web"; \
		echo "  ErrorLog ${APACHE_LOG_DIR}/error.log"; \
		echo "  CustomLog ${APACHE_LOG_DIR}/access.log combined"; \
		echo "</VirtualHost>"; \
	} > /etc/apache2/sites-enabled/000-default.conf

# Add mysql for D7
RUN apt-get install -y --no-install-recommends mariadb-client

# Clean system
RUN apt-get clean
RUN rm -rf /var/lib/apt/lists/*

ENTRYPOINT ["docker-php-entrypoint-drupal"]
HEALTHCHECK --interval=1m --timeout=3s CMD curl --fail http://localhost/ || exit 1
EXPOSE 80
CMD ["apache2-foreground"]

