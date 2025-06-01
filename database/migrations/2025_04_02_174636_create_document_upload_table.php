<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentUploadTable extends Migration
{
    public function up()
    {
        // Check if the table doesn't exist before creating
        if (!Schema::hasTable('document_upload')) {
            Schema::create('document_upload', function (Blueprint $table) {
                $table->id();
                $table->string('applicant_email');
                $table->enum('doc_type', ['form138', 'good_moral', 'birth_certificate', 'id_picture', 'medical_clearance', 'application_form']);
                $table->enum('doc_status', ['pending', 'approved', 'declined'])->default('pending');
                $table->text('remarks')->nullable();
                $table->string('docfilename');
                $table->timestamp('uploaded_at')->useCurrent();
                $table->unsignedBigInteger('uploaded_by')->nullable();
                $table->timestamps();

                // Define foreign key constraint only for uploaded_by if needed
                $table->foreign('uploaded_by')->references('id')->on('users');
            });
        } else {
            // If the table exists, ensure it has the correct structure
            Schema::table('document_upload', function (Blueprint $table) {
                if (!Schema::hasColumn('document_upload', 'doc_status')) {
                    $table->enum('doc_status', ['pending', 'approved', 'declined'])->default('pending');
                }
                if (!Schema::hasColumn('document_upload', 'remarks')) {
                    $table->text('remarks')->nullable();
                }
                if (!Schema::hasColumn('document_upload', 'docfilename')) {
                    $table->string('docfilename');
                }
                if (!Schema::hasColumn('document_upload', 'uploaded_at')) {
                    $table->timestamp('uploaded_at')->useCurrent();
                }
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('document_upload');
    }
}
