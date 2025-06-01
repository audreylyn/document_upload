<?php

// A simple script to test the database connection and document_upload table
// This can be run from the command line to verify the table structure and connection

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=================================================\n";
echo "Database Connection and Table Structure Verifier\n";
echo "=================================================\n\n";

// Test database connection
try {
    DB::connection()->getPdo();
    echo "✅ Successfully connected to database: " . DB::connection()->getDatabaseName() . "\n\n";
} catch (\Exception $e) {
    echo "❌ Could not connect to the database. Error: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Check if document_upload table exists
if (Schema::hasTable('document_upload')) {
    echo "✅ document_upload table exists.\n\n";
    
    // Get table structure
    echo "Table Structure:\n";
    echo "----------------\n";
    $columns = DB::select("SHOW COLUMNS FROM document_upload");
    foreach ($columns as $column) {
        echo "- {$column->Field}: {$column->Type}" . 
             ($column->Null === "NO" ? " (Required)" : " (Nullable)") . 
             ($column->Default ? " Default: {$column->Default}" : "") . 
             ($column->Key ? " Key: {$column->Key}" : "") .
             "\n";
    }
    
    // Get table data count
    $count = DB::table('document_upload')->count();
    echo "\nTotal records: {$count}\n\n";
    
    // Get unique applicants
    $applicantCount = DB::table('document_upload')
        ->distinct()
        ->count('applicant_email');
    echo "Unique applicants: {$applicantCount}\n\n";
    
    // Check document_upload table engine and collation
    $tableStatus = DB::select("SHOW TABLE STATUS LIKE 'document_upload'")[0];
    echo "Table Engine: {$tableStatus->Engine}\n";
    echo "Table Collation: {$tableStatus->Collation}\n\n";
    
    // Verify foreign key constraint
    $foreignKeys = DB::select("
        SELECT *
        FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
        WHERE CONSTRAINT_TYPE = 'FOREIGN KEY' 
        AND TABLE_NAME = 'document_upload'
    ");
    
    if (count($foreignKeys) > 0) {
        echo "✅ Foreign key constraint exists for document_upload table.\n\n";
    } else {
        echo "⚠️ Warning: No foreign key constraints found for document_upload table.\n\n";
    }
    
} else {
    echo "❌ document_upload table does not exist!\n\n";
}

echo "=================================================\n";
echo "Verification complete.\n";
echo "=================================================\n";
