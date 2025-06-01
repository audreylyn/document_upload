<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DocumentUploadSeeder extends Seeder
{    public function run()
    {
        // Drop the table if it exists
        if (Schema::hasTable('document_upload')) {
            Schema::dropIfExists('document_upload');
        }
        
        // Create the table with Schema builder
        Schema::create('document_upload', function ($table) {
            $table->id();
            $table->string('applicant_email');
            $table->enum('doc_type', ['form138', 'good_moral', 'birth_certificate', 'id_picture', 'medical_clearance', 'application_form']);
            $table->enum('doc_status', ['pending', 'approved', 'declined'])->default('pending');
            $table->text('remarks')->nullable();
            $table->string('docfilename');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();
            
            // Define foreign key constraint
            // Note: Only add this if the users table exists
            if (Schema::hasTable('users')) {
                $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');
            }
        });
        
        // Insert sample data
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

        // Insert data into the document_upload table
        foreach ($data as $record) {
            DB::table('document_upload')->insert($record);
        }
    }
}
