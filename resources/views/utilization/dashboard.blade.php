<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Pemanfaatan BMN</title>

    {{-- Custom Alert System --}}
    @include('includes.custom-alert')

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="{{ asset('js/terbilang.min.js') }}"></script>


    <style>
        :root {
            --bs-primary-rgb: 79, 70, 229;
            --bs-body-font-family: 'Inter', sans-serif;
            --primary-color: #4f46e5;
            --primary-light: #6366f1;
            --primary-dark: #4338ca;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--bs-body-font-family);
            min-height: 100vh;
            padding: 2rem 0;
            position: relative;
            overflow-x: hidden;
        }

        /* Background Image with Subtle Overlay */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ asset('storage/image/bg_pemanfaatan.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            z-index: -2;
        }

        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg,
                rgba(55, 65, 81, 0.75) 0%,
                rgba(75, 85, 99, 0.72) 25%,
                rgba(107, 114, 128, 0.70) 50%,
                rgba(75, 85, 99, 0.72) 75%,
                rgba(55, 65, 81, 0.75) 100%
            );
            backdrop-filter: blur(1px);
            z-index: -1;
        }

        .container-fluid {
            max-width: 1400px;
        }

        /* Header Section - Clean & Professional */
        .page-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px) saturate(180%);
            -webkit-backdrop-filter: blur(10px) saturate(180%);
            border-radius: 1.5rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow:
                0 4px 20px rgba(0, 0, 0, 0.08),
                0 0 0 1px rgba(79, 70, 229, 0.08);
            border: 1px solid rgba(79, 70, 229, 0.1);
            position: relative;
            z-index: 100;
        }

        .page-header h1 {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: var(--gray-600);
            font-size: 0.95rem;
        }

        .page-header .text-primary-dark {
            color: var(--primary-color) !important;
        }

        /* Modern KPI Cards - Clean White Design */
        .stat-card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(10px) saturate(180%);
            -webkit-backdrop-filter: blur(10px) saturate(180%);
            border-radius: 1.25rem;
            padding: 1.75rem;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow:
                0 4px 20px rgba(0, 0, 0, 0.06),
                0 0 0 1px rgba(0, 0, 0, 0.02);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            background: rgba(255, 255, 255, 1);
            box-shadow:
                0 12px 40px rgba(79, 70, 229, 0.15),
                0 0 0 1px rgba(79, 70, 229, 0.1);
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-icon-wrapper {
            width: 56px;
            height: 56px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover .stat-icon-wrapper {
            transform: scale(1.1) rotate(5deg);
        }

        .stat-card-primary .stat-icon-wrapper {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        }

        .stat-card-success .stat-icon-wrapper {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
        }

        .stat-card-warning .stat-icon-wrapper {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            box-shadow: 0 8px 16px rgba(245, 158, 11, 0.3);
        }

        .stat-card-info .stat-icon-wrapper {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
        }

        .stat-card-danger .stat-icon-wrapper {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 8px 16px rgba(239, 68, 68, 0.3);
        }

        .stat-value {
            font-size: 2.25rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--gray-900) 0%, var(--gray-700) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0.5rem 0;
            line-height: 1;
        }

        .stat-title {
            color: var(--gray-600);
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .stat-change {
            display: inline-flex;
            align-items: center;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.5rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }

        .stat-change.positive {
            color: var(--success-color);
            background-color: rgba(16, 185, 129, 0.1);
        }

        .stat-change.negative {
            color: var(--danger-color);
            background-color: rgba(239, 68, 68, 0.1);
        }

        /* Modern Table Design - Clean White */
        .table-container {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(10px) saturate(180%);
            -webkit-backdrop-filter: blur(10px) saturate(180%);
            border-radius: 1.5rem;
            padding: 2rem;
            box-shadow:
                0 4px 20px rgba(0, 0, 0, 0.08),
                0 0 0 1px rgba(79, 70, 229, 0.08);
            border: 1px solid rgba(79, 70, 229, 0.1);
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .table-header h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0;
        }

        .table-header h3 .text-primary-dark {
            color: var(--primary-color) !important;
        }

        /* Search Bar - Clean Design */
        .search-wrapper {
            position: relative;
            width: 100%;
            max-width: 320px;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 3rem;
            border: 2px solid var(--gray-200);
            border-radius: 0.75rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            background: white;
            color: var(--gray-900);
        }

        .search-input::placeholder {
            color: var(--gray-400);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 1.125rem;
        }

        .table {
            margin: 0;
        }

        .table thead th {
            border-top: none;
            border-bottom: 2px solid var(--gray-200);
            font-weight: 700;
            color: var(--gray-700);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 1rem 0.75rem;
            background: rgba(249, 250, 251, 0.8);
        }

        .table thead th:first-child {
            border-top-left-radius: 0.75rem;
        }

        .table thead th:last-child {
            border-top-right-radius: 0.75rem;
        }

        .table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--gray-100);
            color: var(--gray-700);
            font-size: 0.875rem;
        }

        .table tbody tr {
            transition: all 0.2s ease;
            background: rgba(255, 255, 255, 0.6);
        }

        .table tbody tr:hover {
            background-color: rgba(243, 244, 246, 0.8);
            transform: scale(1.001);
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Action Buttons */
        .action-buttons .btn {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            border-width: 1.5px;
        }

        .action-buttons .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.025em;
            transition: all 0.2s ease;
        }

        .status-lengkap {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 1.5px solid #6ee7b7;
        }

        .status-belum-lengkap {
            background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%);
            color: #9a3412;
            border: 1.5px solid #fb923c;
        }

        /* Row highlight based on completion status */
        tr.data-incomplete {
            background-color: rgba(254, 243, 199, 0.5) !important;
        }

        tr.data-incomplete:hover {
            background-color: rgba(254, 243, 199, 0.7) !important;
        }

        /* Toggle Switch */
        .form-check-input {
            width: 2.75rem;
            height: 1.5rem;
            cursor: pointer;
            background-color: var(--gray-300);
            border: none;
            transition: all 0.3s ease;
        }

        .form-check-input:checked {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }

        .form-check-label {
            cursor: pointer;
            font-size: 0.813rem;
            font-weight: 500;
            color: var(--gray-600);
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5568d3 0%, #63398e 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        }

        .add-more-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 0.75rem;
            padding: 0.875rem 1.75rem;
            font-weight: 600;
            font-size: 0.938rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .add-more-btn:hover {
            background: linear-gradient(135deg, #5568d3 0%, #63398e 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        /* Pagination */
        .pagination-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 2px solid var(--gray-100);
        }

        .pagination-info {
            color: var(--gray-600);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .pagination-info strong {
            color: var(--gray-900);
        }

        .pagination {
            margin: 0;
        }

        .pagination .page-link {
            border: 2px solid var(--gray-200);
            background: white;
            color: var(--gray-700);
            font-weight: 600;
            padding: 0.5rem 0.875rem;
            margin: 0 0.25rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .pagination .page-link:hover {
            background-color: var(--gray-100);
            border-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: transparent;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .pagination .page-item.disabled .page-link {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Utility Classes */
        .text-primary-dark {
            color: var(--primary-color) !important;
        }

        .text-success-dark {
            color: var(--success-color) !important;
        }

        .text-warning-dark {
            color: var(--warning-color) !important;
        }

        .text-danger-dark {
            color: var(--danger-color) !important;
        }

        .text-muted {
            color: var(--gray-500) !important;
        }

        table tbody strong {
            color: var(--gray-900);
        }

        table tbody small {
            color: var(--gray-500);
        }

        /* Tab navigation styling */
        .nav-pills .nav-link {
            color: #6b7280;
            background: #f3f4f6;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
            margin-right: 0.5rem;
            transition: all 0.2s ease;
        }

        .nav-pills .nav-link:hover {
            background: #e5e7eb;
        }

        .nav-pills .nav-link.active {
            background: #4f46e5;
            color: white;
        }

        /* Progress bar styling */
        .progress {
            background-color: #e5e7eb;
        }

        /* Modal styling */
        .modal-xl {
            max-width: 90%;
        }

        @media (max-width: 768px) {
            .nav-pills .nav-link {
                font-size: 0.75rem;
                padding: 0.4rem 0.6rem;
                margin-right: 0.25rem;
                margin-bottom: 0.25rem;
            }
        }
        /* Classy Tab Transitions */
        @keyframes slide-up-fade-in {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .tab-pane.active {
            /* The animation is applied when the tab becomes active */
            animation: slide-up-fade-in 0.4s ease-out forwards;
        }

        .terbilang-output {
            font-style: italic;
            color: #6c757d;
            margin-top: 0.25rem;
            display: block;
            text-transform: capitalize;
        }

    </style>
</head>
<body>

    <div class="container-fluid">
        <!-- Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1>
                        <i class="bi bi-briefcase text-primary-dark me-2"></i>Dashboard Pemanfaatan BMN
                    </h1>
                    <p class="mb-0">Monitor dan kelola pemanfaatan Barang Milik Negara melalui sistem sewa/kerjasama</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <!-- Notification Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-white position-relative p-2 rounded-circle shadow-sm" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="width: 45px; height: 45px; border: 1px solid var(--gray-200);">
                            <i class="bi bi-bell-fill text-primary" style="font-size: 1.2rem;"></i>
                            @if(isset($notifications) && $notifications->count() > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white">
                                    {{ $notifications->count() }}
                                    <span class="visually-hidden">unread messages</span>
                                </span>
                            @endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-0 shadow-lg border-0" aria-labelledby="notificationDropdown" style="width: 350px; max-height: 450px; overflow-y: auto; border-radius: 1rem; z-index: 1050;">
                            <li class="p-3 bg-primary text-white" style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-bell me-2"></i>Notifikasi Sewa</h6>
                                    <span class="badge bg-white text-primary rounded-pill">{{ isset($notifications) ? $notifications->count() : 0 }}</span>
                                </div>
                            </li>
                            @if(isset($notifications) && $notifications->count() > 0)
                                @foreach($notifications as $notif)
                                    <li>
                                        <a class="dropdown-item p-3 border-bottom" href="{{ route('bmn.utilization.documents', $notif->id) }}">
                                            <div class="d-flex align-items-start">
                                                <div class="me-3 flex-shrink-0">
                                                    @if(\Carbon\Carbon::parse($notif->surat_konfirmasi_tanggal_berakhir)->isPast())
                                                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                                        </div>
                                                    @else
                                                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                            <i class="bi bi-clock-history"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="mb-1 fw-bold text-wrap text-dark" style="font-size: 0.9rem;">{{ $notif->nama_mitra_penyewa }}</p>
                                                    <p class="mb-1 small text-muted">
                                                        @if(\Carbon\Carbon::parse($notif->surat_konfirmasi_tanggal_berakhir)->isPast())
                                                            <span class="text-danger fw-semibold">Telah berakhir</span> pada {{ \Carbon\Carbon::parse($notif->surat_konfirmasi_tanggal_berakhir)->format('d M Y') }}
                                                        @else
                                                            <span class="text-warning fw-semibold">Akan berakhir</span> pada {{ \Carbon\Carbon::parse($notif->surat_konfirmasi_tanggal_berakhir)->format('d M Y') }}
                                                        @endif
                                                    </p>
                                                    <small class="text-primary" style="font-size: 0.75rem;">Klik untuk perpanjang</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            @else
                                <li class="p-5 text-center text-muted">
                                    <i class="bi bi-bell-slash fs-1 d-block mb-3 text-gray-300"></i>
                                    <p class="mb-0">Tidak ada notifikasi saat ini</p>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <button type="button" class="btn btn-primary add-more-btn" data-bs-toggle="modal" data-bs-target="#addUtilizationModal">
                        <i class="bi bi-plus-lg me-2"></i>Tambah Pemanfaatan
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-5 g-4 mb-4">
            <div class="col">
                <div class="stat-card stat-card-primary h-100">
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <p class="stat-title">Total Pemanfaatan</p>
                    <p class="stat-value" id="total-utilization">0</p>
                </div>
            </div>
            <div class="col">
                <div class="stat-card stat-card-success h-100">
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <p class="stat-title">Data Lengkap</p>
                    <p class="stat-value" id="complete-utilization">0</p>
                </div>
            </div>
            <div class="col">
                <div class="stat-card stat-card-info h-100">
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <p class="stat-title">Aktif Berlangsung</p>
                    <p class="stat-value" id="active-utilization">0</p>
                </div>
            </div>
            <div class="col">
                <div class="stat-card stat-card-warning h-100">
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <p class="stat-title">Pendapatan Sewa</p>
                    <p class="stat-value" id="revenue-utilization">Rp 0</p>
                </div>
            </div>
            <div class="col">
                <div class="stat-card stat-card-danger h-100">
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <p class="stat-title">Outstanding</p>
                    <p class="stat-value" id="outstanding-utilization">Rp 0</p>
                    <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">Belum Dibayar</small>
                </div>
            </div>
        </div>

        <!-- Main Content - Full Width Table -->
        <div class="table-container">
            <div class="table-header">
                <div>
                    <h3>
                        <i class="bi bi-table text-primary-dark me-2"></i>Daftar Pemanfaatan BMN
                    </h3>
                </div>
                <div class="d-flex gap-3 align-items-center flex-wrap">
                    <div class="search-wrapper">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="search-input" id="search-input" placeholder="Cari data pemanfaatan...">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Status</th>
                            <th scope="col">PIC Penyewa</th>
                            <th scope="col">Nama Mitra</th>
                            <th scope="col">Jenis Mitra</th>
                            <th scope="col">Jenis Usulan</th>
                            <th scope="col">Peruntukan Sewa</th>
                            <th scope="col">Kontak</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="utilization-table-body">
                        <!-- Data will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Menampilkan <strong id="showing-start">0</strong> - <strong id="showing-end">0</strong> dari <strong id="total-records">0</strong> data
                </div>
                <nav aria-label="Table pagination">
                    <ul class="pagination mb-0" id="pagination-controls">
                        <!-- Pagination will be populated by JavaScript -->
                    </ul>
                </nav>
            </div>
        </div>


    </div>

    <!-- Add Utilization Modal - TAHAP 1: Informasi Penyewa -->
    <div class="modal fade" id="addUtilizationModal" tabindex="-1" aria-labelledby="addUtilizationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addUtilizationModalLabel">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Pemanfaatan BMN
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="add-utilization-form">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Tahap 1:</strong> Isi informasi penyewa terlebih dahulu. Anda dapat melengkapi detail lainnya nanti.
                        </div>

                        <h6 class="mb-3 text-primary"><i class="bi bi-person-badge me-2"></i>Informasi Penyewa</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="pic_penyewa" class="form-label">PIC Penyewa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="pic_penyewa" name="pic_penyewa" required>
                            </div>
                            <div class="col-md-6">
                                <label for="nomor_hp_pic_penyewa" class="form-label">Nomor HP PIC Penyewa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nomor_hp_pic_penyewa" name="nomor_hp_pic_penyewa" placeholder="08xx atau +62xxx" required>
                            </div>
                            <div class="col-md-6">
                                <label for="pic_administrasi_bmn" class="form-label">PIC Administrasi BMN <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="pic_administrasi_bmn" name="pic_administrasi_bmn" required>
                            </div>
                            <div class="col-md-6">
                                <label for="nomor_pic_administrasi_bmn" class="form-label">Nomor HP PIC Admin BMN <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nomor_pic_administrasi_bmn" name="nomor_pic_administrasi_bmn" placeholder="08xx atau +62xxx" required>
                            </div>
                            <div class="col-12">
                                <label for="nama_mitra_penyewa" class="form-label">Nama Mitra Penyewa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_mitra_penyewa" name="nama_mitra_penyewa" required>
                            </div>
                            <div class="col-md-6">
                                <label for="jenis_mitra" class="form-label">Jenis Mitra <span class="text-danger">*</span></label>
                                <select class="form-select" id="jenis_mitra" name="jenis_mitra" required>
                                    <option value="">Pilih Jenis Mitra</option>
                                    <option value="Perusahaan">Perusahaan</option>
                                    <option value="Yayasan">Yayasan</option>
                                    <option value="Koperasi">Koperasi</option>
                                    <option value="Perseorangan">Perseorangan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="jenis_usulan" class="form-label">Jenis Usulan <span class="text-danger">*</span></label>
                                <select class="form-select" id="jenis_usulan" name="jenis_usulan" required>
                                    <option value="">Pilih Jenis Usulan</option>
                                    <option value="Perpanjangan">Perpanjangan</option>
                                    <option value="Usulan Baru">Usulan Baru</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="peruntukan_sewa" class="form-label">Peruntukan Sewa</label>
                                <textarea class="form-control" id="peruntukan_sewa" name="peruntukan_sewa" rows="2" placeholder="Jelaskan peruntukan sewa BMN"></textarea>
                            </div>
                            <div class="col-12">
                                <label for="keterangan_uraian" class="form-label">Keterangan/Uraian</label>
                                <textarea class="form-control" id="keterangan_uraian" name="keterangan_uraian" rows="2" placeholder="Informasi tambahan"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan & Lanjutkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Utilization Modal - Edit Informasi Penyewa -->
    <div class="modal fade" id="editUtilizationModal" tabindex="-1" aria-labelledby="editUtilizationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="editUtilizationModalLabel">
                        <i class="bi bi-pencil me-2"></i>Edit Informasi Penyewa
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="edit-utilization-form">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="edit_id" name="id">

                        <div class="alert alert-warning mb-3">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Edit Informasi Dasar:</strong> Untuk mengubah data detail lainnya, gunakan tombol "Lengkapi Data".
                        </div>

                        <h6 class="mb-3 text-warning"><i class="bi bi-person-badge me-2"></i>Informasi Penyewa</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_pic_penyewa" class="form-label">PIC Penyewa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_pic_penyewa" name="pic_penyewa" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_nomor_hp_pic_penyewa" class="form-label">Nomor HP PIC Penyewa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_nomor_hp_pic_penyewa" name="nomor_hp_pic_penyewa" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_pic_administrasi_bmn" class="form-label">PIC Administrasi BMN <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_pic_administrasi_bmn" name="pic_administrasi_bmn" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_nomor_pic_administrasi_bmn" class="form-label">Nomor HP PIC Admin BMN <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_nomor_pic_administrasi_bmn" name="nomor_pic_administrasi_bmn" required>
                            </div>
                            <div class="col-12">
                                <label for="edit_nama_mitra_penyewa" class="form-label">Nama Mitra Penyewa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_nama_mitra_penyewa" name="nama_mitra_penyewa" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_jenis_mitra" class="form-label">Jenis Mitra <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_jenis_mitra" name="jenis_mitra" required>
                                    <option value="">Pilih Jenis Mitra</option>
                                    <option value="Perusahaan">Perusahaan</option>
                                    <option value="Yayasan">Yayasan</option>
                                    <option value="Koperasi">Koperasi</option>
                                    <option value="Perseorangan">Perseorangan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_jenis_usulan" class="form-label">Jenis Usulan <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_jenis_usulan" name="jenis_usulan" required>
                                    <option value="">Pilih Jenis Usulan</option>
                                    <option value="Perpanjangan">Perpanjangan</option>
                                    <option value="Usulan Baru">Usulan Baru</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="edit_peruntukan_sewa" class="form-label">Peruntukan Sewa</label>
                                <textarea class="form-control" id="edit_peruntukan_sewa" name="peruntukan_sewa" rows="2"></textarea>
                            </div>
                            <div class="col-12">
                                <label for="edit_keterangan_uraian" class="form-label">Keterangan/Uraian</label>
                                <textarea class="form-control" id="edit_keterangan_uraian" name="keterangan_uraian" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save me-1"></i>Perbarui
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- Complete Data Confirmation Modal -->
    <div class="modal fade" id="completeDataConfirmModal" tabindex="-1" aria-labelledby="completeDataConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="completeDataConfirmModalLabel">
                        <i class="bi bi-info-circle me-2"></i>Lengkapi Data Pemanfaatan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3"><strong>Data pemanfaatan berhasil ditambahkan!</strong></p>
                    <p>Apakah Anda ingin melengkapi data pemanfaatan sekarang?</p>
                    <div class="alert alert-light border mt-3" role="alert">
                        <i class="bi bi-lightbulb text-warning me-2"></i>
                        <small><strong>Catatan:</strong> Jika memilih "Nanti", Anda dapat melengkapi data kapan saja melalui tombol <strong>Edit</strong> pada daftar pemanfaatan.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Nanti</button>
                    <button type="button" class="btn btn-info text-white" id="complete-now-btn">
                        <i class="bi bi-pencil-square me-1"></i>Lengkapi Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Complete Data Multi-Tab Modal -->
    <div class="modal fade" id="completeDataModal" tabindex="-1" aria-labelledby="completeDataModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="completeDataModalLabel">
                        <i class="bi bi-clipboard-data me-2"></i>Lengkapi Data Pemanfaatan BMN
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="complete-data-form">
                    @csrf
                    <input type="hidden" id="complete_data_id" name="id">

                    <div class="modal-body">
                        <!-- Progress Indicator -->
                        <div class="progress mb-4" style="height: 3px;">
                            <div class="progress-bar bg-info" id="tab-progress" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>

                        <!-- Tab Navigation - 4 TABS FOR DOCUMENTS -->
                        <ul class="nav nav-pills mb-4" id="completeDataTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tab1-tab" data-bs-toggle="pill" data-bs-target="#tab1" type="button" role="tab">
                                    <i class="bi bi-file-earmark-check me-1"></i>1. Dokumen Konfirmasi
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab2-tab" data-bs-toggle="pill" data-bs-target="#tab2" type="button" role="tab">
                                    <i class="bi bi-file-earmark-ruled me-1"></i>2. Dokumen Usulan
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab3-tab" data-bs-toggle="pill" data-bs-target="#tab3" type="button" role="tab">
                                    <i class="bi bi-building me-1"></i>3. Dokumen Penilaian
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab4-tab" data-bs-toggle="pill" data-bs-target="#tab4" type="button" role="tab">
                                    <i class="bi bi-file-text me-1"></i>4. Dokumen Final
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="completeDataTabContent">
                            <!-- Tab 1: Dokumen Konfirmasi -->
                            <div class="tab-pane fade show active" id="tab1" role="tabpanel">
                                <h6 class="mb-4 text-info"><i class="bi bi-file-earmark-check me-2"></i>Dokumen Konfirmasi</h6>

                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong><i class="bi bi-folder-plus me-2"></i>Upload Dokumen Konfirmasi</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="surat_konfirmasi_lampiran" class="form-label">Lampiran Surat Konfirmasi</label>
                                                <div id="view-surat_konfirmasi_lampiran" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="surat_konfirmasi_lampiran" name="surat_konfirmasi_lampiran">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="dokumen_surat_usulan_sewa" class="form-label">Surat Usulan Sewa/Perpanjangan dari Mitra</label>
                                                <div id="view-dokumen_surat_usulan_sewa" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_surat_usulan_sewa" name="dokumen_surat_usulan_sewa">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="dokumen_npwp" class="form-label">NPWP</label>
                                                <div id="view-dokumen_npwp" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_npwp" name="dokumen_npwp">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="dokumen_ktp_penandatangan" class="form-label">KTP Penandatangan Perjanjian</label>
                                                <div id="view-dokumen_ktp_penandatangan" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_ktp_penandatangan" name="dokumen_ktp_penandatangan">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="dokumen_nib" class="form-label">NIB</label>
                                                <div id="view-dokumen_nib" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_nib" name="dokumen_nib">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 2: Dokumen Usulan -->
                            <div class="tab-pane fade" id="tab2" role="tabpanel">
                                <h6 class="mb-4 text-info"><i class="bi bi-file-earmark-ruled me-2"></i>Dokumen Usulan Pemanfaatan</h6>

                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong><i class="bi bi-folder-plus me-2"></i>Upload Dokumen Usulan</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label for="dokumen_psp" class="form-label">Upload PSP</label>
                                                <div id="view-dokumen_psp" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_psp" name="dokumen_psp">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="dokumen_kib" class="form-label">Upload KIB</label>
                                                <div id="view-dokumen_kib" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_kib" name="dokumen_kib">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="dokumen_usulan_ttd" class="form-label">Surat Usulan/SPTJM/Pernyataan (TTD)</label>
                                                <div id="view-dokumen_usulan_ttd" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_usulan_ttd" name="dokumen_usulan_ttd">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 3: Dokumen Penilaian -->
                            <div class="tab-pane fade" id="tab3" role="tabpanel">
                                <h6 class="mb-4 text-info"><i class="bi bi-building me-2"></i>Dokumen Penilaian KPKNL</h6>

                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong><i class="bi bi-folder-plus me-2"></i>Upload Dokumen Penilaian</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label for="dokumen_jadwal_penilaian" class="form-label">Jadwal Penilaian</label>
                                                <div id="view-dokumen_jadwal_penilaian" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_jadwal_penilaian" name="dokumen_jadwal_penilaian">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="dokumen_basl" class="form-label">BASL</label>
                                                <div id="view-dokumen_basl" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_basl" name="dokumen_basl">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="dokumen_persetujuan_kpknl" class="form-label">Persetujuan KPKNL</label>
                                                <div id="view-dokumen_persetujuan_kpknl" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_persetujuan_kpknl" name="dokumen_persetujuan_kpknl">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="dokumen_kode_billing" class="form-label">Dokumen Kode Billing</label>
                                                <div id="view-dokumen_kode_billing" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_kode_billing" name="dokumen_kode_billing">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 4: Dokumen Final -->
                            <div class="tab-pane fade" id="tab4" role="tabpanel">
                                <h6 class="mb-4 text-info"><i class="bi bi-file-text me-2"></i>Dokumen Final</h6>

                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong><i class="bi bi-folder-plus me-2"></i>Upload Dokumen Final</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="dokumen_bukti_bayar" class="form-label">Bukti Bayar</label>
                                                <div id="view-dokumen_bukti_bayar" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_bukti_bayar" name="dokumen_bukti_bayar">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="dokumen_perjanjian" class="form-label">Perjanjian Sewa</label>
                                                <div id="view-dokumen_perjanjian" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_perjanjian" name="dokumen_perjanjian">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="perjanjian_logo_penyewa" class="form-label">Logo Penyewa (Opsional)</label>
                                                <div id="view-perjanjian_logo_penyewa" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="perjanjian_logo_penyewa" name="perjanjian_logo_penyewa" accept="image/*">
                                                <small class="text-muted">Format: JPG, PNG (Max: 1MB)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" id="prev-tab-btn" style="display: none;">
                            <i class="bi bi-arrow-left me-1"></i>Sebelumnya
                        </button>
                        <div class="ms-auto d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="button" class="btn btn-info text-white" id="next-tab-btn">
                                Selanjutnya<i class="bi bi-arrow-right ms-1"></i>
                            </button>
                            <button type="submit" class="btn btn-success" id="save-complete-btn" style="display: none;">
                                <i class="bi bi-check-circle me-1"></i>Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Add this block to bypass ngrok browser warning for AJAX requests
        $.ajaxSetup({
            headers: {
                'ngrok-skip-browser-warning': 'true'
            }
        });

        let currentUtilizationId = null;
        let newlyAddedUtilizationId = null;
        let currentTab = 1;
        const totalTabs = 4; // Updated: 4 tabs for documents only

        // Bootstrap 5 Modal Helper - Enable jQuery-like syntax
        (function($) {
            const originalModal = $.fn.modal;
            $.fn.modal = function(action) {
                if (action === 'show' || action === 'hide' || action === 'toggle') {
                    this.each(function() {
                        const modalElement = this;
                        let modalInstance = bootstrap.Modal.getInstance(modalElement);

                        if (!modalInstance) {
                            modalInstance = new bootstrap.Modal(modalElement);
                        }

                        modalInstance[action]();
                    });
                    return this;
                }
                // Fallback to original if exists
                if (originalModal) {
                    return originalModal.apply(this, arguments);
                }
                return this;
            };
        })(jQuery);

        // Pagination and search variables
        let allUtilizationData = @json($utilizationData);
        let filteredData = [];
        let currentPage = 1;
        const itemsPerPage = 10;
                let searchQuery = '';
        
                // Terbilang function
                function updateTerbilang(element) {
                    const value = element.value;
                    const outputElement = element.nextElementSibling;
                    console.log('Input value:', value);
                    if (value) {
                        try {
                            const terbilangValue = terbilang(value);
                            console.log('Terbilang output:', terbilangValue);
                            outputElement.textContent = terbilangValue + ' rupiah';
                        } catch (e) {
                            console.error('Terbilang error:', e);
                            outputElement.textContent = 'Error konversi.';
                        }
                    } else {
                        outputElement.textContent = '...';
                    }
                }
        
        
        
                // Format currency
                function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }

        // Format date
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        // Format date for HTML input type="date" (YYYY-MM-DD)
        function formatToInputDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // Calculate period
        function calculatePeriod(startDate, endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            const diffMonths = Math.ceil(diffDays / 30);
            return `${diffMonths} bulan`;
        }

        // Search functionality
        $('#search-input').on('keyup', function() {
            searchQuery = $(this).val().toLowerCase();
            currentPage = 1;
            filterAndDisplayData();
        });

        // Filter data based on search query
        function filterAndDisplayData() {
            if (searchQuery === '') {
                filteredData = [...allUtilizationData];
            } else {
                filteredData = allUtilizationData.filter(util => {
                    return (
                        (util.pic_penyewa && util.pic_penyewa.toLowerCase().includes(searchQuery)) ||
                        (util.pic_administrasi_bmn && util.pic_administrasi_bmn.toLowerCase().includes(searchQuery)) ||
                        (util.nama_mitra_penyewa && util.nama_mitra_penyewa.toLowerCase().includes(searchQuery)) ||
                        (util.jenis_mitra && util.jenis_mitra.toLowerCase().includes(searchQuery)) ||
                        (util.jenis_usulan && util.jenis_usulan.toLowerCase().includes(searchQuery)) ||
                        (util.peruntukan_sewa && util.peruntukan_sewa.toLowerCase().includes(searchQuery)) ||
                        (util.keterangan_uraian && util.keterangan_uraian.toLowerCase().includes(searchQuery)) ||
                        (util.nomor_hp_pic_penyewa && util.nomor_hp_pic_penyewa.toLowerCase().includes(searchQuery))
                    );
                });
            }
            displayTableData();
            renderPagination();
        }

        // Display table data with pagination
        function displayTableData() {
            const tbody = $('#utilization-table-body');
            tbody.empty();

            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, filteredData.length);
            const paginatedData = filteredData.slice(startIndex, endIndex);

            if (paginatedData.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="bi bi-inbox display-4 text-muted d-block mb-3"></i>
                            <p class="text-muted mb-0">Tidak ada data yang ditemukan</p>
                        </td>
                    </tr>
                `);
                updatePaginationInfo(0, 0, 0);
                return;
            }

            paginatedData.forEach((util, index) => {
                        // Check if data is complete
                        const isComplete = util.is_complete == 1 || util.is_complete === true;

                        // Status badge
                        const statusBadge = isComplete
                            ? '<span class="status-badge status-lengkap"><i class="bi bi-check-circle-fill me-1"></i>Lengkap</span>'
                            : '<span class="status-badge status-belum-lengkap"><i class="bi bi-exclamation-circle-fill me-1"></i>Belum Lengkap</span>';

                        // Row class for incomplete data
                        const rowClass = !isComplete ? 'data-incomplete' : '';

                        const row = `
                            <tr class="${rowClass}" id="row-${util.id}">
                                <td>${startIndex + index + 1}</td>
                                <td>
                                    ${statusBadge}
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="toggle-complete-${util.id}"
                                               ${isComplete ? 'checked' : ''}
                                               onchange="toggleCompleteStatus(${util.id}, this.checked)">
                                        <label class="form-check-label" for="toggle-complete-${util.id}">
                                            ${isComplete ? 'Lengkap' : 'Tandai Lengkap'}
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <strong>${util.pic_penyewa || '-'}</strong><br>
                                    <small class="text-muted">Admin: ${util.pic_administrasi_bmn || '-'}</small>
                                </td>
                                <td>
                                    <strong>${util.nama_mitra_penyewa || '-'}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary">${util.jenis_mitra || '-'}</span>
                                </td>
                                <td>
                                    <span class="badge ${util.jenis_usulan === 'Perpanjangan' ? 'bg-warning' : 'bg-success'}">${util.jenis_usulan || '-'}</span>
                                </td>
                                <td>
                                    ${util.peruntukan_sewa ? util.peruntukan_sewa.substring(0, 50) + '...' : '-'}
                                </td>
                                <td>
                                    <i class="bi bi-telephone me-1"></i>${util.nomor_hp_pic_penyewa || '-'}<br>
                                    <small class="text-muted"><i class="bi bi-telephone me-1"></i>${util.nomor_pic_administrasi_bmn || '-'}</small>
                                </td>
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <button class="btn btn-sm btn-outline-primary" onclick="editUtilization(${util.id})" title="Edit Informasi Dasar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" onclick="completeDataUtilization(${util.id})" title="Lengkapi Data Detail">
                                            <i class="bi bi-clipboard-data"></i>
                                        </button>
                                        <a href="/utilization-dashboard/${util.id}/documents" class="btn btn-sm btn-outline-success" title="Generate Dokumen">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteUtilization(${util.id})" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                        tbody.append(row);
                    });

            updatePaginationInfo(startIndex + 1, endIndex, filteredData.length);
            updateStats(allUtilizationData);
        }

        // Render pagination controls
        function renderPagination() {
            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            const paginationControls = $('#pagination-controls');
            paginationControls.empty();

            if (totalPages <= 1) {
                return;
            }

            // Previous button
            paginationControls.append(`
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="changePage(${currentPage - 1})">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            `);

            // Page numbers
            const maxPagesToShow = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
            let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

            if (endPage - startPage < maxPagesToShow - 1) {
                startPage = Math.max(1, endPage - maxPagesToShow + 1);
            }

            // First page
            if (startPage > 1) {
                paginationControls.append(`
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0)" onclick="changePage(1)">1</a>
                    </li>
                `);
                if (startPage > 2) {
                    paginationControls.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
                }
            }

            // Page numbers
            for (let i = startPage; i <= endPage; i++) {
                paginationControls.append(`
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="javascript:void(0)" onclick="changePage(${i})">${i}</a>
                    </li>
                `);
            }

            // Last page
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    paginationControls.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
                }
                paginationControls.append(`
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0)" onclick="changePage(${totalPages})">${totalPages}</a>
                    </li>
                `);
            }

            // Next button
            paginationControls.append(`
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="changePage(${currentPage + 1})">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            `);
        }

        // Change page
        function changePage(page) {
            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            displayTableData();
            renderPagination();
            // Scroll to top of table
            $('html, body').animate({
                scrollTop: $('.table-container').offset().top - 100
            }, 300);
        }

        // Update pagination info
        function updatePaginationInfo(start, end, total) {
            $('#showing-start').text(start);
            $('#showing-end').text(end);
            $('#total-records').text(total);
        }

        // This function is now used to REFRESH data from the server
        function resetCompleteDataForm() {
            // Reset the form itself, clearing all input fields
            $('#complete-data-form')[0].reset();

            // Manually clear any dynamically generated content
            $('.file-view-link').empty(); // Clear previously shown file links

            // Reset the tab navigation to the first tab
            currentTab = 1;
            updateTabNavigation();
        }

        function populateUtilizationTable() {
            $.ajax({
                url: '/utilization-dashboard',
                method: 'GET',
                success: function(response) {
                    allUtilizationData = response.utilizationData || [];
                    filterAndDisplayData();
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching utilization data:', error);
                    alert('Gagal memuat data pemanfaatan: ' + error);
                }
            });
        }

        // Update statistics
        function updateStats(utilizationData) {
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Reset time to start of day for accurate comparison

            const total = utilizationData.length;
            const complete = utilizationData.filter(u => u.is_complete == 1 || u.is_complete === true).length;

            // Aktif Berlangsung: filter berdasarkan nodin_konfirmasi_tanggal_berakhir_sewa yang belum jatuh tempo
            const active = utilizationData.filter(u => {
                if (!u.nodin_konfirmasi_tanggal_berakhir_sewa) return false;

                const endDate = new Date(u.nodin_konfirmasi_tanggal_berakhir_sewa);
                endDate.setHours(0, 0, 0, 0);

                // Aktif jika: tanggal berakhir sewa >= hari ini (belum jatuh tempo)
                return endDate >= today;
            }).length;

            // Pendapatan Sewa: Total yang SUDAH DIBAYAR (terealisasi)
            const revenue = utilizationData.reduce((sum, util) =>
                sum + (parseFloat(util.total_pendapatan_terealisasi) || 0), 0
            );

            // Outstanding: Total yang BELUM DIBAYAR
            const outstanding = utilizationData.reduce((sum, util) =>
                sum + (parseFloat(util.total_pendapatan_outstanding) || 0), 0
            );

            $('#total-utilization').text(total);
            $('#complete-utilization').text(complete);
            $('#active-utilization').text(active);
            $('#revenue-utilization').text(formatCurrency(revenue));
            $('#outstanding-utilization').text(formatCurrency(outstanding));
        }

        function editUtilization(id) {
            $.ajax({
                url: '/utilization-dashboard/' + id,
                method: 'GET',
                success: function(response) {
                    const util = response.data;

                    $('#edit_id').val(util.id);
                    $('#edit_pic_penyewa').val(util.pic_penyewa || '');
                    $('#edit_nomor_hp_pic_penyewa').val(util.nomor_hp_pic_penyewa || '');
                    $('#edit_pic_administrasi_bmn').val(util.pic_administrasi_bmn || '');
                    $('#edit_nomor_pic_administrasi_bmn').val(util.nomor_pic_administrasi_bmn || '');
                    $('#edit_nama_mitra_penyewa').val(util.nama_mitra_penyewa || '');
                    $('#edit_jenis_mitra').val(util.jenis_mitra || '');
                    $('#edit_jenis_usulan').val(util.jenis_usulan || '');
                    $('#edit_peruntukan_sewa').val(util.peruntukan_sewa || '');
                    $('#edit_keterangan_uraian').val(util.keterangan_uraian || '');

                    $('#editUtilizationModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching utilization for edit:', error);
                    alert('Gagal memuat data pemanfaatan: ' + error);
                }
            });
        }

        // Delete utilization with SweetAlert confirmation
        function deleteUtilization(id) {
            confirmDelete({
                itemName: 'data pemanfaatan BMN ini',
                onConfirm: function() {
                    // User klik "Ya, Hapus" - lakukan delete
                    $.ajax({
                        url: '/utilization-dashboard/' + id,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                populateUtilizationTable();
                                successToast('Data pemanfaatan berhasil dihapus!');
                            } else {
                                errorToast('Gagal menghapus data pemanfaatan.');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error deleting utilization:', error);
                            errorToast('Terjadi kesalahan saat menghapus data.');
                        }
                    });
                },
                onCancel: function() {
                    // User klik "Batal" - tidak perlu aksi khusus
                    console.log('Delete cancelled');
                }
            });
        }

        // Toggle complete status
        function toggleCompleteStatus(id, isComplete) {
            $.ajax({
                url: '/utilization-dashboard/' + id + '/toggle-complete',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    is_complete: isComplete ? 1 : 0
                },
                success: function(response) {
                    if (response.success) {
                        // Update the row visually
                        const row = $(`#row-${id}`);
                        const badge = row.find('.status-badge');
                        const label = row.find(`label[for="toggle-complete-${id}"]`);

                        if (isComplete) {
                            // Mark as complete
                            row.removeClass('data-incomplete');
                            badge.removeClass('status-belum-lengkap').addClass('status-lengkap');
                            badge.html('<i class="bi bi-check-circle-fill me-1"></i>Lengkap');
                            label.text('Lengkap');
                        } else {
                            // Mark as incomplete
                            row.addClass('data-incomplete');
                            badge.removeClass('status-lengkap').addClass('status-belum-lengkap');
                            badge.html('<i class="bi bi-exclamation-circle-fill me-1"></i>Belum Lengkap');
                            label.text('Tandai Lengkap');
                        }

                        // Find the item in the global data array and update it
                        const itemIndex = allUtilizationData.findIndex(item => item.id === id);
                        if (itemIndex > -1) {
                            allUtilizationData[itemIndex].is_complete = isComplete;
                        }

                        // Recalculate and update the KPI cards
                        updateStats(allUtilizationData);

                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: 'Gagal mengubah status kelengkapan data.',
                            icon: 'error',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'swal2-toast-modern'
                            }
                        });
                        // Revert checkbox state
                        $(`#toggle-complete-${id}`).prop('checked', !isComplete);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error toggling complete status:', error);
                    Swal.fire({
                        title: 'Terjadi kesalahan',
                        text: 'Terjadi kesalahan saat mengubah status.',
                        icon: 'error',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'swal2-toast-modern'
                        }
                    });
                    // Revert checkbox state
                    $(`#toggle-complete-${id}`).prop('checked', !isComplete);
                }
            });
        }

        // Complete data utilization (from dropdown menu) - UPDATED WITH ALL NEW FIELDS
        function completeDataUtilization(id) {
            currentUtilizationId = id;
            // Fetch existing data to populate file links only
            $.ajax({
                url: '{{ route("bmn.utilization.show", ":id") }}'.replace(':id', id),
                method: 'GET',
                success: function(response) {
                    const util = response.data;
                    resetCompleteDataForm();
                    $('#complete_data_id').val(util.id);

                    // Clear all previous file links
                    $('.file-view-link').empty();

                    const createLink = (filePath, containerId, fieldName) => {
                        if (filePath) {
                            const url = `/storage/${filePath}`;
                            let displayFileName = filePath.substring(filePath.lastIndexOf('/') + 1);
                            const escapedFieldName = fieldName.replace(/_/g, '\\_');
                            const regex = new RegExp(`^\\d+_(${escapedFieldName})_`);
                            displayFileName = displayFileName.replace(regex, '');
                            if (!displayFileName) displayFileName = filePath.substring(filePath.lastIndexOf('/') + 1);

                            $(`#${containerId}`).html(
                                `<div class="card border-info mb-2" style="max-width: 300px;">
                                    <div class="card-body p-2 d-flex justify-content-between align-items-center">
                                        <span class="text-truncate me-2" title="${displayFileName}">${displayFileName}</span>
                                        <a href="${url}" target="_blank" class="btn btn-sm btn-info text-white">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </div>`
                            );
                        }
                    };

                    // Tab 1: Dokumen Konfirmasi file links
                    createLink(util.surat_konfirmasi_lampiran, 'view-surat_konfirmasi_lampiran', 'surat_konfirmasi_lampiran');
                    createLink(util.dokumen_surat_usulan_sewa, 'view-dokumen_surat_usulan_sewa', 'dokumen_surat_usulan_sewa');
                    createLink(util.dokumen_npwp, 'view-dokumen_npwp', 'dokumen_npwp');
                    createLink(util.dokumen_ktp_penandatangan, 'view-dokumen_ktp_penandatangan', 'dokumen_ktp_penandatangan');
                    createLink(util.dokumen_nib, 'view-dokumen_nib', 'dokumen_nib');

                    // Tab 2: Dokumen Usulan file links
                    createLink(util.dokumen_psp, 'view-dokumen_psp', 'dokumen_psp');
                    createLink(util.dokumen_kib, 'view-dokumen_kib', 'dokumen_kib');
                    createLink(util.dokumen_usulan_ttd, 'view-dokumen_usulan_ttd', 'dokumen_usulan_ttd');

                    // Tab 3: Dokumen Penilaian file links
                    createLink(util.dokumen_jadwal_penilaian, 'view-dokumen_jadwal_penilaian', 'dokumen_jadwal_penilaian');
                    createLink(util.dokumen_basl, 'view-dokumen_basl', 'dokumen_basl');
                    createLink(util.dokumen_persetujuan_kpknl, 'view-dokumen_persetujuan_kpknl', 'dokumen_persetujuan_kpknl');
                    createLink(util.dokumen_kode_billing, 'view-dokumen_kode_billing', 'dokumen_kode_billing');

                    // Tab 4: Dokumen Final file links
                    createLink(util.dokumen_bukti_bayar, 'view-dokumen_bukti_bayar', 'dokumen_bukti_bayar');
                    createLink(util.dokumen_perjanjian, 'view-dokumen_perjanjian', 'dokumen_perjanjian');
                    createLink(util.perjanjian_logo_penyewa, 'view-perjanjian_logo_penyewa', 'perjanjian_logo_penyewa');

                    // Reset to first tab
                    currentTab = 1;
                    updateTabNavigation();

                    // Show modal
                    $('#completeDataModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Error loading data:', error);
                    Swal.fire({
                        title: 'Gagal',
                        text: 'Gagal memuat data untuk dilengkapi.',
                        icon: 'error',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'swal2-toast-modern'
                        }
                    });
                }
            });
        }

        // Form submission handlers
        $('#add-utilization-form').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: '/utilization-dashboard',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#addUtilizationModal').modal('hide');
                        $('#add-utilization-form')[0].reset();
                        populateUtilizationTable();

                        newlyAddedUtilizationId = response.data.id;
                        
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Data awal berhasil ditambahkan.',
                            icon: 'success',
                            customClass: {
                                popup: 'swal2-modern',
                                title: 'swal2-modern-title',
                                confirmButton: 'swal2-modern-confirm',
                                cancelButton: 'swal2-modern-cancel'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            $('#completeDataConfirmModal').modal('show');
                        });

                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: 'Gagal menambahkan pemanfaatan BMN.',
                            icon: 'error',
                            customClass: {
                                popup: 'swal2-modern',
                                title: 'swal2-modern-title',
                                confirmButton: 'swal2-modern-confirm',
                                cancelButton: 'swal2-modern-cancel'
                            },
                            buttonsStyling: false
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error adding utilization:', error);
                    Swal.fire({
                        title: 'Terjadi kesalahan',
                        text: 'Terjadi kesalahan: ' + error,
                        icon: 'error',
                        customClass: {
                            popup: 'swal2-modern',
                            title: 'swal2-modern-title',
                            confirmButton: 'swal2-modern-confirm',
                            cancelButton: 'swal2-modern-cancel'
                        },
                        buttonsStyling: false
                    });
                }
            });
        });

                $('#edit-utilization-form').on('submit', function(e) {

                    e.preventDefault();

                    const id = $('#edit_id').val();

                    $.ajax({

                        url: '/utilization-dashboard/' + id,

                        method: 'PUT',

                                                data: $(this).serialize(),

                                                success: function(response) {

                                                    if (response.success) {

                                                        $('#editUtilizationModal').modal('hide');

                                                        populateUtilizationTable();

                                                        Swal.fire({

                                                            title: 'Berhasil!',

                                                            text: 'Data berhasil diperbarui.',

                                                            icon: 'success',

                                                            timer: 2000,

                                                            showConfirmButton: false

                                                        });

                                                    } else {

                                                        Swal.fire('Gagal', 'Gagal memperbarui pemanfaatan BMN.', 'error');

                                                    }

                                                },

                                                error: function(xhr, status, error) {

                                                    console.error('Error updating utilization:', error);

                                                    Swal.fire('Error', 'Terjadi kesalahan saat memperbarui: ' + error, 'error');

                                                }
            });
        });

        $('#confirm-delete-btn').on('click', function() {
            $.ajax({
                url: '/utilization-dashboard/' + currentUtilizationId,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#deleteConfirmModal').modal('hide');
                        populateUtilizationTable();
                        Swal.fire({
                            title: 'Berhasil',
                            text: 'Pemanfaatan BMN berhasil dihapus!',
                            icon: 'success',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'swal2-toast-modern'
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: 'Gagal menghapus pemanfaatan BMN.',
                            icon: 'error',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'swal2-toast-modern'
                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting utilization:', error);
                    Swal.fire({
                        title: 'Terjadi kesalahan',
                        text: 'Terjadi kesalahan saat menghapus: ' + error,
                        icon: 'error',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'swal2-toast-modern'
                        }
                    });
                }
            });
        });

        // Handle "Lengkapi Sekarang" button
        $('#complete-now-btn').on('click', function() {
            $('#completeDataConfirmModal').modal('hide');

            // Load data for completion
            $.ajax({
                url: `/utilization-dashboard/${newlyAddedUtilizationId}`,
                method: 'GET',
                success: function(response) {
                    const util = response.data;

                    // Populate read-only fields in Tab 1
                    $('#complete_data_id').val(util.id);
                    $('#complete_pic').val(util.pic || '');
                    $('#complete_mitra').val(util.mitra || '');
                    $('#complete_jenis_usaha').val(util.jenis_usaha || '');
                    $('#complete_lokasi').val(util.lokasi || '');
                    $('#complete_uraian').val(util.uraian || '');

                    // Reset to first tab
                    currentTab = 1;
                    updateTabNavigation();

                    // Show modal
                    $('#completeDataModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Error loading data:', error);
                    Swal.fire({
                        title: 'Gagal',
                        text: 'Gagal memuat data untuk dilengkapi.',
                        icon: 'error',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'swal2-toast-modern'
                        }
                    });
                }
            });
        });

        // Tab navigation functions
        function updateTabNavigation() {
            // Hide all tabs
            for (let i = 1; i <= totalTabs; i++) {
                $(`#tab${i}`).removeClass('show active');
                $(`#tab${i}-tab`).removeClass('active');
            }

            // Show current tab
            $(`#tab${currentTab}`).addClass('show active');
            $(`#tab${currentTab}-tab`).addClass('active');

            // Update button visibility
            if (currentTab === 1) {
                $('#prev-tab-btn').hide();
            } else {
                $('#prev-tab-btn').show();
            }

            if (currentTab === totalTabs) {
                $('#next-tab-btn').hide();
                $('#save-complete-btn').show();
            } else {
                $('#next-tab-btn').show();
                $('#save-complete-btn').hide();
            }

            // Update progress bar
            const progress = (currentTab / totalTabs) * 100;
            $('#tab-progress').css('width', progress + '%').attr('aria-valuenow', progress);
        }

        // Next button handler
        $('#next-tab-btn').on('click', function() {
            if (currentTab < totalTabs) {
                currentTab++;
                updateTabNavigation();
            }
        });

        // Previous button handler
        $('#prev-tab-btn').on('click', function() {
            if (currentTab > 1) {
                currentTab--;
                updateTabNavigation();
            }
        });

        // Complete data form submission - Upload documents only
        $('#complete-data-form').on('submit', function(e) {
            e.preventDefault();
            const id = $('#complete_data_id').val();
            const formData = new FormData(this);
            formData.append('_method', 'POST');

            // Show loader
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengunggah...');

            $.ajax({
                url: '/utilization-dashboard/' + id + '/upload-documents',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#completeDataModal').modal('hide');
                        populateUtilizationTable();
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Dokumen berhasil diunggah.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Gagal', 'Gagal mengunggah dokumen.', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    let errorMsg = 'Terjadi kesalahan saat mengunggah dokumen.';
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        const firstError = Object.values(errors)[0][0];
                        errorMsg = `Gagal Validasi: ${firstError}`;
                    }
                    Swal.fire('Gagal!', errorMsg, 'error');
                },
                complete: function() {
                    // Re-enable button
                    submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i>Simpan');
                }
            });
        });

        // Reset modal on close
        $('#completeDataModal').on('hidden.bs.modal', function() {
            currentTab = 1;
            updateTabNavigation();
            $('#complete-data-form')[0].reset();
            $('.file-view-link').empty(); // Clear file links
        });

        // Initialize the page
        $(document).ready(function() {
            populateUtilizationTable();

            // Event listener for tab clicks to update progress bar
            $('#completeDataTabs button[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
                const targetTabId = $(e.target).attr('id'); // e.g., "tab1-tab"
                currentTab = parseInt(targetTabId.replace('tab', '').replace('-tab', ''));
                updateTabNavigation();
            });
        });
    </script>
    {{-- Session-based Alerts --}}
    @if (session('success'))
        <script>
            showSuccess('{{ session('success') }}');
        </script>
    @endif

    @if (session('error'))
        <script>
            showError('{{ session('error') }}');
        </script>
    @endif

    @if (session('info'))
        <script>
            showInfo('{{ session('info') }}');
        </script>
    @endif

    @if (session('warning'))
        <script>
            showWarning('{{ session('warning') }}');
        </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const modalId = urlParams.get('open_modal_id');
            const tabId = urlParams.get('tab');

            if (modalId && tabId) {
                console.log(`Redirected: Opening modal for ID ${modalId} and switching to tab ${tabId}`);

                // A short delay to ensure the modal is fully initialized before switching tabs
                setTimeout(function() {
                    // Open the main modal
                    completeDataUtilization(modalId);

                    // Wait for the modal to be shown before trying to switch tabs
                    const completeModal = document.getElementById('completeDataModal');
                    $(completeModal).on('shown.bs.modal', function () {
                        const tabButton = document.getElementById(tabId);
                        if (tabButton) {
                            const tab = new bootstrap.Tab(tabButton);
                            tab.show();
                        }
                    });

                }, 300);
            }
        });
    </script>
</body>
</html>