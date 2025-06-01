@echo off
echo ===================================================
echo Laravel Migration Reset and Document Upload Fix
echo ===================================================
echo.

echo Running database migration reset...
php artisan migrate:reset
echo.

echo Running Laravel migrations...
php artisan migrate
echo.

echo Process completed.
pause
