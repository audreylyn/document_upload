<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixDocumentUploadTable extends Migration
{
    /**
     * Run the migration.
     */
    public function up()
    {
        // First, check if the table exists
        if (Schema::hasTable('document_upload')) {
            // Check and fix column types and enums
            $this->fixColumnTypes();
        } else {
            // Create the table from scratch if it doesn't exist
            $this->createTableFromScratch();
        }
    }

    /**
     * Reverse the migration.
     */
    public function down()
    {
        // This is a fixing migration, so down() doesn't need to do anything
    }

    /**
     * Create the table from scratch with the correct structure
     */
    protected function createTableFromScratch()
    {
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

            // Add foreign key constraint
            $table->foreign('uploaded_by')->references('id')->on('users');
        });
    }

    /**
     * Fix column types and enums in the existing table
     */
    protected function fixColumnTypes()
    {
        // Get the current columns
        $docTypeEnum = $this->getEnumValues('doc_type');
        $docStatusEnum = $this->getEnumValues('doc_status');

        // Check if doc_type enum needs fixing
        if ($docTypeEnum != ['form138', 'good_moral', 'birth_certificate', 'id_picture', 'medical_clearance', 'application_form']) {
            $this->fixEnumColumn('doc_type', ['form138', 'good_moral', 'birth_certificate', 'id_picture', 'medical_clearance', 'application_form']);
        }

        // Check if doc_status enum needs fixing
        if ($docStatusEnum != ['pending', 'approved', 'declined']) {
            $this->fixEnumColumn('doc_status', ['pending', 'approved', 'declined']);
        }

        // Make sure all other columns exist
        Schema::table('document_upload', function (Blueprint $table) {
            if (!Schema::hasColumn('document_upload', 'applicant_email')) {
                $table->string('applicant_email');
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
            if (!Schema::hasColumn('document_upload', 'uploaded_by')) {
                $table->unsignedBigInteger('uploaded_by')->nullable();
                $table->foreign('uploaded_by')->references('id')->on('users');
            }
        });
    }

    /**
     * Get the current enum values for a column
     */
    protected function getEnumValues($column)
    {
        $type = \DB::select("SHOW COLUMNS FROM document_upload WHERE Field = '{$column}'")[0]->Type;
        preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
        if (isset($matches[1])) {
            return explode("','", $matches[1]);
        }
        return [];
    }

    /**
     * Fix an enum column by modifying its type
     */
    protected function fixEnumColumn($column, $values)
    {
        $valuesString = "'" . implode("','", $values) . "'";
        \DB::statement("ALTER TABLE document_upload MODIFY COLUMN {$column} ENUM({$valuesString})");
        
        // If the column is doc_status, set the default value
        if ($column === 'doc_status') {
            \DB::statement("ALTER TABLE document_upload MODIFY COLUMN {$column} ENUM({$valuesString}) NOT NULL DEFAULT 'pending'");
        }
    }
}
