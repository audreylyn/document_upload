@echo off
echo Clearing Laravel application cache...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo Cache cleared successfully!
