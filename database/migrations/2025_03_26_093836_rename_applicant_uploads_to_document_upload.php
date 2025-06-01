<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */    public function up(): void
    {
        // No longer needed - we're using a different approach
        // Schema::rename('applicant_uploads', 'document_upload');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No longer needed - we're using a different approach
        // Schema::rename('document_upload', 'applicant_uploads');
    }
};
