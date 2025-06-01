# Document Upload Table Migration Fix

This guide provides step-by-step instructions to fix the migration conflicts between different versions of the `document_upload` table.

## Background

There have been issues with migration conflicts between different versions of the `document_upload` table. The conflicts arise from having multiple migrations that attempt to create or modify the same table structure.

## Solution Options

### Option 1: Delete and Reset Using Laravel Migrations (Recommended)

This approach uses Laravel's migration system to properly rebuild your database:

1. Open your terminal and run:
   ```
   php artisan migrate:reset
   ```
   This will roll back all migrations.

2. Then, run the migrations to create all tables with the correct structure:
   ```
   php artisan migrate
   ```

3. Optionally, seed the database with sample data:
   ```
   php artisan db:seed
   ```

**For convenience, you can use the `reset_and_migrate.bat` script:**
- Double-click on `reset_and_migrate.bat` to perform steps 1 and 2 automatically.

### Option 2: Manual Table Reset Using PHP Script

This approach directly manipulates the database to recreate the `document_upload` table:

1. Delete the current `document_upload` table in phpMyAdmin:
   - Open phpMyAdmin
   - Select your database
   - Find the `document_upload` table
   - Select "Operations" tab
   - Click "Drop" to remove the table

2. Run the PHP script to create the table with the correct structure:
   - Double-click on `reset_document_upload.bat` to run the script
   - This will create the table with the exact structure from the SQL file

3. Then run Laravel migrations to ensure all database tables are synced:
   ```
   php artisan migrate
   ```

## Details About the Fixes

1. A new migration file (`2025_04_10_000000_fix_document_upload_table.php`) has been created that properly checks and repairs the `document_upload` table structure.

2. The `doc_type` enum has been fixed to include all required values:
   - form138
   - good_moral
   - birth_certificate
   - id_picture
   - medical_clearance
   - application_form

3. The `doc_status` enum has been fixed to include all required values:
   - pending
   - approved
   - declined

## Required Table Structure

The `document_upload` table should have the following structure:

```sql
CREATE TABLE `document_upload` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `applicant_email` varchar(255) NOT NULL,
  `doc_type` enum('form138','good_moral','birth_certificate','id_picture','medical_clearance','application_form') NOT NULL,
  `doc_status` enum('pending','approved','declined') NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `docfilename` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `uploaded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_upload_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `document_upload_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Troubleshooting

If you encounter any issues with the steps above:

1. Check for error messages in the console
2. Verify that the database connection is working using `test_db_connection.php`
3. Clear the Laravel cache using `clear_cache.bat`
4. Check for any conflicting migrations and disable them if necessary

## Notes

- All previously created utility scripts (`fix_document_upload_table.php`, etc.) will still work, but the new approach is recommended
- The logout functionality in both admin and applicant dashboards has been verified to be working correctly
