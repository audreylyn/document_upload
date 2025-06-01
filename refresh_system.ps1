# refresh_system.ps1 - PowerShell script to set up the application database and configuration

Write-Host "=====================================" -ForegroundColor Cyan
Write-Host "Refreshing Laravel System" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan

Write-Host "Clearing Laravel caches..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear

Write-Host "Creating folders if they don't exist..." -ForegroundColor Yellow
if (-not (Test-Path -Path "storage\framework\sessions")) {
    New-Item -Path "storage\framework\sessions" -ItemType Directory -Force
}
if (-not (Test-Path -Path "storage\framework\views")) {
    New-Item -Path "storage\framework\views" -ItemType Directory -Force
}
if (-not (Test-Path -Path "storage\framework\cache")) {
    New-Item -Path "storage\framework\cache" -ItemType Directory -Force
}

Write-Host "Setting proper permissions..." -ForegroundColor Yellow
icacls storage /grant Everyone:F /T

Write-Host "Running essential migrations for authentication..." -ForegroundColor Yellow
php artisan migrate --path=database/migrations/2025_05_14_000000_create_admins_table.php
php artisan migrate --path=database/migrations/2025_05_14_000001_create_applicants_table.php

Write-Host "Setting up document_upload table..." -ForegroundColor Yellow
php fix_document_upload_table.php

# Alternative methods (commented out)
# Write-Host "Setting up document_upload table (alternative method)..." -ForegroundColor Yellow
# php setup_document_upload.php

Write-Host "Seeding database with initial accounts..." -ForegroundColor Yellow
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=ApplicantSeeder

Write-Host "Clearing configuration cache again..." -ForegroundColor Yellow
php artisan config:clear

Write-Host "======================================" -ForegroundColor Green
Write-Host "All cache cleared and system refreshed" -ForegroundColor Green
Write-Host "======================================" -ForegroundColor Green
Write-Host "Admin login: admin@example.com / password" -ForegroundColor Cyan
Write-Host "Applicant login: applicant@example.com / password" -ForegroundColor Cyan
