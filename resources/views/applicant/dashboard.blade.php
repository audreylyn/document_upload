<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Document management" />
    <!-- ===== FONT AWESOME ===== -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" /> <!-- ===== CSS ===== -->
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/upload.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/status-badges.css') }}" />
    <title>Applicant Dashboard</title>
</head>

<body class="main-body">
    <header class="top-bar" id="header">
        <div class="menu-trigger">
            <label class="burger-button">
                <input type="checkbox" class="menu-checkbox" aria-label="Toggle navigation menu" />
                <svg viewBox="0 0 32 32">
                    <path class="burger-line burger-line-animate" d="M27 10 13 10C10.8 10 9 8.2 9 6 9 3.5 10.8 2 13 2 15.2 2 17 3.8 17 6L17 26C17 28.2 18.8 30 21 30 23.2 30 25 28.2 25 26 25 23.8 23.2 22 21 22L7 22"></path>
                    <path class="burger-line" d="M7 16 27 16"></path>
                </svg>
            </label>
        </div>
        <div class="user-controls">
            <div class="profile-thumb">
                <img src="{{ asset('assets/img/avatar.webp') }}" alt="Profile picture" />
            </div>
            <div class="user-greeting">
                <span>Hello, {{ explode(' ', Auth::guard('applicant')->user()->name)[0] }}</span>
            </div>
        </div>
    </header>

    <aside class="side-menu" id="nav-bar">
        <nav class="navigation">
            <div>
                <a href="#" class="brand-logo">
                    <i class="fa-solid fa-graduation-cap logo-icon"></i>
                    <span class="logo-text">BIT Academy</span>
                </a>
                <div class="menu-container">
                    <a href="#" class="menu-item">
                        <i class="fa-solid fa-chart-line menu-icon"></i>
                        <span class="menu-name">Dashboard</span>
                    </a> <a href="#" class="menu-item {{ request()->routeIs('applicant.profile') ? 'menu-active' : '' }}">
                        <i class="fa-solid fa-user menu-icon"></i>
                        <span class="menu-name">Profile</span>
                    </a>
                    <a href="#" class="menu-item menu-active">
                        <i class="fa-solid fa-file-lines menu-icon"></i>
                        <span class="menu-name">Documents</span>
                    </a>
                    <a href="#" class="menu-item">
                        <i class="fa-solid fa-gear menu-icon"></i>
                        <span class="menu-name">Settings</span>
                    </a>
                    <a href="#" class="menu-item">
                        <i class="fa-solid fa-circle-question menu-icon"></i>
                        <span class="menu-name">Help Center</span>
                    </a>
                </div>
            </div> <a href="#" class="menu-item" onclick="event.preventDefault(); document.getElementById('applicant-logout-form').submit();">
                <i class="fa-solid fa-right-from-bracket menu-icon"></i>
                <span class="menu-name">Sign Out</span>
            </a>
            <form id="applicant-logout-form" action="{{ route('applicant.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </nav>
    </aside>

    <main class="main-content">
        <div class="container-forms">
            <div class="section-header">
                <div class="header-content">
                    <div class="title-container">
                        <div class="title-wrapper">
                            <h1 class="section-title">{{ Auth::guard('applicant')->user()->name }}</h1>
                            <p class="section-subtitle">Your Documents</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Begin Form -->
            <form id="upload-form" action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <!-- Add CSRF token meta tag for AJAX requests -->
                <meta name="csrf-token" content="{{ csrf_token() }}">
                <input type="hidden" id="applicant_email" name="applicant_email" value="{{ Auth::guard('applicant')->user()->email }}" />

                <div class="cards-container">
                    <!-- Card for ID Picture -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-header-content">
                                <div class="status-badges">
                                    <h3 class="card-title">ID Picture</h3>
                                    @if(isset($uploadsByType['id_picture']))
                                    <div class="upload-status">
                                        @if($uploadsByType['id_picture']->doc_status === 'approved')
                                        <span class="status-badge approved">Approved</span>
                                        @elseif($uploadsByType['id_picture']->doc_status === 'declined')
                                        <span class="status-badge declined">Rejected</span>
                                        @else
                                        <span class="status-badge pending">Pending</span>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                                <p class="card-subtitle">Recent photo taken within 3 months.</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <label for="id_picture" class="sr-only">Upload ID Picture</label>
                            <input type="file" id="id_picture" name="id_picture" accept=".jpg,.jpeg,.png,.pdf" class="hidden" title="Upload ID Picture">
                            @unless(isset($uploadsByType['id_picture']))
                            <button type="button" class="upload-button" onclick="document.getElementById('id_picture').click()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                Click to upload
                            </button>
                            <p class="file-info">jpg, jpeg, png, or pdf files (max 4MB)</p>
                            @endunless
                            @if(isset($uploadsByType['id_picture']))
                            @if($uploadsByType['id_picture']->doc_status === 'approved')

                            <div class="upload-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                                <span>{{ $uploadsByType['id_picture']->file_name }}</span>
                                <p class="file-info">jpg, jpeg, png, or pdf files (max 4MB)</p>
                            </div>
                            @elseif($uploadsByType['id_picture']->doc_status === 'pending')
                            <div class="upload-success pending">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10zM12 6v6l4 2"></path>
                                </svg>
                                <span>{{ $uploadsByType['id_picture']->file_name }}</span>
                                <p class="file-info">jpg, jpeg, png, or pdf files (max 4MB)</p>
                            </div>
                            @elseif($uploadsByType['id_picture']->doc_status === 'declined')
                            <button type="button" class="upload-button reupload declined">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                Resubmit document
                            </button>
                            @if($uploadsByType['id_picture']->remarks)
                            <div class="rejection-details">
                                <p class="rejection-details-title">Rejection Details</p>
                                <p class="rejection-details-text">{{ $uploadsByType['id_picture']->remarks }}</p>
                            </div>
                            @endif
                            @endif
                            @endif
                        </div>
                    </div>

                    <!-- Card for Form 138 -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-header-content">
                                <div class="status-badges">
                                    <h3 class="card-title">Form 138</h3>
                                    @if(isset($uploadsByType['form138']))
                                    <div class="upload-status">
                                        @if($uploadsByType['form138']->doc_status === 'approved')
                                        <span class="status-badge approved">Approved</span>
                                        @elseif($uploadsByType['form138']->doc_status === 'declined')
                                        <span class="status-badge declined">Rejected</span>
                                        @else
                                        <span class="status-badge pending">Pending</span>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                                <p class="card-subtitle">Grade 12 reflecting 2nd grading period</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <label for="form138" class="sr-only">Upload Form 138</label>
                            <input type="file" id="form138" name="form138" accept=".jpg,.jpeg,.png,.pdf" class="hidden" title="Upload Form 138">
                            @unless(isset($uploadsByType['form138']))
                            <button type="button" class="upload-button" onclick="document.getElementById('form138').click()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                Click to upload
                            </button>
                            <p class="file-info">jpg, jpeg, png, or pdf files (max 4MB)</p>
                            @endunless
                            @if(isset($uploadsByType['form138']))
                            @if($uploadsByType['form138']->doc_status === 'approved')
                            <div class="upload-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                                <span>{{ $uploadsByType['form138']->file_name }}</span>
                                <p class="file-info">jpg, jpeg, png, or pdf files (max 4MB)</p>
                            </div>
                            @elseif($uploadsByType['form138']->doc_status === 'pending')
                            <div class="upload-success pending">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10zM12 6v6l4 2"></path>
                                </svg>
                                <span>{{ $uploadsByType['form138']->file_name }}</span>
                                <p class="file-info">jpg, jpeg, png, or pdf files (max 4MB)</p>
                            </div>
                            @elseif($uploadsByType['form138']->doc_status === 'declined')
                            <button type="button" class="upload-button reupload declined">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                Resubmit document
                            </button>
                            @if($uploadsByType['form138']->remarks)
                            <div class="rejection-details">
                                <p class="rejection-details-title">Rejection Details</p>
                                <p class="rejection-details-text">{{ $uploadsByType['form138']->remarks }}</p>
                            </div>
                            @endif
                            @endif
                            @endif
                        </div>
                    </div>

                    <!-- Card for Good Moral Certificate -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-header-content">
                                <div class="status-badges">
                                    <h3 class="card-title">Good Moral Certificate</h3>
                                    @if(isset($uploadsByType['good_moral']))
                                    <div class="upload-status">
                                        @if($uploadsByType['good_moral']->doc_status === 'approved')
                                        <span class="status-badge approved">Approved</span>
                                        @elseif($uploadsByType['good_moral']->doc_status === 'declined')
                                        <span class="status-badge declined">Rejected</span>
                                        @else
                                        <span class="status-badge pending">Pending</span>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                                <p class="card-subtitle">Certificate of Good Moral Character</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <label for="good_moral" class="sr-only">Upload Good Moral Certificate</label>
                            <input type="file" id="good_moral" name="good_moral" accept=".jpg,.jpeg,.png,.pdf" class="hidden" title="Upload Good Moral Certificate">
                            @unless(isset($uploadsByType['good_moral']))
                            <button type="button" class="upload-button" onclick="document.getElementById('good_moral').click()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                Click to upload
                            </button>
                            <p class="file-info">jpg, jpeg, png, or pdf files (max 4MB)</p>
                            @endunless
                            @if(isset($uploadsByType['good_moral']))
                            @if($uploadsByType['good_moral']->doc_status === 'approved')
                            <div class="upload-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                                <span>{{ $uploadsByType['good_moral']->file_name }}</span>
                                <p class="file-info">jpg, jpeg, png, or pdf files (max 4MB)</p>
                            </div>
                            @elseif($uploadsByType['good_moral']->doc_status === 'pending')
                            <div class="upload-success pending">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10zM12 6v6l4 2"></path>
                                </svg>
                                <span>{{ $uploadsByType['good_moral']->file_name }}</span>
                                <p class="file-info">jpg, jpeg, png, or pdf files (max 4MB)</p>
                            </div>
                            @elseif($uploadsByType['good_moral']->doc_status === 'declined')
                            <button type="button" class="upload-button reupload declined">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                Resubmit document
                            </button>
                            @if($uploadsByType['good_moral']->remarks)
                            <div class="rejection-details">
                                <p class="rejection-details-title">Rejection Details</p>
                                <p class="rejection-details-text">{{ $uploadsByType['good_moral']->remarks }}</p>
                            </div>
                            @endif
                            @endif
                            @endif
                        </div>
                    </div>

                    <!-- Card for Birth Certificate -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-header-content">
                                <div class="status-badges">
                                    <h3 class="card-title">Birth Certificate</h3>
                                    @if(isset($uploadsByType['birth_certificate']))
                                    <div class="upload-status">
                                        @if($uploadsByType['birth_certificate']->doc_status === 'approved')
                                        <span class="status-badge approved">Approved</span>
                                        @elseif($uploadsByType['birth_certificate']->doc_status === 'declined')
                                        <span class="status-badge declined">Rejected</span>
                                        @else
                                        <span class="status-badge pending">Pending</span>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                                <p class="card-subtitle">PSA Birth Certificate</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <label for="birth_certificate" class="sr-only">Upload Birth Certificate</label>
                            <input type="file" id="birth_certificate" name="birth_certificate" accept=".jpg,.jpeg,.png,.pdf" class="hidden" title="Upload Birth Certificate">
                            @unless(isset($uploadsByType['birth_certificate']))
                            <button type="button" class="upload-button" onclick="document.getElementById('birth_certificate').click()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                Click to upload
                            </button>
                            <p class="file-info">jpg, jpeg, png, or pdf files (max 4MB)</p>
                            @endunless
                            @if(isset($uploadsByType['birth_certificate']))
                            @if($uploadsByType['birth_certificate']->doc_status === 'approved')
                            <div class="upload-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                                <span>{{ $uploadsByType['birth_certificate']->file_name }}</span>
                                <p class="file-info">jpg, jpeg, png, or pdf files (max 4MB)</p>
                            </div>
                            @elseif($uploadsByType['birth_certificate']->doc_status === 'pending')
                            <div class="upload-success pending">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10zM12 6v6l4 2"></path>
                                </svg>
                                <span>{{ $uploadsByType['birth_certificate']->file_name }}</span>
                                <p class="file-info">jpg, jpeg, png, or pdf files (max 4MB)</p>
                            </div>
                            @elseif($uploadsByType['birth_certificate']->doc_status === 'declined')
                            <button type="button" class="upload-button reupload declined">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                Resubmit document
                            </button>
                            @if($uploadsByType['birth_certificate']->remarks)
                            <div class="rejection-details">
                                <p class="rejection-details-title">Rejection Details</p>
                                <p class="rejection-details-text">{{ $uploadsByType['birth_certificate']->remarks }}</p>
                            </div>
                            @endif
                            @endif
                            @endif
                        </div>
                    </div>

                    <!-- Card for Medical Clearance -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-header-content">
                                <div class="status-badges">
                                    <h3 class="card-title">Medical Clearance</h3>
                                    @if(isset($uploadsByType['medical_clearance']))
                                    <div class="upload-status">
                                        @if($uploadsByType['medical_clearance']->doc_status === 'approved')
                                        <span class="status-badge approved">Approved</span>
                                        @elseif($uploadsByType['medical_clearance']->doc_status === 'declined')
                                        <span class="status-badge declined">Rejected</span>
                                        @else
                                        <span class="status-badge pending">Pending</span>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                                <p class="card-subtitle">Medical Certificate from authorized physician</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <label for="medical_clearance" class="sr-only">Upload Medical Clearance</label>
                            <input type="file" id="medical_clearance" name="medical_clearance" accept=".jpg,.jpeg,.png,.pdf" class="hidden" title="Upload Medical Clearance">
                            @unless(isset($uploadsByType['medical_clearance']))
                            <button type="button" class="upload-button" onclick="document.getElementById('medical_clearance').click()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                Click to upload
                            </button>
                            <p class="file-info">jpg, jpeg, png, or pdf files (max 4MB)</p>
                            @endunless
                            @if(isset($uploadsByType['medical_clearance']))
                            @if($uploadsByType['medical_clearance']->doc_status === 'approved')
                            <div class="upload-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                                <span>{{ $uploadsByType['medical_clearance']->file_name }}</span>
                                <p class="file-info">jpg, jpeg, png, or pdf files (max 4MB)</p>
                            </div>
                            @elseif($uploadsByType['medical_clearance']->doc_status === 'pending')
                            <div class="upload-success pending">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10zM12 6v6l4 2"></path>
                                </svg>
                                <span>{{ $uploadsByType['medical_clearance']->file_name }}</span>
                                <p class="file-info">jpg, jpeg, png, or pdf files (max 4MB)</p>
                            </div>
                            @elseif($uploadsByType['medical_clearance']->doc_status === 'declined')
                            <button type="button" class="upload-button reupload declined">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                Resubmit document
                            </button>
                            @if($uploadsByType['medical_clearance']->remarks)
                            <div class="rejection-details">
                                <p class="rejection-details-title">Rejection Details</p>
                                <p class="rejection-details-text">{{ $uploadsByType['medical_clearance']->remarks }}</p>
                            </div>
                            @endif
                            @endif
                            @endif
                        </div>
                    </div>

                    <!-- Card for Application Form -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-header-content">
                                <div class="status-badges">
                                    <h3 class="card-title">Application Form</h3>
                                    @if(isset($uploadsByType['application_form']))
                                    <div class="upload-status">
                                        @if($uploadsByType['application_form']->doc_status === 'approved')
                                        <span class="status-badge approved">Approved</span>
                                        @elseif($uploadsByType['application_form']->doc_status === 'declined')
                                        <span class="status-badge declined">Rejected</span>
                                        @else
                                        <span class="status-badge pending">Pending</span>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                                <p class="card-subtitle">Completed application form</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <label for="application_form" class="sr-only">Upload Application Form</label>
                            <input type="file" id="application_form" name="application_form" accept=".jpg,.jpeg,.png,.pdf" class="hidden" title="Upload Application Form">
                            @unless(isset($uploadsByType['application_form']))
                            <button type="button" class="upload-button" onclick="document.getElementById('application_form').click()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                Click to upload
                            </button>
                            <p class="file-info">jpg, jpeg, png, or pdf files (max 4MB)</p>
                            @endunless
                            @if(isset($uploadsByType['application_form']))
                            @if($uploadsByType['application_form']->doc_status === 'approved')
                            <div class="upload-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                                <span>{{ $uploadsByType['application_form']->file_name }}</span>
                                <p class="file-info">jpg, jpeg, png, or pdf files (max 4MB)</p>
                            </div>
                            @elseif($uploadsByType['application_form']->doc_status === 'pending')
                            <div class="upload-success pending">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10zM12 6v6l4 2"></path>
                                </svg>
                                <span>{{ $uploadsByType['application_form']->file_name }}</span>
                                <p class="file-info">jpg, jpeg, png, or pdf files (max 4MB)</p>
                            </div>
                            @elseif($uploadsByType['application_form']->doc_status === 'declined')
                            <button type="button" class="upload-button reupload declined">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                Resubmit document
                            </button>
                            @if($uploadsByType['application_form']->remarks)
                            <div class="rejection-details">
                                <p class="rejection-details-title">Rejection Details</p>
                                <p class="rejection-details-text">{{ $uploadsByType['application_form']->remarks }}</p>
                            </div>
                            @endif
                            @endif
                            @endif
                        </div>
                    </div>
                </div>
                <button type="submit" class="submit-button">
                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 2L11 13"></path>
                        <path d="M22 2L15 22L11 13L2 9L22 2Z"></path>
                    </svg>
                    Submit
                </button>
            </form>
            <div id="files-list"></div>
        </div>
    </main>
    <footer class="footer">
        <p class="footer-text">
            © 2024 BIT Academy. All rights reserved. | <a href="#" class="footer-link">Privacy Policy</a> | <a href="#" class="footer-link">Terms of Service</a>
        </p>
    </footer>
    <!--===== MAIN JS =====-->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/js/applicant.js') }}"></script>
</body>

</html>