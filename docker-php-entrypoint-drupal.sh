#!/bin/sh
set +e
echo "Start Drupal Update DB..."
/var/www/html/vendor/bin/drush --root /var/www/html/web updatedb
echo "Start Drupal Clear Cache..."
/var/www/html/vendor/bin/drush --root /var/www/html/web cache:clear all
echo "Run Drupal's Cron..."
/var/www/html/vendor/bin/drush --root /var/www/html/web core:cron
set -e

docker-php-entrypoint

exec "$@"
