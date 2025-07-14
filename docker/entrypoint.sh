#!/bin/sh
composer install --no-dev --optimize-autoloader
exec apache2-foreground