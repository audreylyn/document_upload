<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migration disabled - using setup_document_upload.php instead
        // This avoids migration conflicts with the document_upload table
        /*
        Schema::table('document_upload', function (Blueprint $table) {
            //
        });
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Migration disabled - using setup_document_upload.php instead
        // This avoids migration conflicts with the document_upload table
        /*
        Schema::table('document_upload', function (Blueprint $table) {
            //
        });
        */
    }
};
