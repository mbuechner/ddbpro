#!/bin/sh
set +e
echo "Start Drupal Update DB..."
/var/www/html/vendor/bin/drush -y updatedb
echo "Start Drupal Clear Cache..."
/var/www/html/vendor/bin/drush -y cache:clear all
echo "Run Drupal's Cron..."
/var/www/html/vendor/bin/drush -y core:cron
set -e

docker-php-entrypoint

exec "$@"
