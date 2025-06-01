@echo off
REM Commands to reset Laravel caches and reload configurations

echo Clearing Laravel caches...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear

echo Creating folders if they don't exist...
mkdir storage\framework\sessions -p
mkdir storage\framework\views -p
mkdir storage\framework\cache -p

echo Setting proper permissions...
icacls storage /grant Everyone:F /T

echo Running essential migrations for authentication...
php artisan migrate --path=database/migrations/2025_05_14_000000_create_admins_table.php
php artisan migrate --path=database/migrations/2025_05_14_000001_create_applicants_table.php

echo Setting up document_upload table...
php fix_document_upload_table.php

REM Alternative methods (pick one if the above doesn't work)
REM echo Setting up document_upload table (alternative method 1)...
REM php setup_document_upload.php
REM echo Setting up document_upload table (alternative method 2)...
REM mysql -u root -h 127.0.0.1 bit_docs_upload < document_upload.sql

echo Seeding database with initial accounts...
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=ApplicantSeeder

echo Clearing configuration cache again...
php artisan config:clear

echo ======================================
echo All cache cleared and system refreshed
echo ======================================
echo Admin login: admin@example.com / password
echo Applicant login: applicant@example.com / password
