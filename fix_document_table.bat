@echo off
echo ====================================================
echo Document Upload Table Structure Fix Script
echo ====================================================

echo Clearing configuration cache...
php artisan config:clear

echo Running document_upload table structure fix...
php fix_document_upload_table.php

echo ====================================================
echo Document upload table fix process complete!
echo ====================================================
