<?php
// Script to completely reset and recreate the document_upload table
// This script will:
// 1. Check if the table exists
// 2. If it does, drop it
// 3. Create the table from scratch based on the SQL structure

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "========================================================\n";
echo "Document Upload Table Complete Reset\n";
echo "========================================================\n\n";

// Test database connection
try {
    DB::connection()->getPdo();
    echo "Connected to database: " . DB::connection()->getDatabaseName() . "\n\n";
} catch (\Exception $e) {
    die("Could not connect to the database. Error: " . $e->getMessage() . "\n\n");
}

// Check if document_upload table exists and drop it
if (Schema::hasTable('document_upload')) {
    echo "- document_upload table exists. Dropping the table...\n";
    Schema::dropIfExists('document_upload');
    echo "  Table dropped successfully.\n\n";
} else {
    echo "- document_upload table doesn't exist. Will create it from scratch.\n\n";
}

// Create the table with correct structure
echo "- Creating document_upload table with correct structure...\n";
DB::statement("CREATE TABLE `document_upload` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `applicant_email` varchar(255) NOT NULL,
  `doc_type` enum('form138','good_moral','birth_certificate','id_picture','medical_clearance','application_form') NOT NULL,
  `doc_status` enum('pending','approved','declined') NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `docfilename` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `uploaded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Add primary key, index, and auto increment
DB::statement("ALTER TABLE `document_upload` ADD PRIMARY KEY (`id`)");
DB::statement("ALTER TABLE `document_upload` ADD KEY `document_upload_uploaded_by_foreign` (`uploaded_by`)");
DB::statement("ALTER TABLE `document_upload` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1");

// Add foreign key
try {
    DB::statement("ALTER TABLE `document_upload` ADD CONSTRAINT `document_upload_uploaded_by_foreign` 
                 FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)");
    echo "  Foreign key constraint added successfully.\n";
} catch (\Exception $e) {
    echo "  Warning: Could not add foreign key constraint. Error: " . $e->getMessage() . "\n";
    echo "  Table structure is still correct, but the foreign key is missing.\n";
}

// Check if the table was created successfully
if (Schema::hasTable('document_upload')) {
    echo "\n✅ document_upload table created successfully with the correct structure.\n\n";
    
    // Verify enum fields
    $columns = DB::select("SHOW COLUMNS FROM document_upload WHERE Field IN ('doc_type', 'doc_status')");
    foreach ($columns as $column) {
        echo "  Column '{$column->Field}' type: {$column->Type}\n";
    }
    
    echo "\nTable is ready for use with Laravel migrations.\n";
    echo "Now you can run 'php artisan migrate:reset' and then 'php artisan migrate' to properly set up all tables.\n";
} else {
    echo "\n❌ Failed to create the document_upload table. Please check for errors.\n";
}

echo "\nProcess completed.\n";
