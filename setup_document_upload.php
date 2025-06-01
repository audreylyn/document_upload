<?php

// Import the document_upload.sql file using Laravel's database connection
// This is a standalone PHP script that can be run from the command line

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Starting document_upload table setup...\n";

try {
    // First, drop the table if it exists
    if (Schema::hasTable('document_upload')) {
        Schema::dropIfExists('document_upload');
        echo "Dropped existing document_upload table\n";
    }    // Create the table with the EXACT structure as provided in document_upload.sql
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");    echo "Created document_upload table successfully\n";
    
    // Add primary key, index, and auto increment
    DB::statement("ALTER TABLE `document_upload` ADD PRIMARY KEY (`id`)");
    DB::statement("ALTER TABLE `document_upload` ADD KEY `document_upload_uploaded_by_foreign` (`uploaded_by`)");
    DB::statement("ALTER TABLE `document_upload` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13");
    
    // Add the foreign key constraint if users table exists
    try {
        if (Schema::hasTable('users')) {
            DB::statement("ALTER TABLE `document_upload` ADD CONSTRAINT `document_upload_uploaded_by_foreign` 
                           FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)");
            echo "Added foreign key constraint successfully\n";
        }
    } catch (Exception $e) {
        echo "Warning: Could not add foreign key constraint: " . $e->getMessage() . "\n";
    }
    
    // Sample data from the SQL file
    $data = [
        [
            'applicant_email' => 'qwer@gmail.com',
            'doc_type' => 'birth_certificate',
            'doc_status' => 'pending',
            'remarks' => null,
            'docfilename' => 'uploads/qwer-at-gmailcom/zoom pic 100.png',
            'uploaded_at' => '2025-04-02 19:02:08',
            'created_at' => '2025-04-02 11:02:08',
            'updated_at' => '2025-04-02 11:02:08'
        ],
        [
            'applicant_email' => 'qwer@gmail.com',
            'doc_type' => 'medical_clearance',
            'doc_status' => 'pending',
            'remarks' => null,
            'docfilename' => 'uploads/qwer-at-gmailcom/logo st berna.jpg',
            'uploaded_at' => '2025-04-02 19:02:08',
            'created_at' => '2025-04-02 11:02:08',
            'updated_at' => '2025-04-02 11:02:08'
        ],
        [
            'applicant_email' => 'qwer@gmail.com',
            'doc_type' => 'id_picture',
            'doc_status' => 'pending',
            'remarks' => null,
            'docfilename' => 'uploads/qwer-at-gmailcom/462558659_1615666179339202_5776994203367453167_n.jpg',
            'uploaded_at' => '2025-04-02 19:02:09',
            'created_at' => '2025-04-02 11:02:09',
            'updated_at' => '2025-04-02 11:02:09'
        ],
        [
            'applicant_email' => 'qwer@gmail.com',
            'doc_type' => 'form138',
            'doc_status' => 'pending',
            'remarks' => null,
            'docfilename' => 'uploads/qwer-at-gmailcom/default image.jpg',
            'uploaded_at' => '2025-04-02 19:02:09',
            'created_at' => '2025-04-02 11:02:09',
            'updated_at' => '2025-04-02 11:02:09'
        ],
        [
            'applicant_email' => 'qwer@gmail.com',
            'doc_type' => 'good_moral',
            'doc_status' => 'pending',
            'remarks' => null,
            'docfilename' => 'uploads/qwer-at-gmailcom/insert-face-police-39684.jpg',
            'uploaded_at' => '2025-04-02 19:02:09',
            'created_at' => '2025-04-02 11:02:09',
            'updated_at' => '2025-04-02 11:02:09'
        ],
        [
            'applicant_email' => 'qwer@gmail.com',
            'doc_type' => 'application_form',
            'doc_status' => 'pending',
            'remarks' => null,
            'docfilename' => 'uploads/qwer-at-gmailcom/ITFRAME.jpg',
            'uploaded_at' => '2025-04-02 19:02:10',
            'created_at' => '2025-04-02 11:02:10',
            'updated_at' => '2025-04-02 11:02:10'
        ],
        [
            'applicant_email' => 'drey2@gmail.com',
            'doc_type' => 'application_form',
            'doc_status' => 'pending',
            'remarks' => null,
            'docfilename' => 'uploads/drey2-at-gmailcom/sean white background.jpg',
            'uploaded_at' => '2025-04-03 06:06:07',
            'created_at' => '2025-04-02 22:06:07',
            'updated_at' => '2025-04-02 22:06:07'
        ],
        [
            'applicant_email' => 'drey2@gmail.com',
            'doc_type' => 'id_picture',
            'doc_status' => 'pending',
            'remarks' => null,
            'docfilename' => 'uploads/drey2-at-gmailcom/SIA-LEC-L7-9.pdf',
            'uploaded_at' => '2025-04-03 06:06:07',
            'created_at' => '2025-04-02 22:06:07',
            'updated_at' => '2025-04-02 22:06:07'
        ],
        [
            'applicant_email' => 'drey2@gmail.com',
            'doc_type' => 'form138',
            'doc_status' => 'pending',
            'remarks' => null,
            'docfilename' => 'uploads/drey2-at-gmailcom/WEEK3REPORT.pdf',
            'uploaded_at' => '2025-04-03 06:06:08',
            'created_at' => '2025-04-02 22:06:08',
            'updated_at' => '2025-04-02 22:06:08'
        ],
        [
            'applicant_email' => 'drey2@gmail.com',
            'doc_type' => 'birth_certificate',
            'doc_status' => 'pending',
            'remarks' => null,
            'docfilename' => 'uploads/drey2-at-gmailcom/462558659_1615666179339202_5776994203367453167_n.jpg',
            'uploaded_at' => '2025-04-03 06:06:08',
            'created_at' => '2025-04-02 22:06:08',
            'updated_at' => '2025-04-02 22:06:08'
        ],
        [
            'applicant_email' => 'drey2@gmail.com',
            'doc_type' => 'medical_clearance',
            'doc_status' => 'pending',
            'remarks' => null,
            'docfilename' => 'uploads/drey2-at-gmailcom/307849339_1122387448692487_8111979713351845381_n.jpg',
            'uploaded_at' => '2025-04-03 06:06:09',
            'created_at' => '2025-04-02 22:06:09',
            'updated_at' => '2025-04-02 22:06:09'
        ],
        [
            'applicant_email' => 'drey2@gmail.com',
            'doc_type' => 'good_moral',
            'doc_status' => 'pending',
            'remarks' => null,
            'docfilename' => 'uploads/drey2-at-gmailcom/105-Article Text-711-1-10-20200708-2.pdf',
            'uploaded_at' => '2025-04-03 06:06:09',
            'created_at' => '2025-04-02 22:06:09',
            'updated_at' => '2025-04-02 22:06:09'
        ],
    ];
    
    // Insert data
    foreach ($data as $record) {
        DB::table('document_upload')->insert($record);
    }
    
    echo "Inserted sample data successfully\n";
    echo "Document upload table setup completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error setting up document_upload table: " . $e->getMessage() . "\n";
}
