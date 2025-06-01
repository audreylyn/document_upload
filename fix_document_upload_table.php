<?php

// This script specifically checks and fixes the document_upload table structure
// to match exactly what's defined in document_upload.sql

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "========================================================\n";
echo "Document Upload Table Structure Verification and Repair\n";
echo "========================================================\n\n";

// Test database connection
try {
    DB::connection()->getPdo();
    echo "Connected to database: " . DB::connection()->getDatabaseName() . "\n\n";
} catch (\Exception $e) {
    echo "Could not connect to the database. Error: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Check if document_upload table exists
if (Schema::hasTable('document_upload')) {
    echo "document_upload table exists. Checking structure...\n\n";
    
    // Get current structure
    $currentColumns = DB::select("SHOW COLUMNS FROM document_upload");
    $currentStructure = [];
    foreach ($currentColumns as $column) {
        $currentStructure[$column->Field] = [
            'type' => $column->Type,
            'null' => $column->Null,
            'default' => $column->Default
        ];
    }
    
    // Check for enum fields that might need fixing
    $docTypeEnum = $currentStructure['doc_type']['type'] ?? '';
    $docStatusEnum = $currentStructure['doc_status']['type'] ?? '';
    
    $correctDocTypeEnum = "enum('form138','good_moral','birth_certificate','id_picture','medical_clearance','application_form')";
    $correctDocStatusEnum = "enum('pending','approved','declined')";
    
    $needsRebuild = false;
    
    if ($docTypeEnum !== $correctDocTypeEnum) {
        echo "❌ doc_type enum is incorrect. Current: $docTypeEnum\n";
        echo "   Should be: $correctDocTypeEnum\n\n";
        $needsRebuild = true;
    } else {
        echo "✅ doc_type enum is correct\n\n";
    }
    
    if ($docStatusEnum !== $correctDocStatusEnum) {
        echo "❌ doc_status enum is incorrect. Current: $docStatusEnum\n";
        echo "   Should be: $correctDocStatusEnum\n\n";
        $needsRebuild = true;
    } else {
        echo "✅ doc_status enum is correct\n\n";
    }
    
    // If structure needs fixing, rebuild the table
    if ($needsRebuild) {
        echo "Rebuilding document_upload table with correct structure...\n\n";
        
        // Backup existing data
        echo "- Backing up existing data...\n";
        $existingData = DB::table('document_upload')->get();
        echo "  " . count($existingData) . " records found\n";
        
        // Drop the table
        echo "- Dropping existing table...\n";
        Schema::dropIfExists('document_upload');
        
        // Create new table with correct structure
        echo "- Creating table with correct structure...\n";
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
        DB::statement("ALTER TABLE `document_upload` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13");
        
        // Add foreign key
        try {
            DB::statement("ALTER TABLE `document_upload` ADD CONSTRAINT `document_upload_uploaded_by_foreign` 
                         FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)");
        } catch (Exception $e) {
            echo "  Warning: Could not add foreign key constraint: " . $e->getMessage() . "\n";
        }
        
        // Restore data if possible
        if (count($existingData) > 0) {
            echo "- Restoring data...\n";
            
            $restored = 0;
            foreach ($existingData as $record) {
                // Convert object to array
                $recordArray = (array) $record;
                
                // Make sure doc_type and doc_status values are valid for the new enums
                if (!in_array($recordArray['doc_type'], ['form138', 'good_moral', 'birth_certificate', 'id_picture', 'medical_clearance', 'application_form'])) {
                    $recordArray['doc_type'] = 'form138'; // Default fallback
                }
                
                if (!in_array($recordArray['doc_status'], ['pending', 'approved', 'declined'])) {
                    $recordArray['doc_status'] = 'pending'; // Default fallback
                }
                
                try {
                    DB::table('document_upload')->insert($recordArray);
                    $restored++;
                } catch (Exception $e) {
                    echo "  Error restoring record ID {$recordArray['id']}: {$e->getMessage()}\n";
                }
            }
            
            echo "  Restored $restored out of " . count($existingData) . " records\n";
        }
        
        echo "\n✅ Table rebuild complete!\n";
    } else {
        echo "✅ document_upload table structure appears to be correct. No changes needed.\n";
    }
} else {
    echo "❌ document_upload table does not exist! Creating it now...\n\n";
    
    // Create the table with the correct structure
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
    } catch (Exception $e) {
        echo "Warning: Could not add foreign key constraint: " . $e->getMessage() . "\n";
    }
    
    echo "✅ document_upload table created successfully!\n";
}

echo "\n========================================================\n";
echo "Process completed.\n";
echo "========================================================\n";
