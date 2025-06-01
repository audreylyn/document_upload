{{-- resources/views/admin/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Document management" />

    <!-- CSRF Token & Global JS Variables -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.adminEndpoints = {
            getApplication: "{{ url('admin/application') }}", // GET endpoint, expects /admin/application/{id}
            updateStatus: "{{ url('admin/application/update-status') }}", // POST endpoint
            saveNotes: "{{ url('admin/application/save-notes') }}", // POST endpoint
            updateDocumentStatus: "{{ url('admin/application/document-status') }}" // POST endpoint for document status updates
        };
        window.assetPath = "{{ asset('storage/uploads') }}"; // Base URL for uploaded files
        console.log('Endpoints:', window.adminEndpoints);
        console.log('Asset Path:', window.assetPath);
    </script>

    <!-- ===== FONT AWESOME ===== -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- ===== DATA TABLES & BOOTSTRAP ===== -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">

    <!-- ===== CSS ===== -->
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/upload.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}" />

    <title>Admin Dashboard</title>

    <!-- ===== SCRIPTS ===== -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
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
                <span>Hello, {{ explode(' ', Auth::guard('admin')->user()->name)[0] }}</span>
            </div>
        </div>
    </header>

    <aside class="side-menu" id="nav-bar">
        <nav class="navigation">
            <div>
                <a href="{{ route('admin.dashboard') }}" class="brand-logo">
                    <i class="fa-solid fa-graduation-cap logo-icon"></i>
                    <span class="logo-text">BIT Academy</span>
                </a>
                <div class="menu-container">
                    <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'menu-active' : '' }}">
                        <i class="fa-solid fa-chart-line menu-icon"></i>
                        <span class="menu-name">Dashboard</span>
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.profile') ? 'menu-active' : '' }}">
                        <i class="fa-solid fa-user menu-icon"></i>
                        <span class="menu-name">Profile</span>
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.documents') ? 'menu-active' : '' }}">
                        <i class="fa-solid fa-file-lines menu-icon"></i>
                        <span class="menu-name">Documents</span>
                    </a>
                    <a href="{{ route('admin.settings') }}" class="menu-item {{ request()->routeIs('admin.settings') ? 'menu-active' : '' }}">
                        <i class="fa-solid fa-gear menu-icon"></i>
                        <span class="menu-name">Settings</span>
                    </a>
                    <a href="{{ route('admin.help') }}" class="menu-item {{ request()->routeIs('admin.help') ? 'menu-active' : '' }}">
                        <i class="fa-solid fa-circle-question menu-icon"></i>
                        <span class="menu-name">Help Center</span>
                    </a>
                </div>
            </div>
            <a href="#" class="menu-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa-solid fa-right-from-bracket menu-icon"></i>
                <span class="menu-name">Sign Out</span>
            </a>
            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </nav>
    </aside>

    <main class="main-content">
        <div class="container">
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon total">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $stats->total }}</h3>
                        <p>Total Applications</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon pending">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $stats->pending }}</h3>
                        <p>Pending</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon approved">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $stats->approved }}</h3>
                        <p>Approved</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon declined">
                        <i class="fas fa-times"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $stats->declined }}</h3>
                        <p>Declined</p>
                    </div>
                </div>
            </div>

            <div class="filter-section">
                <div class="filter-tabs">
                    <button class="filter-tab active" onclick="filterApplications('all')">All Applications</button>
                    <button class="filter-tab" onclick="filterApplications('pending')">Pending</button>
                    <button class="filter-tab" onclick="filterApplications('approved')">Approved</button>
                    <button class="filter-tab" onclick="filterApplications('declined')">Declined</button>
                </div>
                <div class="filter-dropdown">
                    <button class="filter-btn">
                        <i class="fas fa-filter"></i>
                        Filter
                    </button>
                    <select onchange="filterApplications(this.value)">
                        <option value="all">All Applications</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="declined">Declined</option>
                    </select>
                </div>
            </div>

            @if(isset($error) && $error)
            <div class="error">{{ $error }}</div>
            @endif

            <table id="applicationsTable">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Documents</th>
                        <th>Status</th>
                        <th>Uploaded At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($applications as $app)
                    <tr>
                        <td data-label="Email">{{ $app->applicant_email }}</td>
                        <td data-label="Documents">
                            @foreach($app->doc_types as $type)
                                <span class="document-tag">{{ ucwords(str_replace('_', ' ', $type)) }}</span>
                            @endforeach
                        </td>
                        <td data-label="Status">
                            <span class="status status-{{ $app->status }}">{{ ucfirst($app->status) }}</span>
                        </td>
                        <td data-label="Uploaded At">{{ date('M d, Y H:i', strtotime($app->uploaded_at)) }}</td>
                        <td data-label="Actions">
                            <button class="action-btn view-btn" onclick="viewApplication('{{ $app->applicant_email }}')">View</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Application Details Modal -->
        <div id="applicationModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Applicant Details</h2>
                    <span class="close">&times;</span>
                </div>
                <div id="applicationDetails"></div>
            </div>
        </div>
    </main>

    <!--===== MAIN JS =====-->
    <script src="{{ asset('assets/js/admin.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
</body>

</html>