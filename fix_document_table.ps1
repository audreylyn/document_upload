# Fix document_upload table PowerShell script

Write-Host "====================================================" -ForegroundColor Cyan
Write-Host "Document Upload Table Structure Fix Script" -ForegroundColor Cyan
Write-Host "====================================================" -ForegroundColor Cyan

# Clear Laravel cache to avoid any configuration issues
Write-Host "Clearing configuration cache..." -ForegroundColor Yellow
php artisan config:clear

# Run the table fix script
Write-Host "Running document_upload table structure fix..." -ForegroundColor Yellow
php fix_document_upload_table.php

Write-Host "====================================================" -ForegroundColor Green
Write-Host "Document upload table fix process complete!" -ForegroundColor Green
Write-Host "====================================================" -ForegroundColor Green
