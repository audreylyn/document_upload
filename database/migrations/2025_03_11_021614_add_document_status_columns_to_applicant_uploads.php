<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */    public function up(): void
    {
        // Skip this migration as we're using a different structure with the document_upload table
        if (Schema::hasTable('applicant_uploads')) {
            Schema::table('applicant_uploads', function (Blueprint $table) {
                // Add status and notes columns for each document
                $documents = [
                    'form138',
                    'good_moral',
                    'birth_certificate',
                    'id_picture',
                    'medical_clearance',
                    'application_form'
                ];
                
                foreach ($documents as $doc) {
                    if (!Schema::hasColumn('applicant_uploads', $doc . '_status')) {
                        $table->string($doc . '_status')->default('pending')->after($doc);
                    }
                    if (!Schema::hasColumn('applicant_uploads', $doc . '_notes')) {
                        $table->text($doc . '_notes')->nullable()->after($doc . '_status');
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicant_uploads', function (Blueprint $table) {
            $documents = [
                'form138',
                'good_moral',
                'birth_certificate',
                'id_picture',
                'medical_clearance',
                'application_form'
            ];

            foreach ($documents as $doc) {
                $table->dropColumn($doc . '_status');
                $table->dropColumn($doc . '_notes');
            }
        });
    }
};
