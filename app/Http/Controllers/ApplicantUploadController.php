<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApplicantUpload;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ApplicantUploadController extends Controller
{
    // Display the upload form
    public function create()
    {
        return view('upload');
    }

    // Process the form submission
    public function store(Request $request)
    {
        // Get current user's email
        $applicantEmail = Auth::guard('applicant')->user()->email;
        $request->merge(['applicant_email' => $applicantEmail]);
        
        try {
            // Validate the request according to the new structure
            $validatedData = $request->validate([
                'applicant_email' => 'required|email',
                'docfilename'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
                'doc_type'        => 'required|in:form138,good_moral,birth_certificate,id_picture,medical_clearance,application_form',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $e->errors(),
            ], 422);
        }

        // Create a folder name using a slug of the email
        $folder = 'uploads/' . Str::slug($validatedData['applicant_email']);

        // Store the file using its original name in the designated folder
        $docfilenamePath = $request->file('docfilename')->storeAs(
            $folder, 
            $request->file('docfilename')->getClientOriginalName(), 
            'public'
        );

        // Check if a document of this type already exists for this applicant
        $existing = ApplicantUpload::where('applicant_email', $validatedData['applicant_email'])
            ->where('doc_type', $validatedData['doc_type'])
            ->orderByDesc('id')
            ->first();

        if ($existing) {
            if ($existing->doc_status === 'declined') {
                // Delete the old file from storage if it exists
                if ($existing->docfilename && \Storage::disk('public')->exists($existing->docfilename)) {
                    \Storage::disk('public')->delete($existing->docfilename);
                }
                // Update the existing record: reset status, clear remarks, update file
                $existing->docfilename = $docfilenamePath;
                $existing->doc_status = 'pending';
                $existing->remarks = null;
                $existing->save();
                $applicantUpload = $existing;
            } else {
                // Prevent re-upload if already pending or approved
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot re-upload this document unless it was rejected.',
                ], 409);
            }
        } else {
            // Create a new record in the database
            $applicantUpload = ApplicantUpload::create([
                'applicant_email' => $validatedData['applicant_email'],
                'doc_type'        => $validatedData['doc_type'],
                'docfilename'     => $docfilenamePath,
                // doc_status defaults to 'pending'
            ]);
        }

        // Return a JSON response for AJAX requests
        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully!',
            'data'    => $applicantUpload,
        ]);
    }

    // Display the applicant dashboard
    public function dashboard()
    {
        $user = Auth::guard('applicant')->user();
        $uploads = ApplicantUpload::where('applicant_email', $user->email)->get();
        // Group by doc_type for easy lookup in view
        $uploadsByType = $uploads->keyBy('doc_type');
        return view('applicant.dashboard', compact('uploadsByType'));
    }
}