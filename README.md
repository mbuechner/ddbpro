![PHP Composer](https://github.com/mbuechner/ddbpro/workflows/PHP%20Composer/badge.svg)
# DDBpro - Das Portal für Datenpartner der Deutschen Digitalen Bibliothek
This is a [Composer](https://getcomposer.org/) project for [DDBpro](http://pro.deutsche-digitale-bibliothek.de/).

## Migrate existing DDBpro
### 1. Database
The connection (to an existing) database can be configured in `web/sites/default/settings.php`. Add something like:
```
$databases['default']['default'] = array(
  'driver' => 'mysql',
  'database' => 'ddbpro',
  'username' => 'ddbpro',
  'password' => 'ddbpro',
  'host' => '127.0.0.1',
  'prefix' => '',
  'collation' => 'utf8_general_ci',
);
```
A possible proxy configuration can also be configured here.

### 2. Build Project
```
> composer install
```

### 3. Rebuild Registry
The pathes to all Drupal modules (might) have been changed and Drupal won't run correctly. To fix that, it's necessary to rebuild the registry. Therefor the module [Registry Rebuild](https://www.drupal.org/project/registry_rebuild) is already installed. Run:
```
> cd web/sites/all/modules/contrib/registry_rebuild
> php registry_rebuild.php
```

Rebuilding the registry is only necessary once, while migrating an existing DDBpro instance to DDBpro Composer. The Registry Rebuild module can be removed afterwards (`composer remove drupal/registry_rebuild`).

### 4. Clear Cache and Run Database Updates
Run:
```
> vendor/drush/drush/drush cc all
```
...and...
```
> vendor/drush/drush/drush updatedb
```

### 5. Files
DDBpro specific files are stored in the directory `download/` with its subdirectories `private/` and `public/`. The folder `download/` should be located in `web/` within the DDBpro Composer project.

### 6. Done
Yeah! :-)

## Build-in webserver
For testing and developing purposes [Drush](https://www.drush.org/) does provide a webserver. Run:
```
> vendor/drush/drush/drush rs
```
Website is available: http://127.0.0.1:8888/

## Core & Module Updates
Drupal's core, all modules and all necessary libraries are managed by Composer.

- Install new module? Do it with Composer.
- Update existing modules? Do it with Composer.
- Does a module need a specific PHP library? Composer. ;-)

Get a list of update-able packages.
```
> composer update --dry-run
```
Update all packages (be careful!):
```
> composer update
```
Update specific package:
```
> composer update drupal/drupal
```

## Module Installation
To install a new module to Drupal use:
```
> composer require drupal/faq
```
To remove an existing module (be sure it's disabled in Drupal before!):
```
> composer remove drupal/faq
```

## Useful Drush Commands
### Clear all cache
```
> vendor/drush/drush/drush cc all
```
### Run Cron Job
```
> vendor/drush/drush/drush core-cron
```
