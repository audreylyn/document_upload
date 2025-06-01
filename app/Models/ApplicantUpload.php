<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicantUpload extends Model
{
    use HasFactory;

    protected $table = 'document_upload'; // Make sure this matches your actual table name
    
    protected $fillable = [
        'applicant_email',
        'doc_type',
        'docfilename',
        'doc_status',
        'remarks',
        'uploaded_by'
    ];

    // Ensure files are stored in public disk
    protected $disk = 'public';

    // Get the full URL for the document
    public function getDocumentUrl()
    {
        return asset('storage/' . $this->docfilename);
    }
}