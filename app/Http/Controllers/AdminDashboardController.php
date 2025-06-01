<?php

namespace App\Http\Controllers;

use App\Models\ApplicantUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard with application statistics.
     */
    public function index()
    {
        // Retrieve statistics based on document statuses
        try {
            $stats = DB::table('document_upload')
                ->select(
                    DB::raw('COUNT(DISTINCT applicant_email) as total'),
                    DB::raw('COUNT(DISTINCT CASE WHEN doc_status = "pending" THEN applicant_email END) as pending'),
                    DB::raw('COUNT(DISTINCT CASE WHEN doc_status = "approved" THEN applicant_email END) as approved'),
                    DB::raw('COUNT(DISTINCT CASE WHEN doc_status = "declined" OR doc_status = "rejected" THEN applicant_email END) as declined')
                )
                ->first();
        } catch (\Exception $e) {
            // Fallback to zeros if an error occurs
            $stats = (object)[
                'total'    => 0,
                'pending'  => 0,
                'approved' => 0,
                'declined' => 0,
            ];
        }

        // Get unique applications grouped by email with their latest upload time
        try {
            // First, get the unique emails and their latest upload dates
            $uniqueApplications = DB::table('document_upload')
                ->select('applicant_email', DB::raw('MAX(uploaded_at) as uploaded_at'))
                ->groupBy('applicant_email')
                ->orderBy('uploaded_at', 'desc')
                ->get();
                
            // Then fetch the complete documents for each email
            $applications = collect();
            foreach ($uniqueApplications as $app) {
                $documents = DB::table('document_upload')
                    ->where('applicant_email', $app->applicant_email)
                    ->get();
                
                // Extract document types and statuses
                $doc_types = $documents->pluck('doc_type')->toArray();
                $statuses = $documents->pluck('doc_status')->toArray();
                
                // Calculate overall status
                $status = 'pending';
                if (in_array('declined', $statuses) || in_array('rejected', $statuses)) {
                    $status = 'declined';
                } elseif (!in_array('pending', $statuses) && count(array_filter($statuses, function($s) { return $s === 'approved'; })) === count($statuses)) {
                    $status = 'approved';
                }
                
                $applications->push((object)[
                    'id' => $app->applicant_email,
                    'applicant_email' => $app->applicant_email,
                    'uploaded_at' => $app->uploaded_at,
                    'status' => $status,
                    'doc_types' => array_unique($doc_types)
                ]);
            }
        } catch (\Exception $e) {
            // If there's an error, return an empty collection
            $applications = collect();
        }

        return view('admin.dashboard', compact('stats', 'applications'));
    }

    /**
     * Show the admin profile page.
     */
    public function profile()
    {
        // Add any data you need for the profile page
        return view('admin.profile');
    }

    /**
     * Show the admin documents page.
     */
    public function documents()
    {
        // Add any data you need for the documents page
        return view('admin.documents');
    }

    /**
     * Show the admin settings page.
     */
    public function settings()
    {
        // Add any data you need for the settings page
        return view('admin.settings');
    }

    /**
     * Show the admin help center page.
     */
    public function help()
    {
        // Add any data you need for the help page
        return view('admin.help');
    }

    /**
     * Return details of a specific application as JSON.
     */
    public function getApplication(Request $request, $email)
    {
        // Get all documents for this email
        $documents = DB::table('document_upload')
            ->where('applicant_email', $email)
            ->get();

        if ($documents->count() > 0) {
            // Count document statuses
            $documentStatuses = [
                'pending' => 0,
                'approved' => 0,
                'declined' => 0
            ];

            // Group documents by type
            $documentsByType = [];
            foreach ($documents as $doc) {
                $documentsByType[$doc->doc_type] = [
                    'path' => $doc->docfilename,
                    'status' => $doc->doc_status,
                    'notes' => $doc->remarks
                ];
                $documentStatuses[$doc->doc_status]++;
            }

            // Calculate overall status
            $status = 'pending';
            if ($documentStatuses['declined'] > 0) {
                $status = 'declined';
            } else if ($documentStatuses['pending'] === 0 && $documentStatuses['approved'] === count($documents)) {
                $status = 'approved';
            }

            return response()->json([
                'success' => true,
                'email' => $email,
                'uploaded_at' => $documents->max('uploaded_at'),
                'status' => $status,
                'document_counts' => $documentStatuses,
                'documents' => $documentsByType
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Application not found',
            ], 404);
        }
    }

    /**
     * Update document status and notes.
     */
    public function updateDocumentStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string', // Email
            'document' => 'required|string',
            'status' => 'required|in:pending,approved,declined',
            'notes' => 'nullable|string',
            'rejection_reason' => 'required_if:status,declined|nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request data',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            // Update document status
            $document = DB::table('document_upload')
                ->where('applicant_email', $request->id)
                ->where('doc_type', $request->document)
                ->first();

            if (!$document) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Document not found',
                ], 404);
            }

            // Update document status and remarks
            DB::table('document_upload')
                ->where('applicant_email', $request->id)
                ->where('doc_type', $request->document)
                ->update([
                    'doc_status' => $request->status,
                    'remarks' => $request->status === 'declined' 
                        ? ($request->rejection_reason . ($request->notes ? ": " . $request->notes : ""))
                        : $request->notes
                ]);

            // Get updated document statuses for this email
            $allDocuments = DB::table('document_upload')
                ->where('applicant_email', $request->id)
                ->get();

            // Calculate overall status
            $hasDeclined = $allDocuments->contains('doc_status', 'declined');
            $allApproved = $allDocuments->every(function($doc) {
                return $doc->doc_status === 'approved';
            });

            $overallStatus = 'pending';
            if ($hasDeclined) {
                $overallStatus = 'declined';
            } elseif ($allApproved) {
                $overallStatus = 'approved';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Document status updated successfully',
                'application_status' => $overallStatus
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating document status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating document status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update admin notes for an application.
     */
    public function saveNotes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'notes' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request data',
                'errors' => $validator->errors(),
            ], 422);
        }

        $id = $request->input('id');
        $notes = $request->input('notes');

        $application = ApplicantUpload::find($id);
        if ($application) {
            $application->admin_notes = $notes;
            $application->save();
            return response()->json([
                'success' => true,
                'message' => 'Notes saved successfully',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Application not found',
            ], 404);
        }
    }

    /**
     * Update the status (and optionally admin notes) of an application.
     */
    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'status' => 'required|in:pending,approved,declined',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request data',
                'errors' => $validator->errors(),
            ], 422);
        }

        $id = $request->input('id');
        $status = $request->input('status');
        $notes = $request->input('notes', '');

        $application = ApplicantUpload::find($id);
        if ($application) {
            $application->status = $status;
            $application->admin_notes = $notes;
            $application->save();

            return response()->json([
                'success' => true,
                'message' => 'Status and notes updated successfully',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Application not found',
            ], 404);
        }
    }

    /**
     * Securely download a file from storage.
     */    public function downloadFile(string $path, Request $request)
    {
        try {
            $path = "uploads/{$path}";

            // Check if file exists
            if (!Storage::disk('public')->exists($path)) {
                abort(404, 'File not found');
            }

            // Get the full path to the file
            $fullPath = Storage::disk('public')->path($path);

            // Verify the file exists in the filesystem
            if (!file_exists($fullPath)) {
                abort(404, 'File not found in filesystem');
            }

            // Get the filename from the path
            $filename = basename($path);

            // Get MIME type
            $mimeType = mime_content_type($fullPath);
            
            // Set disposition based on the file type
            $fileExtension = pathinfo($path, PATHINFO_EXTENSION);
            
            // Determine if we should display inline (for preview) or as attachment (for download)
            // Check for a toolbar parameter which indicates preview mode
            $disposition = $request->has('toolbar') ? 'inline' : 'attachment';
            
            // Set appropriate headers
            $headers = [
                'Content-Type' => $mimeType,
                'Content-Disposition' => $disposition . '; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ];

            if ($disposition === 'inline') {
                // For inline display/preview
                return response()->file($fullPath, $headers);
            } else {
                // For download
                return response()->download($fullPath, $filename, $headers);
            }
        } catch (\Exception $e) {
            \Log::error('File download error: ' . $e->getMessage());
            abort(404, 'Error accessing file');
        }
    }
}
