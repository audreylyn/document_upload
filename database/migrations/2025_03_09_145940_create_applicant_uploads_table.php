<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */    public function up()
    {
        // This table is no longer needed as we're using document_upload table instead
        // The table structure is kept here for reference but not created
        if (!Schema::hasTable('applicant_uploads')) {
            /*
            Schema::create('applicant_uploads', function (Blueprint $table) {
                $table->increments('id');
                $table->string('applicant_email');
                $table->string('form138')->nullable();
                $table->string('good_moral')->nullable();
                $table->string('birth_certificate')->nullable();
                $table->string('id_picture')->nullable();
                $table->string('medical_clearance')->nullable();
                $table->string('application_form')->nullable();
                $table->enum('status', ['pending', 'approved', 'declined'])->default('pending');
                $table->text('admin_notes')->nullable();
                $table->timestamp('uploaded_at')->useCurrent();
                $table->timestamps(); // Adds created_at and updated_at columns
            });
            */
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_uploads');
    }
};
