#!/bin/sh
set +e
echo "Start Drupal Update DB..."
/var/www/html/vendor/bin/drush --root /var/www/html/web updb
echo "Start Drupal Clear Cache..."
/var/www/html/vendor/bin/drush --root /var/www/html/web cc all
echo "Run Drupal's Cron..."
/var/www/html/vendor/bin/drush --root /var/www/html/web cron
set -e

docker-php-entrypoint

exec "$@"
