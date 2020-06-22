#!/bin/sh
set +e
echo "Start Drupal Update DB..."
/var/www/html/vendor/bin/drush --root /var/www/html/web -y updatedb
echo "Start Drupal Clear Cache..."
/var/www/html/vendor/bin/drush --root /var/www/html/web -y cache:clear all
echo "Run Drupal's Cron..."
/var/www/html/vendor/bin/drush --root /var/www/html/web -y core:cron
set -e

docker-php-entrypoint

exec "$@"
