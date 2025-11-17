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
                <button type="button" class="btn btn-primary add-more-btn" data-bs-toggle="modal" data-bs-target="#addUtilizationModal">
                    <i class="bi bi-plus-lg me-2"></i>Tambah Pemanfaatan
                </button>
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
                            <div class="progress-bar bg-info" id="tab-progress" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>

                        <!-- Tab Navigation - 4 TABS SESUAI PROMPT.MD -->
                        <ul class="nav nav-pills mb-4" id="completeDataTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tab1-tab" data-bs-toggle="pill" data-bs-target="#tab1" type="button" role="tab">
                                    <i class="bi bi-person-badge me-1"></i>1. Informasi Penyewa
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab2-tab" data-bs-toggle="pill" data-bs-target="#tab2" type="button" role="tab">
                                    <i class="bi bi-file-earmark-check me-1"></i>2. Konfirmasi
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab3-tab" data-bs-toggle="pill" data-bs-target="#tab3" type="button" role="tab">
                                    <i class="bi bi-file-earmark-ruled me-1"></i>3. Usulan Pemanfaatan
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab4-tab" data-bs-toggle="pill" data-bs-target="#tab4" type="button" role="tab">
                                    <i class="bi bi-building me-1"></i>4. Penilaian KPKNL
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab5-tab" data-bs-toggle="pill" data-bs-target="#tab5" type="button" role="tab">
                                    <i class="bi bi-file-text me-1"></i>5. Perjanjian
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="completeDataTabContent">
                            <!-- Tab 1: Informasi Penyewa (READ ONLY) -->
                            <div class="tab-pane fade show active" id="tab1" role="tabpanel">
                                <div class="alert alert-info mb-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Informasi Penyewa:</strong> Data ini berasal dari Tahap 1. Untuk mengubah, gunakan tombol "Edit" di dashboard.
                                </div>
                                <h6 class="mb-3 text-info"><i class="bi bi-person-badge me-2"></i>Informasi Penyewa</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label"><strong>PIC Penyewa</strong></label>
                                        <input type="text" class="form-control-plaintext" id="view_pic_penyewa" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><strong>Nomor HP PIC Penyewa</strong></label>
                                        <input type="text" class="form-control-plaintext" id="view_nomor_hp_pic_penyewa" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><strong>PIC Administrasi BMN</strong></label>
                                        <input type="text" class="form-control-plaintext" id="view_pic_administrasi_bmn" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><strong>Nomor HP PIC Admin BMN</strong></label>
                                        <input type="text" class="form-control-plaintext" id="view_nomor_pic_administrasi_bmn" readonly>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label"><strong>Nama Mitra Penyewa</strong></label>
                                        <input type="text" class="form-control-plaintext" id="view_nama_mitra_penyewa" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><strong>Jenis Mitra</strong></label>
                                        <input type="text" class="form-control-plaintext" id="view_jenis_mitra" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><strong>Jenis Usulan</strong></label>
                                        <input type="text" class="form-control-plaintext" id="view_jenis_usulan" readonly>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label"><strong>Peruntukan Sewa</strong></label>
                                        <textarea class="form-control-plaintext" id="view_peruntukan_sewa" rows="2" readonly></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label"><strong>Keterangan/Uraian</strong></label>
                                        <textarea class="form-control-plaintext" id="view_keterangan_uraian" rows="2" readonly></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 2: KONFIRMASI (3 SECTIONS SESUAI PROMPT.MD) -->
                            <div class="tab-pane fade" id="tab2" role="tabpanel">
                                <h6 class="mb-4 text-info"><i class="bi bi-file-earmark-check me-2"></i>Konfirmasi</h6>

                                <!-- Section 1: Nodin Konfirmasi Perpanjangan Sewa -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong><i class="bi bi-file-earmark-text me-2"></i>Nodin Konfirmasi Perpanjangan Sewa</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="nodin_konfirmasi_nomor" class="form-label">Nomor Surat</label>
                                                <input type="text" class="form-control" id="nodin_konfirmasi_nomor" name="nodin_konfirmasi_nomor">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="nodin_konfirmasi_tanggal" class="form-label">Tanggal Surat</label>
                                                <input type="date" class="form-control" id="nodin_konfirmasi_tanggal" name="nodin_konfirmasi_tanggal">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="nodin_konfirmasi_mitra_peruntukan" class="form-label">Mitra dan Peruntukan Sewa</label>
                                                <input type="text" class="form-control" id="nodin_konfirmasi_mitra_peruntukan" name="nodin_konfirmasi_mitra_peruntukan">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="nodin_konfirmasi_tanggal_berakhir_sewa" class="form-label">Tanggal Berakhir Sewa</label>
                                                <input type="date" class="form-control" id="nodin_konfirmasi_tanggal_berakhir_sewa" name="nodin_konfirmasi_tanggal_berakhir_sewa">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 2: Surat Konfirmasi Perpanjangan Sewa -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong><i class="bi bi-envelope me-2"></i>Surat Konfirmasi Perpanjangan Sewa</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="surat_konfirmasi_nomor" class="form-label">Nomor Surat</label>
                                                <input type="text" class="form-control" id="surat_konfirmasi_nomor" name="surat_konfirmasi_nomor">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="surat_konfirmasi_tujuan" class="form-label">Tujuan Surat</label>
                                                <input type="text" class="form-control" id="surat_konfirmasi_tujuan" name="surat_konfirmasi_tujuan">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="surat_konfirmasi_peruntukan" class="form-label">Peruntukan Sewa</label>
                                                <input type="text" class="form-control" id="surat_konfirmasi_peruntukan" name="surat_konfirmasi_peruntukan">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="surat_konfirmasi_nomor_perjanjian_lama" class="form-label">Nomor Perjanjian Sewa Lama</label>
                                                <input type="text" class="form-control" id="surat_konfirmasi_nomor_perjanjian_lama" name="surat_konfirmasi_nomor_perjanjian_lama">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="surat_konfirmasi_tanggal_berakhir" class="form-label">Tanggal Berakhir</label>
                                                <input type="date" class="form-control" id="surat_konfirmasi_tanggal_berakhir" name="surat_konfirmasi_tanggal_berakhir">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="surat_konfirmasi_kasub_nama_nomor" class="form-label">Nama & Nomor Penanggung Jawab (Kasub)</label>
                                                <input type="text" class="form-control" id="surat_konfirmasi_kasub_nama_nomor" name="surat_konfirmasi_kasub_nama_nomor" placeholder="Nama: xxx, Nomor: xxx">
                                            </div>
                                            <div class="col-12">
                                                <label for="surat_konfirmasi_lampiran" class="form-label">Upload Lampiran Surat Konfirmasi</label>
                                                <div id="view-surat_konfirmasi_lampiran" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="surat_konfirmasi_lampiran" name="surat_konfirmasi_lampiran">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 3: Dokumen Pendukung -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong><i class="bi bi-folder-plus me-2"></i>Upload Dokumen Pendukung</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
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

                            <!-- Tab 3: USULAN PEMANFAATAN (SESUAI PROMPT.MD) -->
                            <div class="tab-pane fade" id="tab3" role="tabpanel">
                                <h6 class="mb-4 text-info"><i class="bi bi-file-earmark-ruled me-2"></i>Usulan Pemanfaatan</h6>

                                <!-- Section 1: Nodin Berjenjang -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong><i class="bi bi-file-earmark-text me-2"></i>Nodin Berjenjang</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="nodin_berjenjang_mitra" class="form-label">Mitra Penyewa</label>
                                                <input type="text" class="form-control" id="nodin_berjenjang_mitra" name="nodin_berjenjang_mitra">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="nodin_berjenjang_peruntukan" class="form-label">Peruntukan</label>
                                                <input type="text" class="form-control" id="nodin_berjenjang_peruntukan" name="nodin_berjenjang_peruntukan">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 2: Surat Usulan Sewa KPKNL -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong><i class="bi bi-envelope me-2"></i>Surat Usulan Sewa KPKNL</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="surat_usulan_kpknl_nomor" class="form-label">Nomor Surat</label>
                                                <input type="text" class="form-control" id="surat_usulan_kpknl_nomor" name="surat_usulan_kpknl_nomor">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="surat_usulan_kpknl_tanggal" class="form-label">Tanggal Surat</label>
                                                <input type="date" class="form-control" id="surat_usulan_kpknl_tanggal" name="surat_usulan_kpknl_tanggal">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="surat_usulan_kpknl_hal" class="form-label">Hal Surat (PT)</label>
                                                <input type="text" class="form-control" id="surat_usulan_kpknl_hal" name="surat_usulan_kpknl_hal">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="surat_usulan_kpknl_tujuan" class="form-label">Tujuan Surat</label>
                                                <input type="text" class="form-control" id="surat_usulan_kpknl_tujuan" name="surat_usulan_kpknl_tujuan">
                                            </div>
                                            <div class="col-12">
                                                <label for="surat_usulan_kpknl_isi" class="form-label">Isi Surat</label>
                                                <textarea class="form-control" id="surat_usulan_kpknl_isi" name="surat_usulan_kpknl_isi" rows="3" placeholder="Nama mitra: [nama], Tanggal berakhir: [tanggal]"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 3: SPTJM -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong><i class="bi bi-file-earmark-check me-2"></i>Surat Pernyataan Tanggung Jawab Mutlak (SPTJM)</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="sptjm_nomor" class="form-label">Nomor Surat</label>
                                                <input type="text" class="form-control" id="sptjm_nomor" name="sptjm_nomor">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="sptjm_tanggal" class="form-label">Tanggal Surat</label>
                                                <input type="date" class="form-control" id="sptjm_tanggal" name="sptjm_tanggal">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="sptjm_kode_barang" class="form-label">Kode Barang</label>
                                                <input type="text" class="form-control" id="sptjm_kode_barang" name="sptjm_kode_barang">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="sptjm_nup" class="form-label">NUP</label>
                                                <input type="text" class="form-control" id="sptjm_nup" name="sptjm_nup">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="sptjm_luasan_sewa" class="form-label">Luasan Sewa</label>
                                                <input type="text" class="form-control" id="sptjm_luasan_sewa" name="sptjm_luasan_sewa" placeholder="contoh: 100 m²">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="sptjm_lokasi_sewa" class="form-label">Lokasi Sewa</label>
                                                <input type="text" class="form-control" id="sptjm_lokasi_sewa" name="sptjm_lokasi_sewa">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 4: Surat Pernyataan -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong><i class="bi bi-file-earmark-text me-2"></i>Surat Pernyataan</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="surat_pernyataan_nomor" class="form-label">Nomor Surat</label>
                                                <input type="text" class="form-control" id="surat_pernyataan_nomor" name="surat_pernyataan_nomor">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="surat_pernyataan_tanggal" class="form-label">Tanggal Surat</label>
                                                <input type="date" class="form-control" id="surat_pernyataan_tanggal" name="surat_pernyataan_tanggal">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="surat_pernyataan_kode_barang" class="form-label">Kode Barang</label>
                                                <input type="text" class="form-control" id="surat_pernyataan_kode_barang" name="surat_pernyataan_kode_barang">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="surat_pernyataan_nup" class="form-label">NUP</label>
                                                <input type="text" class="form-control" id="surat_pernyataan_nup" name="surat_pernyataan_nup">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="surat_pernyataan_luasan_sewa" class="form-label">Luasan Sewa</label>
                                                <input type="text" class="form-control" id="surat_pernyataan_luasan_sewa" name="surat_pernyataan_luasan_sewa" placeholder="contoh: 100 m²">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="surat_pernyataan_lokasi_sewa" class="form-label">Lokasi Sewa</label>
                                                <input type="text" class="form-control" id="surat_pernyataan_lokasi_sewa" name="surat_pernyataan_lokasi_sewa">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 5: Daftar BMN yang Diusulkan (TABEL DINAMIS) -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <strong><i class="bi bi-table me-2"></i>Daftar BMN yang Diusulkan</strong>
                                        <button type="button" class="btn btn-sm btn-success" onclick="addBmnRow()">
                                            <i class="bi bi-plus-circle me-1"></i>Tambah BMN
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm" id="daftarBmnTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="40">No</th>
                                                        <th>Kode Barang</th>
                                                        <th>NUP</th>
                                                        <th>Jenis BMN</th>
                                                        <th>Luas (m²)</th>
                                                        <th>Nilai (Rp)</th>
                                                        <th>Lokasi</th>
                                                        <th>Peruntukan</th>
                                                        <th width="60">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="daftarBmnBody">
                                                    <!-- Rows will be added dynamically -->
                                                </tbody>
                                            </table>
                                        </div>
                                        <input type="hidden" id="daftar_bmn" name="daftar_bmn">
                                    </div>
                                </div>

                                <!-- Section 6: Upload Dokumen Usulan -->
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

                            <!-- Tab 4: PENILAIAN KPKNL (SESUAI PROMPT.MD) -->
                            <div class="tab-pane fade" id="tab4" role="tabpanel">
                                <h6 class="mb-4 text-info"><i class="bi bi-building me-2"></i>Penilaian KPKNL</h6>

                                <!-- Section 1: Upload Dokumen Penilaian -->
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
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 2: Nodin Penyampaian Surat Persetujuan KPKNL -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong><i class="bi bi-file-earmark-text me-2"></i>Nodin Penyampaian Surat Persetujuan KPKNL</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="nodin_persetujuan_kpknl_nomor" class="form-label">Nomor Surat</label>
                                                <input type="text" class="form-control" id="nodin_persetujuan_kpknl_nomor" name="nodin_persetujuan_kpknl_nomor">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="nodin_persetujuan_kpknl_tanggal" class="form-label">Tanggal Surat</label>
                                                <input type="date" class="form-control" id="nodin_persetujuan_kpknl_tanggal" name="nodin_persetujuan_kpknl_tanggal">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="nodin_persetujuan_kpknl_tujuan" class="form-label">Tujuan</label>
                                                <input type="text" class="form-control" id="nodin_persetujuan_kpknl_tujuan" name="nodin_persetujuan_kpknl_tujuan">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="nodin_persetujuan_kpknl_nomor_persetujuan" class="form-label">Nomor Persetujuan</label>
                                                <input type="text" class="form-control" id="nodin_persetujuan_kpknl_nomor_persetujuan" name="nodin_persetujuan_kpknl_nomor_persetujuan">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="nodin_persetujuan_kpknl_tanggal_persetujuan" class="form-label">Tanggal Persetujuan</label>
                                                <input type="date" class="form-control" id="nodin_persetujuan_kpknl_tanggal_persetujuan" name="nodin_persetujuan_kpknl_tanggal_persetujuan">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="nodin_persetujuan_kpknl_periode_sewa" class="form-label">Periode Sewa</label>
                                                <input type="text" class="form-control" id="nodin_persetujuan_kpknl_periode_sewa" name="nodin_persetujuan_kpknl_periode_sewa" placeholder="contoh: 01/01/2024 - 31/12/2024">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="nodin_persetujuan_kpknl_nominal" class="form-label">Nominal (Rp)</label>
                                                <input type="number" class="form-control" id="nodin_persetujuan_kpknl_nominal" name="nodin_persetujuan_kpknl_nominal" step="0.01">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="nodin_persetujuan_kpknl_mitra" class="form-label">Mitra</label>
                                                <input type="text" class="form-control" id="nodin_persetujuan_kpknl_mitra" name="nodin_persetujuan_kpknl_mitra">
                                            </div>
                                            <div class="col-12">
                                                <label for="nodin_persetujuan_kpknl_kasub" class="form-label">Nama & Nomor Penanggung Jawab (Kasub)</label>
                                                <input type="text" class="form-control" id="nodin_persetujuan_kpknl_kasub" name="nodin_persetujuan_kpknl_kasub" placeholder="Nama: xxx, Nomor: xxx">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 3: Surat Penyampaian Invoice -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong><i class="bi bi-envelope me-2"></i>Surat Penyampaian Invoice</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="surat_invoice_nomor" class="form-label">Nomor Surat</label>
                                                <input type="text" class="form-control" id="surat_invoice_nomor" name="surat_invoice_nomor">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="surat_invoice_tanggal" class="form-label">Tanggal Surat</label>
                                                <input type="date" class="form-control" id="surat_invoice_tanggal" name="surat_invoice_tanggal">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="surat_invoice_tujuan" class="form-label">Tujuan</label>
                                                <input type="text" class="form-control" id="surat_invoice_tujuan" name="surat_invoice_tujuan">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="surat_invoice_nomor_persetujuan" class="form-label">Nomor Persetujuan</label>
                                                <input type="text" class="form-control" id="surat_invoice_nomor_persetujuan" name="surat_invoice_nomor_persetujuan">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="surat_invoice_tanggal_persetujuan" class="form-label">Tanggal Persetujuan</label>
                                                <input type="date" class="form-control" id="surat_invoice_tanggal_persetujuan" name="surat_invoice_tanggal_persetujuan">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="surat_invoice_periode_sewa" class="form-label">Periode Sewa</label>
                                                <input type="text" class="form-control" id="surat_invoice_periode_sewa" name="surat_invoice_periode_sewa" placeholder="contoh: 01/01/2024 - 31/12/2024">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="surat_invoice_nominal" class="form-label">Nominal (Rp)</label>
                                                <input type="number" class="form-control" id="surat_invoice_nominal" name="surat_invoice_nominal" step="0.01">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="surat_invoice_mitra" class="form-label">Mitra</label>
                                                <input type="text" class="form-control" id="surat_invoice_mitra" name="surat_invoice_mitra">
                                            </div>
                                            <div class="col-12">
                                                <label for="surat_invoice_kasub" class="form-label">Nama & Nomor Penanggung Jawab (Kasub)</label>
                                                <input type="text" class="form-control" id="surat_invoice_kasub" name="surat_invoice_kasub" placeholder="Nama: xxx, Nomor: xxx">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 4: Upload Kode Billing -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong><i class="bi bi-file-earmark-code me-2"></i>Upload Kode Billing</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label for="dokumen_kode_billing" class="form-label">Dokumen Kode Billing</label>
                                                <div id="view-dokumen_kode_billing" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_kode_billing" name="dokumen_kode_billing">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 5: PERJANJIAN (SESUAI PROMPT.MD) -->
                            <div class="tab-pane fade" id="tab5" role="tabpanel">
                                <h6 class="mb-4 text-info"><i class="bi bi-file-text me-2"></i>Perjanjian</h6>

                                <!-- Section 1: Upload Dokumen Final -->
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
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 2: Detail Perjanjian -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong><i class="bi bi-file-earmark-text me-2"></i>Detail Perjanjian</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="perjanjian_logo_penyewa" class="form-label">Logo Penyewa (Opsional)</label>
                                                <div id="view-perjanjian_logo_penyewa" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="perjanjian_logo_penyewa" name="perjanjian_logo_penyewa" accept="image/*">
                                                <small class="text-muted">Format: JPG, PNG (Max: 1MB)</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="perjanjian_mitra" class="form-label">Mitra</label>
                                                <input type="text" class="form-control" id="perjanjian_mitra" name="perjanjian_mitra">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="perjanjian_peruntukan" class="form-label">Peruntukan</label>
                                                <input type="text" class="form-control" id="perjanjian_peruntukan" name="perjanjian_peruntukan">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="perjanjian_gedung" class="form-label">Gedung</label>
                                                <input type="text" class="form-control" id="perjanjian_gedung" name="perjanjian_gedung">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="perjanjian_hari_tanggal" class="form-label">Hari/Tanggal Penandatanganan</label>
                                                <input type="text" class="form-control" id="perjanjian_hari_tanggal" name="perjanjian_hari_tanggal" placeholder="contoh: Senin, 15 Januari 2024">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="perjanjian_nomor" class="form-label">Nomor Perjanjian</label>
                                                <input type="text" class="form-control" id="perjanjian_nomor" name="perjanjian_nomor">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="perjanjian_tanggal_penandatanganan" class="form-label">Tanggal Penandatanganan</label>
                                                <input type="date" class="form-control" id="perjanjian_tanggal_penandatanganan" name="perjanjian_tanggal_penandatanganan">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="jangka_waktu_nilai" class="form-label">Jangka Waktu (Nilai)</label>
                                                <input type="number" class="form-control" id="jangka_waktu_nilai" name="jangka_waktu_nilai" placeholder="12">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="jangka_waktu_satuan" class="form-label">Satuan</label>
                                                <select class="form-select" id="jangka_waktu_satuan" name="jangka_waktu_satuan">
                                                    <option value="">Pilih</option>
                                                    <option value="hari">Hari</option>
                                                    <option value="bulan">Bulan</option>
                                                    <option value="tahun">Tahun</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label for="perjanjian_detail_pihak_kedua" class="form-label">Detail Pihak Kedua</label>
                                                <textarea class="form-control" id="perjanjian_detail_pihak_kedua" name="perjanjian_detail_pihak_kedua" rows="3" placeholder="Nama lengkap, jabatan, alamat, dll"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 3: Nodin Permohonan Ttd Perjanjian -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong><i class="bi bi-file-earmark-text me-2"></i>Nodin Permohonan Ttd Perjanjian Kepada Mitra</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="nodin_ttd_nomor" class="form-label">Nomor Surat</label>
                                                <input type="text" class="form-control" id="nodin_ttd_nomor" name="nodin_ttd_nomor">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="nodin_ttd_tanggal" class="form-label">Tanggal Surat</label>
                                                <input type="date" class="form-control" id="nodin_ttd_tanggal" name="nodin_ttd_tanggal">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="nodin_ttd_tujuan" class="form-label">Tujuan</label>
                                                <input type="text" class="form-control" id="nodin_ttd_tujuan" name="nodin_ttd_tujuan">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="nodin_ttd_mitra" class="form-label">Mitra</label>
                                                <input type="text" class="form-control" id="nodin_ttd_mitra" name="nodin_ttd_mitra">
                                            </div>
                                            <div class="col-12">
                                                <label for="nodin_ttd_judul_perjanjian" class="form-label">Judul Perjanjian</label>
                                                <input type="text" class="form-control" id="nodin_ttd_judul_perjanjian" name="nodin_ttd_judul_perjanjian">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 4: Nodin Berjenjang Internal -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong><i class="bi bi-file-earmark-text me-2"></i>Nodin Berjenjang Internal</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="nodin_internal_nomor" class="form-label">Nomor Surat</label>
                                                <input type="text" class="form-control" id="nodin_internal_nomor" name="nodin_internal_nomor">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="nodin_internal_tanggal" class="form-label">Tanggal Surat</label>
                                                <input type="date" class="form-control" id="nodin_internal_tanggal" name="nodin_internal_tanggal">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="nodin_internal_mitra" class="form-label">Mitra</label>
                                                <input type="text" class="form-control" id="nodin_internal_mitra" name="nodin_internal_mitra">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="nodin_internal_judul_perjanjian" class="form-label">Judul Perjanjian</label>
                                                <input type="text" class="form-control" id="nodin_internal_judul_perjanjian" name="nodin_internal_judul_perjanjian">
                                            </div>
                                            <div class="col-12">
                                                <label for="nodin_internal_nomor_perjanjian" class="form-label">Nomor Perjanjian</label>
                                                <input type="text" class="form-control" id="nodin_internal_nomor_perjanjian" name="nodin_internal_nomor_perjanjian">
                                            </div>
                                            <div class="col-12">
                                                <label for="nodin_internal_detail_persetujuan" class="form-label">Detail Persetujuan Sewa</label>
                                                <textarea class="form-control" id="nodin_internal_detail_persetujuan" name="nodin_internal_detail_persetujuan" rows="3"></textarea>
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
        const totalTabs = 5; // Updated: 5 tabs instead of 7

        // Pagination and search variables
        let allUtilizationData = @json($utilizationData);
        let filteredData = [];
        let currentPage = 1;
        const itemsPerPage = 10;
        let searchQuery = '';



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
            clearBmnTable(); // Clear the dynamic BMN table

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
            // Fetch existing data to populate the form
            $.ajax({
                url: '{{ route("bmn.utilization.show", ":id") }}'.replace(':id', id),
                method: 'GET',
                success: function(response) {
                    const util = response.data;
                    resetCompleteDataForm();
                    $('#complete_data_id').val(util.id);

                    // ===== TAB 1: INFORMASI PENYEWA (READ ONLY) =====
                    $('#view_pic_penyewa').val(util.pic_penyewa || '-');
                    $('#view_nomor_hp_pic_penyewa').val(util.nomor_hp_pic_penyewa || '-');
                    $('#view_pic_administrasi_bmn').val(util.pic_administrasi_bmn || '-');
                    $('#view_nomor_pic_administrasi_bmn').val(util.nomor_pic_administrasi_bmn || '-');
                    $('#view_nama_mitra_penyewa').val(util.nama_mitra_penyewa || '-');
                    $('#view_jenis_mitra').val(util.jenis_mitra || '-');
                    $('#view_jenis_usulan').val(util.jenis_usulan || '-');
                    $('#view_peruntukan_sewa').val(util.peruntukan_sewa || '-');
                    $('#view_keterangan_uraian').val(util.keterangan_uraian || '-');

                    // ===== TAB 2: KONFIRMASI =====
                    // Nodin Konfirmasi
                    $('#nodin_konfirmasi_nomor').val(util.nodin_konfirmasi_nomor || '');
                    $('#nodin_konfirmasi_tanggal').val(formatToInputDate(util.nodin_konfirmasi_tanggal));
                    $('#nodin_konfirmasi_mitra_peruntukan').val(util.nodin_konfirmasi_mitra_peruntukan || '');
                    $('#nodin_konfirmasi_tanggal_berakhir_sewa').val(formatToInputDate(util.nodin_konfirmasi_tanggal_berakhir_sewa));

                    // Surat Konfirmasi
                    $('#surat_konfirmasi_nomor').val(util.surat_konfirmasi_nomor || '');
                    $('#surat_konfirmasi_tujuan').val(util.surat_konfirmasi_tujuan || '');
                    $('#surat_konfirmasi_peruntukan').val(util.surat_konfirmasi_peruntukan || '');
                    $('#surat_konfirmasi_nomor_perjanjian_lama').val(util.surat_konfirmasi_nomor_perjanjian_lama || '');
                    $('#surat_konfirmasi_tanggal_berakhir').val(formatToInputDate(util.surat_konfirmasi_tanggal_berakhir));
                    $('#surat_konfirmasi_kasub_nama_nomor').val(util.surat_konfirmasi_kasub_nama_nomor || '');

                    // ===== TAB 3: USULAN PEMANFAATAN =====
                    // Nodin Berjenjang
                    $('#nodin_berjenjang_mitra').val(util.nodin_berjenjang_mitra || '');
                    $('#nodin_berjenjang_peruntukan').val(util.nodin_berjenjang_peruntukan || '');

                    // Surat Usulan KPKNL
                    $('#surat_usulan_kpknl_nomor').val(util.surat_usulan_kpknl_nomor || '');
                    $('#surat_usulan_kpknl_tanggal').val(formatToInputDate(util.surat_usulan_kpknl_tanggal));
                    $('#surat_usulan_kpknl_hal').val(util.surat_usulan_kpknl_hal || '');
                    $('#surat_usulan_kpknl_tujuan').val(util.surat_usulan_kpknl_tujuan || '');
                    $('#surat_usulan_kpknl_isi').val(util.surat_usulan_kpknl_isi || '');

                    // SPTJM
                    $('#sptjm_nomor').val(util.sptjm_nomor || '');
                    $('#sptjm_tanggal').val(formatToInputDate(util.sptjm_tanggal));
                    $('#sptjm_kode_barang').val(util.sptjm_kode_barang || '');
                    $('#sptjm_nup').val(util.sptjm_nup || '');
                    $('#sptjm_luasan_sewa').val(util.sptjm_luasan_sewa || '');
                    $('#sptjm_lokasi_sewa').val(util.sptjm_lokasi_sewa || '');

                    // Surat Pernyataan
                    $('#surat_pernyataan_nomor').val(util.surat_pernyataan_nomor || '');
                    $('#surat_pernyataan_tanggal').val(formatToInputDate(util.surat_pernyataan_tanggal));
                    $('#surat_pernyataan_kode_barang').val(util.surat_pernyataan_kode_barang || '');
                    $('#surat_pernyataan_nup').val(util.surat_pernyataan_nup || '');
                    $('#surat_pernyataan_luasan_sewa').val(util.surat_pernyataan_luasan_sewa || '');
                    $('#surat_pernyataan_lokasi_sewa').val(util.surat_pernyataan_lokasi_sewa || '');

                    // Daftar BMN (Load dynamic table)
                    if (util.daftar_bmn) {
                        let bmnArray = [];
                        if (typeof util.daftar_bmn === 'string') {
                            try {
                                bmnArray = JSON.parse(util.daftar_bmn);
                            } catch (e) {
                                console.error('Failed to parse daftar_bmn:', e);
                            }
                        } else if (Array.isArray(util.daftar_bmn)) {
                            bmnArray = util.daftar_bmn;
                        }
                        loadBmnData(bmnArray);
                    }

                    // ===== TAB 4: PENILAIAN KPKNL =====
                    // Nodin Persetujuan KPKNL
                    $('#nodin_persetujuan_kpknl_nomor').val(util.nodin_persetujuan_kpknl_nomor || '');
                    $('#nodin_persetujuan_kpknl_tanggal').val(formatToInputDate(util.nodin_persetujuan_kpknl_tanggal));
                    $('#nodin_persetujuan_kpknl_tujuan').val(util.nodin_persetujuan_kpknl_tujuan || '');
                    $('#nodin_persetujuan_kpknl_nomor_persetujuan').val(util.nodin_persetujuan_kpknl_nomor_persetujuan || '');
                    $('#nodin_persetujuan_kpknl_tanggal_persetujuan').val(formatToInputDate(util.nodin_persetujuan_kpknl_tanggal_persetujuan));
                    $('#nodin_persetujuan_kpknl_periode_sewa').val(util.nodin_persetujuan_kpknl_periode_sewa || '');
                    $('#nodin_persetujuan_kpknl_nominal').val(util.nodin_persetujuan_kpknl_nominal || '');
                    $('#nodin_persetujuan_kpknl_mitra').val(util.nodin_persetujuan_kpknl_mitra || '');
                    $('#nodin_persetujuan_kpknl_kasub').val(util.nodin_persetujuan_kpknl_kasub || '');

                    // Surat Invoice
                    $('#surat_invoice_nomor').val(util.surat_invoice_nomor || '');
                    $('#surat_invoice_tanggal').val(formatToInputDate(util.surat_invoice_tanggal));
                    $('#surat_invoice_tujuan').val(util.surat_invoice_tujuan || '');
                    $('#surat_invoice_nomor_persetujuan').val(util.surat_invoice_nomor_persetujuan || '');
                    $('#surat_invoice_tanggal_persetujuan').val(formatToInputDate(util.surat_invoice_tanggal_persetujuan));
                    $('#surat_invoice_periode_sewa').val(util.surat_invoice_periode_sewa || '');
                    $('#surat_invoice_nominal').val(util.surat_invoice_nominal || '');
                    $('#surat_invoice_mitra').val(util.surat_invoice_mitra || '');
                    $('#surat_invoice_kasub').val(util.surat_invoice_kasub || '');

                    // ===== TAB 5: PERJANJIAN =====
                    // Detail Perjanjian
                    $('#perjanjian_mitra').val(util.perjanjian_mitra || '');
                    $('#perjanjian_peruntukan').val(util.perjanjian_peruntukan || '');
                    $('#perjanjian_gedung').val(util.perjanjian_gedung || '');
                    $('#perjanjian_hari_tanggal').val(util.perjanjian_hari_tanggal || '');
                    $('#perjanjian_nomor').val(util.perjanjian_nomor || '');
                    $('#perjanjian_tanggal_penandatanganan').val(formatToInputDate(util.perjanjian_tanggal_penandatanganan));
                    $('#jangka_waktu_nilai').val(util.jangka_waktu_nilai || '');
                    $('#jangka_waktu_satuan').val(util.jangka_waktu_satuan || '');
                    $('#perjanjian_detail_pihak_kedua').val(util.perjanjian_detail_pihak_kedua || '');

                    // Nodin TTD
                    $('#nodin_ttd_nomor').val(util.nodin_ttd_nomor || '');
                    $('#nodin_ttd_tanggal').val(formatToInputDate(util.nodin_ttd_tanggal));
                    $('#nodin_ttd_tujuan').val(util.nodin_ttd_tujuan || '');
                    $('#nodin_ttd_mitra').val(util.nodin_ttd_mitra || '');
                    $('#nodin_ttd_judul_perjanjian').val(util.nodin_ttd_judul_perjanjian || '');

                    // Nodin Internal
                    $('#nodin_internal_nomor').val(util.nodin_internal_nomor || '');
                    $('#nodin_internal_tanggal').val(formatToInputDate(util.nodin_internal_tanggal));
                    $('#nodin_internal_mitra').val(util.nodin_internal_mitra || '');
                    $('#nodin_internal_judul_perjanjian').val(util.nodin_internal_judul_perjanjian || '');
                    $('#nodin_internal_nomor_perjanjian').val(util.nodin_internal_nomor_perjanjian || '');
                    $('#nodin_internal_detail_persetujuan').val(util.nodin_internal_detail_persetujuan || '');

                    // ===== FILE LINKS =====
                    $('.file-view-link').empty(); // Clear previous links first

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

                    // Tab 2 file links
                    createLink(util.surat_konfirmasi_lampiran, 'view-surat_konfirmasi_lampiran', 'surat_konfirmasi_lampiran');
                    createLink(util.dokumen_surat_usulan_sewa, 'view-dokumen_surat_usulan_sewa', 'dokumen_surat_usulan_sewa');
                    createLink(util.dokumen_npwp, 'view-dokumen_npwp', 'dokumen_npwp');
                    createLink(util.dokumen_ktp_penandatangan, 'view-dokumen_ktp_penandatangan', 'dokumen_ktp_penandatangan');
                    createLink(util.dokumen_nib, 'view-dokumen_nib', 'dokumen_nib');

                    // Tab 3 file links
                    createLink(util.dokumen_psp, 'view-dokumen_psp', 'dokumen_psp');
                    createLink(util.dokumen_kib, 'view-dokumen_kib', 'dokumen_kib');
                    createLink(util.dokumen_usulan_ttd, 'view-dokumen_usulan_ttd', 'dokumen_usulan_ttd');

                    // Tab 4 file links
                    createLink(util.dokumen_jadwal_penilaian, 'view-dokumen_jadwal_penilaian', 'dokumen_jadwal_penilaian');
                    createLink(util.dokumen_basl, 'view-dokumen_basl', 'dokumen_basl');
                    createLink(util.dokumen_persetujuan_kpknl, 'view-dokumen_persetujuan_kpknl', 'dokumen_persetujuan_kpknl');
                    createLink(util.dokumen_kode_billing, 'view-dokumen_kode_billing', 'dokumen_kode_billing');

                    // Tab 5 file links
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

        // Complete data form submission
        $('#complete-data-form').on('submit', function(e) {
            e.preventDefault();
            const id = $('#complete_data_id').val();
            const formData = new FormData(this);
            formData.append('_method', 'PUT'); // Spoof PUT method for FormData

            // Serialize dynamic table data and append to formData
            const daftarBmnData = [];
            $('#daftarBmnBody tr').each(function() {
                const row = {
                    kode_barang: $(this).find('input[name="kode_barang[]"]').val(),
                    nup: $(this).find('input[name="nup[]"]').val(),
                    jenis_bmn: $(this).find('input[name="jenis_bmn[]"]').val(),
                    luas: $(this).find('input[name="luas[]"]').val(),
                    nilai: $(this).find('input[name="nilai[]"]').val(),
                    lokasi: $(this).find('input[name="lokasi[]"]').val(),
                    peruntukan: $(this).find('input[name="peruntukan[]"]').val(),
                };
                daftarBmnData.push(row);
            });
            formData.set('daftar_bmn', JSON.stringify(daftarBmnData));


            // Show loader
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');

            $.ajax({
                url: '{{ route("bmn.utilization.update", ":id") }}'.replace(':id', id),
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
                            text: 'Data pemanfaatan berhasil dilengkapi.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Gagal', 'Gagal melengkapi data pemanfaatan.', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    let errorMsg = 'Terjadi kesalahan saat melengkapi data.';
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        const firstError = Object.values(errors)[0][0];
                        errorMsg = `Gagal Validasi: ${firstError}`;
                    }
                    showError('Gagal!', errorMsg);
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
            clearBmnTable(); // Clear BMN table
        });

        // ========== DYNAMIC BMN TABLE FUNCTIONS ==========
        let bmnRowCounter = 0;
        let bmnData = [];

        function addBmnRow() {
            const row = `
                <tr>
                    <td></td>
                    <td><input type="text" class="form-control form-control-sm" name="kode_barang[]" data-field="kode_barang"></td>
                    <td><input type="text" class="form-control form-control-sm" name="nup[]" data-field="nup"></td>
                    <td><input type="text" class="form-control form-control-sm" name="jenis_bmn[]" data-field="jenis_bmn"></td>
                    <td><input type="number" class="form-control form-control-sm" name="luas[]" data-field="luas" step="0.01"></td>
                    <td><input type="number" class="form-control form-control-sm" name="nilai[]" data-field="nilai" step="0.01"></td>
                    <td><input type="text" class="form-control form-control-sm" name="lokasi[]" data-field="lokasi"></td>
                    <td><input type="text" class="form-control form-control-sm" name="peruntukan[]" data-field="peruntukan"></td>
                    <td><button type="button" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button></td>
                </tr>
            `;
            $('#daftarBmnBody').append(row);
            renumberBmnRows();
        }

        function removeBmnRow(rowId) {
            $(`#bmn-row-${rowId}`).remove();
            renumberBmnRows();
            saveBmnData();
        }

        function saveBmnData() {
            bmnData = [];
            $('#daftarBmnBody tr').each(function(index) {
                const rowData = {
                    no: index + 1,
                    kode_barang: $(this).find('[data-field="kode_barang"]').val() || '',
                    nup: $(this).find('[data-field="nup"]').val() || '',
                    jenis_bmn: $(this).find('[data-field="jenis_bmn"]').val() || '',
                    luas: $(this).find('[data-field="luas"]').val() || '',
                    nilai: $(this).find('[data-field="nilai"]').val() || '',
                    lokasi: $(this).find('[data-field="lokasi"]').val() || '',
                    peruntukan: $(this).find('[data-field="peruntukan"]').val() || ''
                };
                bmnData.push(rowData);
            });
            $('#daftar_bmn').val(JSON.stringify(bmnData));
        }

        function loadBmnData(dataArray) {
            clearBmnTable();
            if (dataArray && Array.isArray(dataArray)) {
                dataArray.forEach((item) => {
                    bmnRowCounter++;
                    const row = `
                        <tr id="bmn-row-${bmnRowCounter}">
                            <td>${item.no || bmnRowCounter}</td>
                            <td><input type="text" class="form-control form-control-sm" name="kode_barang[]" data-field="kode_barang" value="${item.kode_barang || ''}"></td>
                            <td><input type="text" class="form-control form-control-sm" name="nup[]" data-field="nup" value="${item.nup || ''}"></td>
                            <td><input type="text" class="form-control form-control-sm" name="jenis_bmn[]" data-field="jenis_bmn" value="${item.jenis_bmn || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="luas[]" data-field="luas" step="0.01" value="${item.luas || ''}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="nilai[]" data-field="nilai" step="0.01" value="${item.nilai || ''}"></td>
                            <td><input type="text" class="form-control form-control-sm" name="lokasi[]" data-field="lokasi" value="${item.lokasi || ''}"></td>
                            <td><input type="text" class="form-control form-control-sm" name="peruntukan[]" data-field="peruntukan" value="${item.peruntukan || ''}"></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeBmnRow(${bmnRowCounter})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    $('#daftarBmnBody').append(row);
                });
            }
        }

        function clearBmnTable() {
            $('#daftarBmnBody').empty();
            bmnRowCounter = 0;
            bmnData = [];
        }

        // Auto-save BMN data when inputs change
        $(document).on('input', '#daftarBmnBody input', function() {
            saveBmnData();
        });

        function renumberBmnRows() {
            $('#daftarBmnBody tr').each(function(index) {
                const newRowId = index + 1;
                // Update the number in the first cell
                $(this).find('td:first').text(newRowId);
                
                // Update the row's ID
                $(this).attr('id', `bmn-row-${newRowId}`);
                
                // Update the onclick attribute of the delete button
                $(this).find('.btn-danger').attr('onclick', `removeBmnRow(${newRowId})`);
            });
            // Update the global counter
            bmnRowCounter = $('#daftarBmnBody tr').length;
        }

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
</body>
</html>