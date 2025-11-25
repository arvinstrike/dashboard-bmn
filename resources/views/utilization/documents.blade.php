<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dokumen Pemanfaatan BMN - {{ $utilization->nama_mitra_penyewa }}</title>

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

        /* Background Image with Subtle Overlay - Same as utilization dashboard */
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

        .status-ready {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 1.5px solid #6ee7b7;
        }

        .status-missing {
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

        .btn-secondary {
            background: var(--gray-500);
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: var(--gray-600);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(107, 114, 128, 0.3);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(79, 70, 229, 0.3);
        }

        .btn-outline-success {
            border: 2px solid var(--success-color);
            color: var(--success-color);
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-success:hover {
            background: var(--success-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
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

        /* Card styling for document categories */
        .document-category-card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(10px) saturate(180%);
            -webkit-backdrop-filter: blur(10px) saturate(180%);
            border-radius: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow:
                0 4px 20px rgba(0, 0, 0, 0.08),
                0 0 0 1px rgba(79, 70, 229, 0.08);
            border: 1px solid rgba(79, 70, 229, 0.1);
            overflow: hidden;
        }

        .category-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            padding: 1.5rem;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .document-item {
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            transition: all 0.2s ease;
        }

        .document-item:last-child {
            border-bottom: none;
        }

        .document-item:hover {
            background-color: rgba(243, 244, 246, 0.5);
        }

        .document-title {
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
        }

        .document-title i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }

        .document-info {
            color: var(--gray-600);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .document-info i {
            margin-right: 0.25rem;
        }

        .disabled-document {
            opacity: 0.6;
            background-color: rgba(249, 250, 251, 0.5);
        }

        .disabled-document:hover {
            background-color: rgba(249, 250, 251, 0.5);
        }

        .progress-bar {
            height: 8px;
            border-radius: 4px;
        }

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

            .page-header {
                padding: 1.5rem;
            }

            .table-container {
                padding: 1.5rem;
            }

            .document-item {
                padding: 1rem;
            }
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
                        <i class="bi bi-files text-primary-dark me-2"></i>Dokumen Pemanfaatan BMN
                    </h1>
                    <p class="mb-0">Manajemen dokumen untuk pemanfaatan Barang Milik Negara</p>
                </div>
                <a href="/utilization-dashboard" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mb-4">
            <div class="col">
                <div class="stat-card stat-card-primary h-100">
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-building"></i>
                    </div>
                    <p class="stat-title">Nama Mitra</p>
                    <p class="stat-value" style="font-size: 1.5rem;">{{ $utilization->nama_mitra_penyewa ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="col">
                <div class="stat-card stat-card-success h-100">
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-tag"></i>
                    </div>
                    <p class="stat-title">Jenis Mitra</p>
                    <p class="stat-value">{{ $utilization->jenis_mitra ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="col">
                <div class="stat-card stat-card-info h-100">
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-file-text"></i>
                    </div>
                    <p class="stat-title">Jenis Usulan</p>
                    <p class="stat-value">{{ $utilization->jenis_usulan ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="col">
                <div class="stat-card stat-card-warning h-100">
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <p class="stat-title">Dokumen Siap</p>
                    <p class="stat-value">{{ $readyCount ?? 0 }}/12</p>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="table-container">
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <h6 class="mb-0">Progress Dokumen</h6>
                    <span class="text-primary-dark fw-bold">{{ number_format(($readyCount ?? 0) / 12 * 100, 1) }}%</span>
                </div>
                <div class="progress" style="height: 8px; background-color: var(--gray-200);">
                    <div class="progress-bar bg-success" role="progressbar"
                         style="width: {{ number_format(($readyCount ?? 0) / 12 * 100, 1) }}%"
                         aria-valuenow="{{ $readyCount ?? 0 }}"
                         aria-valuemin="0"
                         aria-valuemax="12">
                    </div>
                </div>
            </div>

            <!-- Download All Button -->
            <div class="text-center mb-4">
                <button class="btn btn-outline-success" onclick="generateAllDocuments({{ $utilization->id }})">
                    <i class="bi bi-download me-2"></i>Download Semua Dokumen (ZIP)
                </button>
            </div>

            <!-- Document Categories -->
            <div class="table-header">
                <h3>
                    <i class="bi bi-folder2-open text-primary-dark me-2"></i>Daftar Dokumen
                </h3>
                <p class="mb-0">Klik tombol "Generate PDF" untuk membuat dokumen sesuai data yang tersedia</p>
            </div>

            <!-- KATEGORI 1: KONFIRMASI -->
            <div class="document-category-card">
                <div class="category-header">
                    <i class="bi bi-folder2-open me-2"></i>KATEGORI 1: KONFIRMASI
                </div>
                <div class="category-body">
                    <!-- 1. Surat Konfirmasi -->
                    <div class="document-item {{ $documentStatus['surat_konfirmasi_perpanjangan_sewa'] === 'missing' ? 'disabled-document' : '' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="document-title">
                                    <i class="bi bi-file-earmark-text"></i>
                                    1. Surat Konfirmasi Perpanjangan Sewa
                                </div>
                                <div class="document-info">
                                    @if($documentStatus['surat_konfirmasi_perpanjangan_sewa'] === 'ready')
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        <span class="text-success">Nomor: {{ $utilization->surat_konfirmasi_nomor ?? '-' }}</span>
                                    @else
                                        <i class="bi bi-exclamation-circle-fill text-danger"></i>
                                        <span class="text-danger">Data belum lengkap</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                @if($documentStatus['surat_konfirmasi_perpanjangan_sewa'] === 'ready')
                                    <span class="status-badge status-ready me-2">
                                        <i class="bi bi-check-circle-fill"></i> Siap
                                    </span>
                                    <button class="btn btn-sm btn-outline-primary me-1"
                                            data-bs-toggle="modal" data-bs-target="#modal-surat-konfirmasi-perpanjangan-sewa">
                                        <i class="bi bi-pencil-square me-1"></i>Edit Data
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm"
                                            onclick="generateDocument({{ $utilization->id }}, 'surat_konfirmasi_perpanjangan_sewa')">
                                        <i class="bi bi-file-earmark-text me-1"></i>Generate Dokumen
                                    </button>
                                @else
                                    <span class="status-badge status-missing me-2">
                                        <i class="bi bi-exclamation-circle-fill"></i> Belum Lengkap
                                    </span>
                                    <button class="btn btn-outline-warning btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#modal-surat-konfirmasi-perpanjangan-sewa">
                                        <i class="bi bi-pencil-square me-1"></i>Isi Data
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 2. Nodin Konfirmasi -->
                    <div class="document-item {{ $documentStatus['nodin_konfirmasi'] === 'missing' ? 'disabled-document' : '' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="document-title">
                                    <i class="bi bi-file-earmark-text"></i>
                                    2. Nodin Konfirmasi Perpanjangan Sewa
                                </div>
                                <div class="document-info">
                                    @if($documentStatus['nodin_konfirmasi'] === 'ready')
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        <span class="text-success">Nomor: {{ $utilization->nodin_konfirmasi_nomor ?? '-' }}</span>
                                    @else
                                        <i class="bi bi-exclamation-circle-fill text-danger"></i>
                                        <span class="text-danger">Data belum lengkap</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                @if($documentStatus['nodin_konfirmasi'] === 'ready')
                                    <span class="status-badge status-ready me-2">
                                        <i class="bi bi-check-circle-fill"></i> Siap
                                    </span>
                                    <button class="btn btn-sm btn-outline-primary me-1"
                                            data-bs-toggle="modal" data-bs-target="#modal-nodin-konfirmasi">
                                        <i class="bi bi-pencil-square me-1"></i>Edit Data
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm"
                                            onclick="generateDocument({{ $utilization->id }}, 'nodin_konfirmasi')">
                                        <i class="bi bi-file-earmark-text me-1"></i>Generate Dokumen
                                    </button>
                                @else
                                    <span class="status-badge status-missing me-2">
                                        <i class="bi bi-exclamation-circle-fill"></i> Belum Lengkap
                                    </span>
                                    <button class="btn btn-outline-warning btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#modal-nodin-konfirmasi">
                                        <i class="bi bi-pencil-square me-1"></i>Isi Data
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>


                </div>
            </div>

            <!-- KATEGORI 2: USULAN PEMANFAATAN -->
            <div class="document-category-card">
                <div class="category-header">
                    <i class="bi bi-folder2-open me-2"></i>KATEGORI 2: USULAN PEMANFAATAN
                </div>
                <div class="category-body">
                    <!-- 3. Nodin Berjenjang -->
                    <div class="document-item {{ $documentStatus['nodin_berjenjang'] === 'missing' ? 'disabled-document' : '' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="document-title">
                                    <i class="bi bi-file-earmark-text"></i>
                                    4. Nodin Berjenjang
                                </div>
                                <div class="document-info">
                                    @if($documentStatus['nodin_berjenjang'] === 'ready')
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        <span class="text-success">Mitra: {{ $utilization->nodin_berjenjang_mitra ?? '-' }}</span>
                                    @else
                                        <i class="bi bi-exclamation-circle-fill text-danger"></i>
                                        <span class="text-danger">Data belum lengkap</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                @if($documentStatus['nodin_berjenjang'] === 'ready')
                                    <span class="status-badge status-ready me-2">
                                        <i class="bi bi-check-circle-fill"></i> Siap
                                    </span>
                                    <button class="btn btn-sm btn-outline-primary me-1"
                                            data-bs-toggle="modal" data-bs-target="#modal-nodin-berjenjang">
                                        <i class="bi bi-pencil-square me-1"></i>Edit Data
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm"
                                            onclick="generateDocument({{ $utilization->id }}, 'nodin_berjenjang')">
                                        <i class="bi bi-file-earmark-text me-1"></i>Generate Dokumen
                                    </button>
                                @else
                                    <span class="status-badge status-missing me-2">
                                        <i class="bi bi-exclamation-circle-fill"></i> Belum Lengkap
                                    </span>
                                    <button class="btn btn-outline-warning btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#modal-nodin-berjenjang">
                                        <i class="bi bi-pencil-square me-1"></i>Isi Data
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 5. Surat Usulan KPKNL & SPTJM -->
                    <div class="document-item {{ $documentStatus['surat_usulan_kpknl'] === 'missing' ? 'disabled-document' : '' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="document-title">
                                    <i class="bi bi-file-earmark-text"></i>
                                    5. Surat Usulan Sewa KPKNL & SPTJM
                                </div>
                                <div class="document-info">
                                    @if($documentStatus['surat_usulan_kpknl'] === 'ready')
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        <span class="text-success">Nomor: {{ $utilization->surat_usulan_kpknl_nomor ?? '-' }}</span>
                                    @else
                                        <i class="bi bi-exclamation-circle-fill text-danger"></i>
                                        <span class="text-danger">Data belum lengkap</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                @if($documentStatus['surat_usulan_kpknl'] === 'ready')
                                    <span class="status-badge status-ready me-2">
                                        <i class="bi bi-check-circle-fill"></i> Siap
                                    </span>
                                    <button class="btn btn-sm btn-outline-primary me-1"
                                            data-bs-toggle="modal" data-bs-target="#modal-surat-usulan-kpknl">
                                        <i class="bi bi-pencil-square me-1"></i>Edit Data
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm"
                                            onclick="generateDocument({{ $utilization->id }}, 'surat_usulan_kpknl')">
                                        <i class="bi bi-file-earmark-text me-1"></i>Generate Dokumen
                                    </button>
                                @else
                                    <span class="status-badge status-missing me-2">
                                        <i class="bi bi-exclamation-circle-fill"></i> Belum Lengkap
                                    </span>
                                    <button class="btn btn-outline-warning btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#modal-surat-usulan-kpknl">
                                        <i class="bi bi-pencil-square me-1"></i>Isi Data
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 6. Surat Pernyataan -->
                    <div class="document-item {{ $documentStatus['surat_pernyataan'] === 'missing' ? 'disabled-document' : '' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="document-title">
                                    <i class="bi bi-file-earmark-text"></i>
                                    6. Surat Pernyataan
                                </div>
                                <div class="document-info">
                                    @if($documentStatus['surat_pernyataan'] === 'ready')
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        <span class="text-success">Nomor: {{ $utilization->surat_pernyataan_nomor ?? '-' }}</span>
                                    @else
                                        <i class="bi bi-exclamation-circle-fill text-danger"></i>
                                        <span class="text-danger">Data belum lengkap</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                @if($documentStatus['surat_pernyataan'] === 'ready')
                                    <span class="status-badge status-ready me-2">
                                        <i class="bi bi-check-circle-fill"></i> Siap
                                    </span>
                                    <button class="btn btn-sm btn-outline-primary me-1"
                                            data-bs-toggle="modal" data-bs-target="#modal-surat-pernyataan">
                                        <i class="bi bi-pencil-square me-1"></i>Edit Data
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm"
                                            onclick="generateDocument({{ $utilization->id }}, 'surat_pernyataan')">
                                        <i class="bi bi-file-earmark-text me-1"></i>Generate Dokumen
                                    </button>
                                @else
                                    <span class="status-badge status-missing me-2">
                                        <i class="bi bi-exclamation-circle-fill"></i> Belum Lengkap
                                    </span>
                                    <button class="btn btn-outline-warning btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#modal-surat-pernyataan">
                                        <i class="bi bi-pencil-square me-1"></i>Isi Data
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 7. Daftar BMN -->
                    <div class="document-item {{ $documentStatus['daftar_bmn'] === 'missing' ? 'disabled-document' : '' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="document-title">
                                    <i class="bi bi-file-earmark-spreadsheet text-success"></i>
                                    7. Daftar BMN yang Diusulkan
                                </div>
                                <div class="document-info">
                                    @if($documentStatus['daftar_bmn'] === 'ready')
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        <span class="text-success">Daftar BMN tersedia</span>
                                    @else
                                        <i class="bi bi-exclamation-circle-fill text-danger"></i>
                                        <span class="text-danger">Daftar BMN belum diisi</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                @if($documentStatus['daftar_bmn'] === 'ready')
                                    <span class="status-badge status-ready me-2">
                                        <i class="bi bi-check-circle-fill"></i> Siap
                                    </span>
                                    <button class="btn btn-sm btn-outline-primary me-1"
                                            data-bs-toggle="modal" data-bs-target="#modal-daftar-bmn">
                                        <i class="bi bi-pencil-square me-1"></i>Edit Data
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm"
                                            onclick="generateDocument({{ $utilization->id }}, 'daftar_bmn')">
                                        <i class="bi bi-file-earmark-text me-1"></i>Generate Dokumen
                                    </button>
                                @else
                                    <span class="status-badge status-missing me-2">
                                        <i class="bi bi-exclamation-circle-fill"></i> Belum Lengkap
                                    </span>
                                    <button class="btn btn-outline-warning btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#modal-daftar-bmn">
                                        <i class="bi bi-pencil-square me-1"></i>Isi Data
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KATEGORI 3: PENILAIAN KPKNL -->
            <div class="document-category-card">
                <div class="category-header">
                    <i class="bi bi-folder2-open me-2"></i>KATEGORI 3: PENILAIAN KPKNL
                </div>
                <div class="category-body">
                    <!-- 8. Nodin Persetujuan KPKNL -->
                    <div class="document-item {{ $documentStatus['nodin_persetujuan_kpknl'] === 'missing' ? 'disabled-document' : '' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="document-title">
                                    <i class="bi bi-file-earmark-text"></i>
                                    8. Nodin Persetujuan KPKNL
                                </div>
                                <div class="document-info">
                                    @if($documentStatus['nodin_persetujuan_kpknl'] === 'ready')
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        <span class="text-success">Nomor: {{ $utilization->nodin_persetujuan_kpknl_nomor ?? '-' }}</span>
                                    @else
                                        <i class="bi bi-exclamation-circle-fill text-danger"></i>
                                        <span class="text-danger">Data belum lengkap</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                @if($documentStatus['nodin_persetujuan_kpknl'] === 'ready')
                                    <span class="status-badge status-ready me-2">
                                        <i class="bi bi-check-circle-fill"></i> Siap
                                    </span>
                                    <button class="btn btn-sm btn-outline-primary me-1"
                                            data-bs-toggle="modal" data-bs-target="#modal-nodin-persetujuan-kpknl">
                                        <i class="bi bi-pencil-square me-1"></i>Edit Data
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm"
                                            onclick="generateDocument({{ $utilization->id }}, 'nodin_persetujuan_kpknl')">
                                        <i class="bi bi-file-earmark-text me-1"></i>Generate Dokumen
                                    </button>
                                @else
                                    <span class="status-badge status-missing me-2">
                                        <i class="bi bi-exclamation-circle-fill"></i> Belum Lengkap
                                    </span>
                                    <button class="btn btn-outline-warning btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#modal-nodin-persetujuan-kpknl">
                                        <i class="bi bi-pencil-square me-1"></i>Isi Data
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 9. Surat Invoice -->
                    <div class="document-item {{ $documentStatus['surat_invoice'] === 'missing' ? 'disabled-document' : '' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="document-title">
                                    <i class="bi bi-file-earmark-text text-warning"></i>
                                    9. Surat Invoice
                                </div>
                                <div class="document-info">
                                    @if($documentStatus['surat_invoice'] === 'ready')
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        <span class="text-success">Nomor: {{ $utilization->surat_invoice_nomor ?? '-' }}</span>
                                    @else
                                        <i class="bi bi-exclamation-circle-fill text-danger"></i>
                                        <span class="text-danger">Data belum lengkap</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                @if($documentStatus['surat_invoice'] === 'ready')
                                    <span class="status-badge status-ready me-2">
                                        <i class="bi bi-check-circle-fill"></i> Siap
                                    </span>
                                    <button class="btn btn-sm btn-outline-primary me-1"
                                            data-bs-toggle="modal" data-bs-target="#modal-surat-invoice">
                                        <i class="bi bi-pencil-square me-1"></i>Edit Data
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm"
                                            onclick="generateDocument({{ $utilization->id }}, 'surat_invoice')">
                                        <i class="bi bi-file-earmark-text me-1"></i>Generate Dokumen
                                    </button>
                                @else
                                    <span class="status-badge status-missing me-2">
                                        <i class="bi bi-exclamation-circle-fill"></i> Belum Lengkap
                                    </span>
                                    <button class="btn btn-outline-warning btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#modal-surat-invoice">
                                        <i class="bi bi-pencil-square me-1"></i>Isi Data
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KATEGORI 4: PERJANJIAN -->
            <div class="document-category-card">
                <div class="category-header">
                    <i class="bi bi-folder2-open me-2"></i>KATEGORI 4: PERJANJIAN
                </div>
                <div class="category-body">
                    <!-- 10. Nodin TTD -->
                    <div class="document-item {{ $documentStatus['nodin_ttd'] === 'missing' ? 'disabled-document' : '' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="document-title">
                                    <i class="bi bi-file-earmark-text"></i>
                                    10. Nodin TTD (Permohonan TTD Perjanjian)
                                </div>
                                <div class="document-info">
                                    @if($documentStatus['nodin_ttd'] === 'ready')
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        <span class="text-success">Nomor: {{ $utilization->nodin_ttd_nomor ?? '-' }}</span>
                                    @else
                                        <i class="bi bi-exclamation-circle-fill text-danger"></i>
                                        <span class="text-danger">Data belum lengkap</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                @if($documentStatus['nodin_ttd'] === 'ready')
                                    <span class="status-badge status-ready me-2">
                                        <i class="bi bi-check-circle-fill"></i> Siap
                                    </span>
                                    <button class="btn btn-sm btn-outline-primary me-1"
                                            data-bs-toggle="modal" data-bs-target="#modal-nodin-ttd">
                                        <i class="bi bi-pencil-square me-1"></i>Edit Data
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm"
                                            onclick="generateDocument({{ $utilization->id }}, 'nodin_ttd')">
                                        <i class="bi bi-file-earmark-text me-1"></i>Generate Dokumen
                                    </button>
                                @else
                                    <span class="status-badge status-missing me-2">
                                        <i class="bi bi-exclamation-circle-fill"></i> Belum Lengkap
                                    </span>
                                    <button class="btn btn-outline-warning btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#modal-nodin-ttd">
                                        <i class="bi bi-pencil-square me-1"></i>Isi Data
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 11. Nodin Internal -->
                    <div class="document-item {{ $documentStatus['nodin_internal'] === 'missing' ? 'disabled-document' : '' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="document-title">
                                    <i class="bi bi-file-earmark-text"></i>
                                    11. Nodin Internal (Berjenjang Internal)
                                </div>
                                <div class="document-info">
                                    @if($documentStatus['nodin_internal'] === 'ready')
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        <span class="text-success">Nomor: {{ $utilization->nodin_internal_nomor ?? '-' }}</span>
                                    @else
                                        <i class="bi bi-exclamation-circle-fill text-danger"></i>
                                        <span class="text-danger">Data belum lengkap</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                @if($documentStatus['nodin_internal'] === 'ready')
                                    <span class="status-badge status-ready me-2">
                                        <i class="bi bi-check-circle-fill"></i> Siap
                                    </span>
                                    <button class="btn btn-sm btn-outline-primary me-1"
                                            data-bs-toggle="modal" data-bs-target="#modal-nodin-internal">
                                        <i class="bi bi-pencil-square me-1"></i>Edit Data
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm"
                                            onclick="generateDocument({{ $utilization->id }}, 'nodin_internal')">
                                        <i class="bi bi-file-earmark-text me-1"></i>Generate Dokumen
                                    </button>
                                @else
                                    <span class="status-badge status-missing me-2">
                                        <i class="bi bi-exclamation-circle-fill"></i> Belum Lengkap
                                    </span>
                                    <button class="btn btn-outline-warning btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#modal-nodin-internal">
                                        <i class="bi bi-pencil-square me-1"></i>Isi Data
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 12. Perjanjian Sewa -->
                    <div class="document-item {{ $documentStatus['perjanjian'] === 'missing' ? 'disabled-document' : '' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="document-title">
                                    <i class="bi bi-file-earmark-ruled text-danger"></i>
                                    12. Perjanjian Sewa
                                </div>
                                <div class="document-info">
                                    @if($documentStatus['perjanjian'] === 'ready')
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        <span class="text-success">Nomor: {{ $utilization->perjanjian_nomor ?? '-' }}</span>
                                    @else
                                        <i class="bi bi-exclamation-circle-fill text-danger"></i>
                                        <span class="text-danger">Data belum lengkap</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                @if($documentStatus['perjanjian'] === 'ready')
                                    <span class="status-badge status-ready me-2">
                                        <i class="bi bi-check-circle-fill"></i> Siap
                                    </span>
                                    <button class="btn btn-sm btn-outline-primary me-1"
                                            data-bs-toggle="modal" data-bs-target="#modal-perjanjian-sewa">
                                        <i class="bi bi-pencil-square me-1"></i>Edit Data
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm"
                                            onclick="generateDocument({{ $utilization->id }}, 'perjanjian')">
                                        <i class="bi bi-file-earmark-text me-1"></i>Generate Dokumen
                                    </button>
                                @else
                                    <span class="status-badge status-missing me-2">
                                        <i class="bi bi-exclamation-circle-fill"></i> Belum Lengkap
                                    </span>
                                    <button class="btn btn-outline-warning btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#modal-perjanjian-sewa">
                                        <i class="bi bi-pencil-square me-1"></i>Isi Data
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Note -->
            <div class="text-center py-4">
                <p class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Untuk melengkapi data dokumen, klik tombol "Lengkapi Data" di dashboard utama
                </p>
            </div>
        </div>
    </div>

    <!-- MODALS FOR DOCUMENT DATA INPUT -->



    <!-- Modal 2: Nodin Konfirmasi -->
    <div class="modal fade" id="modal-nodin-konfirmasi" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Isi Data: Nodin Konfirmasi Perpanjangan Sewa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-nodin-konfirmasi">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Nodin <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nodin_konfirmasi_nomor" value="{{ $utilization->nodin_konfirmasi_nomor ?? '' }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Nodin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="nodin_konfirmasi_tanggal" value="{{ $utilization->nodin_konfirmasi_tanggal ?? '' }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mitra/Peruntukan</label>
                            <input type="text" class="form-control" name="nodin_konfirmasi_mitra_peruntukan" value="{{ $utilization->nodin_konfirmasi_mitra_peruntukan ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Berakhir Sewa</label>
                            <input type="date" class="form-control" name="nodin_konfirmasi_tanggal_berakhir_sewa" value="{{ $utilization->nodin_konfirmasi_tanggal_berakhir_sewa ?? '' }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-surat-konfirmasi-perpanjangan-sewa" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Isi Data: Surat Konfirmasi Perpanjangan Sewa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-surat-konfirmasi-perpanjangan-sewa">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Surat <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="surat_konfirmasi_nomor" value="{{ $utilization->surat_konfirmasi_nomor ?? '' }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="surat_konfirmasi_tanggal" 
                                    value="{{ $utilization->surat_konfirmasi_tanggal ? \Carbon\Carbon::parse($utilization->surat_konfirmasi_tanggal)->format('Y-m-d') : '' }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tujuan</label>
                            <input type="text" class="form-control" name="surat_konfirmasi_tujuan_surat" value="{{ $utilization->surat_konfirmasi_tujuan_surat ?? ($utilization->surat_konfirmasi_tujuan ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Peruntukan</label>
                            <input type="text" class="form-control" name="surat_konfirmasi_peruntukan_surat" value="{{ $utilization->surat_konfirmasi_peruntukan_surat ?? ($utilization->surat_konfirmasi_peruntukan ?? '') }}">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Perjanjian Lama (Mitra)</label>
                                <input type="text" class="form-control" name="surat_konfirmasi_nomor_perjanjian_lama_mitra" value="{{ $utilization->surat_konfirmasi_nomor_perjanjian_lama_mitra ?? ($utilization->surat_konfirmasi_nomor_perjanjian_lama ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Perjanjian Lama (DPR)</label>
                                <input type="text" class="form-control" name="surat_konfirmasi_nomor_perjanjian_lama_dpr" value="{{ $utilization->surat_konfirmasi_nomor_perjanjian_lama_dpr ?? '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Berakhir</label>
                                <input type="date" class="form-control" name="surat_konfirmasi_tanggal_berakhir" 
                                    value="{{ $utilization->surat_konfirmasi_tanggal_berakhir ? \Carbon\Carbon::parse($utilization->surat_konfirmasi_tanggal_berakhir)->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Konfirmasi Terakhir</label>
                                <input type="date" class="form-control" name="surat_konfirmasi_tanggal_konfirmasi_terakhir" 
                                    value="{{ $utilization->surat_konfirmasi_tanggal_konfirmasi_terakhir ? \Carbon\Carbon::parse($utilization->surat_konfirmasi_tanggal_konfirmasi_terakhir)->format('Y-m-d') : '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Kasub</label>
                                <input type="text" class="form-control" name="surat_konfirmasi_kasub_nama" value="{{ $utilization->surat_konfirmasi_kasub_nama ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Kasub</label>
                                <input type="text" class="form-control" name="surat_konfirmasi_kasub_nomor" value="{{ $utilization->surat_konfirmasi_kasub_nomor ?? '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 3: Nodin Berjenjang -->
    <div class="modal fade" id="modal-nodin-berjenjang" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Isi Data: Nodin Berjenjang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-nodin-berjenjang">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Nodin <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nodin_berjenjang_nomor" value="{{ $utilization->nodin_berjenjang_nomor ?? '' }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Nodin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="nodin_berjenjang_tanggal" value="{{ $utilization->nodin_berjenjang_tanggal ?? '' }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="nodin_berjenjang_tanggal_mulai" value="{{ $utilization->nodin_berjenjang_tanggal_mulai ?? '' }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="nodin_berjenjang_tanggal_selesai" value="{{ $utilization->nodin_berjenjang_tanggal_selesai ?? '' }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Mitra</label>
                            <input type="text" class="form-control" name="nodin_berjenjang_mitra" value="{{ $utilization->nodin_berjenjang_mitra ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Peruntukan</label>
                            <input type="text" class="form-control" name="nodin_berjenjang_peruntukan" value="{{ $utilization->nodin_berjenjang_peruntukan ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nominal (Rp)</label>
                            <input type="number" class="form-control" name="nodin_berjenjang_nominal" id="nodin_berjenjang_nominal" value="{{ $utilization->nodin_berjenjang_nominal ?? '' }}">
                            <small class="text-muted" id="nodin_berjenjang_nominal_terbilang"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 5: Surat Usulan KPKNL & SPTJM -->
    <div class="modal fade" id="modal-surat-usulan-kpknl" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Isi Data: Surat Usulan Sewa KPKNL & SPTJM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-surat-usulan-kpknl">
                    <div class="modal-body">
                        <h6 class="text-primary mb-3">Data Surat Usulan KPKNL</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Surat KPKNL <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="surat_usulan_kpknl_nomor" value="{{ $utilization->surat_usulan_kpknl_nomor ?? '' }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="surat_usulan_kpknl_tanggal" value="{{ $utilization->surat_usulan_kpknl_tanggal ? \Carbon\Carbon::parse($utilization->surat_usulan_kpknl_tanggal)->format('Y-m-d') : '' }}" required>
                            </div>
                        </div>
                        <div class="row">
                             <div class="col-md-6 mb-3">
                                <label class="form-label">Peruntukan</label>
                                <input type="text" class="form-control" name="surat_usulan_kpknl_peruntukan" value="{{ $utilization->surat_usulan_kpknl_peruntukan ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Berakhir KPKNL</label>
                                <input type="date" class="form-control" name="surat_usulan_kpknl_tanggal_berakhir" value="{{ $utilization->surat_usulan_kpknl_tanggal_berakhir ? \Carbon\Carbon::parse($utilization->surat_usulan_kpknl_tanggal_berakhir)->format('Y-m-d') : '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Kasubag</label>
                                <input type="text" class="form-control" name="surat_usulan_kpknl_nama_kasubag" value="{{ $utilization->surat_usulan_kpknl_nama_kasubag ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Kasubag</label>
                                <input type="text" class="form-control" name="surat_usulan_kpknl_nomor_kasubag" value="{{ $utilization->surat_usulan_kpknl_nomor_kasubag ?? '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Tujuan Surat</label>
                                <input type="text" class="form-control" name="surat_usulan_kpknl_tujuan" value="{{ $utilization->surat_usulan_kpknl_tujuan ?? '' }}">
                            </div>
                        </div>

                        <h6 class="text-primary mb-3 mt-4">Data SPTJM</h6>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Nomor Surat SPTJM</label>
                                <input type="text" class="form-control" name="sptjm_nomor" value="{{ $utilization->sptjm_nomor ?? '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Barang</label>
                                <input type="text" class="form-control" name="sptjm_kode_barang" value="{{ $utilization->sptjm_kode_barang ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NUP</label>
                                <input type="text" class="form-control" name="sptjm_nup" value="{{ $utilization->sptjm_nup ?? '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Luas Bangunan</label>
                                <input type="text" class="form-control" name="sptjm_luasan_sewa" value="{{ $utilization->sptjm_luasan_sewa ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Lokasi</label>
                                <input type="text" class="form-control" name="sptjm_lokasi_sewa" value="{{ $utilization->sptjm_lokasi_sewa ?? '' }}">
                            </div>
                        </div>
                        <!-- Hidden fields for compatibility if needed -->
                        <input type="hidden" name="sptjm_tanggal" value="{{ $utilization->sptjm_tanggal ? \Carbon\Carbon::parse($utilization->sptjm_tanggal)->format('Y-m-d') : date('Y-m-d') }}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 6: Surat Pernyataan -->
    <div class="modal fade" id="modal-surat-pernyataan" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Isi Data: Surat Pernyataan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-surat-pernyataan">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Surat <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="surat_pernyataan_nomor" value="{{ $utilization->surat_pernyataan_nomor ?? '' }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="surat_pernyataan_tanggal" value="{{ $utilization->surat_pernyataan_tanggal ?? '' }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Barang</label>
                                <input type="text" class="form-control" name="surat_pernyataan_kode_barang" value="{{ $utilization->surat_pernyataan_kode_barang ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NUP</label>
                                <input type="text" class="form-control" name="surat_pernyataan_nup" value="{{ $utilization->surat_pernyataan_nup ?? '' }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Luasan Sewa</label>
                            <input type="text" class="form-control" name="surat_pernyataan_luasan_sewa" value="{{ $utilization->surat_pernyataan_luasan_sewa ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lokasi Sewa</label>
                            <input type="text" class="form-control" name="surat_pernyataan_lokasi_sewa" value="{{ $utilization->surat_pernyataan_lokasi_sewa ?? '' }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 7: Daftar BMN -->
    <div class="modal fade" id="modal-daftar-bmn" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Isi Data: Daftar BMN yang Diusulkan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-daftar-bmn">
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="daftar-bmn-table">
                                <thead>
                                    <tr>
                                        <th>Kode Barang</th>
                                        <th>NUP</th>
                                        <th>Jenis</th>
                                        <th>Luas (m²)</th>
                                        <th>Nilai (Rp)</th>
                                        <th>Lokasi</th>
                                        <th>Peruntukan</th>
                                        <th width="50">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="daftar-bmn-tbody">
                                    @if(isset($utilization->daftar_bmn) && is_array(json_decode($utilization->daftar_bmn, true)))
                                        @foreach(json_decode($utilization->daftar_bmn, true) as $index => $bmn)
                                            <tr>
                                                <td><input type="text" class="form-control form-control-sm" name="daftar_bmn[{{ $index }}][kode_barang]" value="{{ $bmn['kode_barang'] ?? '' }}"></td>
                                                <td><input type="text" class="form-control form-control-sm" name="daftar_bmn[{{ $index }}][nup]" value="{{ $bmn['nup'] ?? '' }}"></td>
                                                <td><input type="text" class="form-control form-control-sm" name="daftar_bmn[{{ $index }}][jenis]" value="{{ $bmn['jenis'] ?? '' }}"></td>
                                                <td><input type="text" class="form-control form-control-sm" name="daftar_bmn[{{ $index }}][luas]" value="{{ $bmn['luas'] ?? '' }}"></td>
                                                <td><input type="number" class="form-control form-control-sm" name="daftar_bmn[{{ $index }}][nilai]" value="{{ $bmn['nilai'] ?? '' }}"></td>
                                                <td><input type="text" class="form-control form-control-sm" name="daftar_bmn[{{ $index }}][lokasi]" value="{{ $bmn['lokasi'] ?? '' }}"></td>
                                                <td><input type="text" class="form-control form-control-sm" name="daftar_bmn[{{ $index }}][peruntukan]" value="{{ $bmn['peruntukan'] ?? '' }}"></td>
                                                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeBmnRow(this)"><i class="bi bi-trash"></i></button></td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td><input type="text" class="form-control form-control-sm" name="daftar_bmn[0][kode_barang]"></td>
                                            <td><input type="text" class="form-control form-control-sm" name="daftar_bmn[0][nup]"></td>
                                            <td><input type="text" class="form-control form-control-sm" name="daftar_bmn[0][jenis]"></td>
                                            <td><input type="text" class="form-control form-control-sm" name="daftar_bmn[0][luas]"></td>
                                            <td><input type="number" class="form-control form-control-sm" name="daftar_bmn[0][nilai]"></td>
                                            <td><input type="text" class="form-control form-control-sm" name="daftar_bmn[0][lokasi]"></td>
                                            <td><input type="text" class="form-control form-control-sm" name="daftar_bmn[0][peruntukan]"></td>
                                            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeBmnRow(this)"><i class="bi bi-trash"></i></button></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addBmnRow()">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Baris
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 8: Nodin Persetujuan KPKNL -->
    <div class="modal fade" id="modal-nodin-persetujuan-kpknl" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Isi Data: Nodin Persetujuan KPKNL</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-nodin-persetujuan-kpknl">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Nodin <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nodin_persetujuan_kpknl_nomor" value="{{ $utilization->nodin_persetujuan_kpknl_nomor ?? '' }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Nodin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="nodin_persetujuan_kpknl_tanggal" value="{{ $utilization->nodin_persetujuan_kpknl_tanggal ?? '' }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tujuan</label>
                            <input type="text" class="form-control" name="nodin_persetujuan_kpknl_tujuan" value="{{ $utilization->nodin_persetujuan_kpknl_tujuan ?? '' }}">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Persetujuan</label>
                                <input type="text" class="form-control" name="nodin_persetujuan_kpknl_nomor_persetujuan" value="{{ $utilization->nodin_persetujuan_kpknl_nomor_persetujuan ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Persetujuan</label>
                                <input type="date" class="form-control" name="nodin_persetujuan_kpknl_tanggal_persetujuan" value="{{ $utilization->nodin_persetujuan_kpknl_tanggal_persetujuan ?? '' }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Periode Sewa</label>
                            <input type="text" class="form-control" name="nodin_persetujuan_kpknl_periode_sewa" value="{{ $utilization->nodin_persetujuan_kpknl_periode_sewa ?? '' }}" placeholder="Contoh: 1 Januari 2024 - 31 Desember 2024">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nominal (Rp)</label>
                            <input type="number" class="form-control" name="nodin_persetujuan_kpknl_nominal" id="nodin_persetujuan_kpknl_nominal" value="{{ $utilization->nodin_persetujuan_kpknl_nominal ?? '' }}">
                            <small class="text-muted" id="nodin_persetujuan_kpknl_nominal_terbilang"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Mitra</label>
                            <input type="text" class="form-control" name="nodin_persetujuan_kpknl_mitra" value="{{ $utilization->nodin_persetujuan_kpknl_mitra ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kasub</label>
                            <input type="text" class="form-control" name="nodin_persetujuan_kpknl_kasub" value="{{ $utilization->nodin_persetujuan_kpknl_kasub ?? '' }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 9: Surat Invoice -->
    <div class="modal fade" id="modal-surat-invoice" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Isi Data: Surat Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-surat-invoice">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Surat <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="surat_invoice_nomor" value="{{ $utilization->surat_invoice_nomor ?? '' }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="surat_invoice_tanggal" value="{{ $utilization->surat_invoice_tanggal ?? '' }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tujuan</label>
                            <input type="text" class="form-control" name="surat_invoice_tujuan" value="{{ $utilization->surat_invoice_tujuan ?? '' }}">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Persetujuan</label>
                                <input type="text" class="form-control" name="surat_invoice_nomor_persetujuan" value="{{ $utilization->surat_invoice_nomor_persetujuan ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Persetujuan</label>
                                <input type="date" class="form-control" name="surat_invoice_tanggal_persetujuan" value="{{ $utilization->surat_invoice_tanggal_persetujuan ?? '' }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Periode Sewa</label>
                            <input type="text" class="form-control" name="surat_invoice_periode_sewa" value="{{ $utilization->surat_invoice_periode_sewa ?? '' }}" placeholder="Contoh: 1 Januari 2024 - 31 Desember 2024">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nominal (Rp)</label>
                            <input type="number" class="form-control" name="surat_invoice_nominal" id="surat_invoice_nominal" value="{{ $utilization->surat_invoice_nominal ?? '' }}">
                            <small class="text-muted" id="surat_invoice_nominal_terbilang"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Mitra</label>
                            <input type="text" class="form-control" name="surat_invoice_mitra" value="{{ $utilization->surat_invoice_mitra ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kasub</label>
                            <input type="text" class="form-control" name="surat_invoice_kasub" value="{{ $utilization->surat_invoice_kasub ?? '' }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 10: Perjanjian Sewa -->
    <div class="modal fade" id="modal-perjanjian-sewa" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Isi Data: Perjanjian Sewa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-perjanjian-sewa">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Mitra</label>
                            <input type="text" class="form-control" name="perjanjian_mitra" value="{{ $utilization->perjanjian_mitra ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Peruntukan</label>
                            <input type="text" class="form-control" name="perjanjian_peruntukan" value="{{ $utilization->perjanjian_peruntukan ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Gedung</label>
                            <input type="text" class="form-control" name="perjanjian_gedung" value="{{ $utilization->perjanjian_gedung ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hari, Tanggal (Lengkap)</label>
                            <input type="text" class="form-control" name="perjanjian_hari_tanggal" value="{{ $utilization->perjanjian_hari_tanggal ?? '' }}" placeholder="Contoh: Senin, 15 Januari 2024">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Detail Pihak Kedua</label>
                            <textarea class="form-control" name="perjanjian_detail_pihak_kedua" rows="3">{{ $utilization->perjanjian_detail_pihak_kedua ?? '' }}</textarea>
                            <small class="text-muted">Isi dengan detail lengkap pihak kedua (nama, jabatan, alamat, dll)</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Perjanjian <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="perjanjian_nomor" value="{{ $utilization->perjanjian_nomor ?? '' }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Penandatanganan <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="perjanjian_tanggal_penandatanganan" value="{{ $utilization->perjanjian_tanggal_penandatanganan ?? '' }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jangka Waktu (Nilai)</label>
                                <input type="number" class="form-control" name="jangka_waktu_nilai" value="{{ $utilization->jangka_waktu_nilai ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jangka Waktu (Satuan)</label>
                                <select class="form-select" name="jangka_waktu_satuan">
                                    <option value="Hari" {{ (isset($utilization->jangka_waktu_satuan) && $utilization->jangka_waktu_satuan === 'Hari') ? 'selected' : '' }}>Hari</option>
                                    <option value="Bulan" {{ (isset($utilization->jangka_waktu_satuan) && $utilization->jangka_waktu_satuan === 'Bulan') ? 'selected' : '' }}>Bulan</option>
                                    <option value="Tahun" {{ (isset($utilization->jangka_waktu_satuan) && $utilization->jangka_waktu_satuan === 'Tahun') ? 'selected' : '' }}>Tahun</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 11: Nodin TTD -->
    <div class="modal fade" id="modal-nodin-ttd" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Isi Data: Nodin TTD (Permohonan TTD Perjanjian)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-nodin-ttd">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Nodin <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nodin_ttd_nomor" value="{{ $utilization->nodin_ttd_nomor ?? '' }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Nodin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="nodin_ttd_tanggal" value="{{ $utilization->nodin_ttd_tanggal ?? '' }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tujuan</label>
                            <input type="text" class="form-control" name="nodin_ttd_tujuan" value="{{ $utilization->nodin_ttd_tujuan ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Mitra</label>
                            <input type="text" class="form-control" name="nodin_ttd_mitra" value="{{ $utilization->nodin_ttd_mitra ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Judul Perjanjian</label>
                            <input type="text" class="form-control" name="nodin_ttd_judul_perjanjian" value="{{ $utilization->nodin_ttd_judul_perjanjian ?? '' }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 12: Nodin Internal -->
    <div class="modal fade" id="modal-nodin-internal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Isi Data: Nodin Internal (Berjenjang Internal)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-nodin-internal">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Nodin <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nodin_internal_nomor" value="{{ $utilization->nodin_internal_nomor ?? '' }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Nodin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="nodin_internal_tanggal" value="{{ $utilization->nodin_internal_tanggal ?? '' }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Mitra</label>
                            <input type="text" class="form-control" name="nodin_internal_mitra" value="{{ $utilization->nodin_internal_mitra ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Judul Perjanjian</label>
                            <input type="text" class="form-control" name="nodin_internal_judul_perjanjian" value="{{ $utilization->nodin_internal_judul_perjanjian ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor Perjanjian</label>
                            <input type="text" class="form-control" name="nodin_internal_nomor_perjanjian" value="{{ $utilization->nodin_internal_nomor_perjanjian ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Detail Persetujuan</label>
                            <textarea class="form-control" name="nodin_internal_detail_persetujuan" rows="4">{{ $utilization->nodin_internal_detail_persetujuan ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/terbilang.min.js') }}"></script>
    <script>
        // CSRF Token setup
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        /**
         * Generate single document
         */
        function generateDocument(id, type) {
            // Show loading state
            if(event && event.target) {
                event.target.disabled = true;
                event.target.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Generating...';
            }

            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/utilization-dashboard/${id}/documents/generate/${type}`;
            form.target = '_blank'; // Open in new tab

            // CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);

            // Reset button after delay
            setTimeout(() => {
                if(event && event.target) {
                    event.target.disabled = false;
                    event.target.innerHTML = '<i class="bi bi-file-earmark-text me-1"></i>Generate Dokumen';
                }
            }, 2000);
        }

        /**
         * Initialize terbilang conversion for nominal fields
         */
        function initTerbilang() {
            // Nodin Berjenjang
            const nodinBerjenjangInput = document.getElementById('nodin_berjenjang_nominal');
            if (nodinBerjenjangInput) {
                nodinBerjenjangInput.addEventListener('input', function() {
                    const value = parseInt(this.value);
                    if (value && !isNaN(value)) {
                        document.getElementById('nodin_berjenjang_nominal_terbilang').textContent =
                            'Terbilang: ' + terbilang(value) + ' rupiah';
                    } else {
                        document.getElementById('nodin_berjenjang_nominal_terbilang').textContent = '';
                    }
                });
                // Trigger on load if value exists
                if (nodinBerjenjangInput.value) {
                    nodinBerjenjangInput.dispatchEvent(new Event('input'));
                }
            }

            // Nodin Persetujuan KPKNL
            const nodinPersetujuanInput = document.getElementById('nodin_persetujuan_kpknl_nominal');
            if (nodinPersetujuanInput) {
                nodinPersetujuanInput.addEventListener('input', function() {
                    const value = parseInt(this.value);
                    if (value && !isNaN(value)) {
                        document.getElementById('nodin_persetujuan_kpknl_nominal_terbilang').textContent =
                            'Terbilang: ' + terbilang(value) + ' rupiah';
                    } else {
                        document.getElementById('nodin_persetujuan_kpknl_nominal_terbilang').textContent = '';
                    }
                });
                if (nodinPersetujuanInput.value) {
                    nodinPersetujuanInput.dispatchEvent(new Event('input'));
                }
            }

            // Surat Invoice
            const suratInvoiceInput = document.getElementById('surat_invoice_nominal');
            if (suratInvoiceInput) {
                suratInvoiceInput.addEventListener('input', function() {
                    const value = parseInt(this.value);
                    if (value && !isNaN(value)) {
                        document.getElementById('surat_invoice_nominal_terbilang').textContent =
                            'Terbilang: ' + terbilang(value) + ' rupiah';
                    } else {
                        document.getElementById('surat_invoice_nominal_terbilang').textContent = '';
                    }
                });
                if (suratInvoiceInput.value) {
                    suratInvoiceInput.dispatchEvent(new Event('input'));
                }
            }
        }

        /**
         * Add row to Daftar BMN table
         */
        function addBmnRow() {
            const tbody = document.getElementById('daftar-bmn-tbody');
            const rowCount = tbody.rows.length;
            const newRow = tbody.insertRow();

            newRow.innerHTML = `
                <td><input type="text" class="form-control form-control-sm" name="daftar_bmn[${rowCount}][kode_barang]"></td>
                <td><input type="text" class="form-control form-control-sm" name="daftar_bmn[${rowCount}][nup]"></td>
                <td><input type="text" class="form-control form-control-sm" name="daftar_bmn[${rowCount}][jenis]"></td>
                <td><input type="text" class="form-control form-control-sm" name="daftar_bmn[${rowCount}][luas]"></td>
                <td><input type="number" class="form-control form-control-sm" name="daftar_bmn[${rowCount}][nilai]"></td>
                <td><input type="text" class="form-control form-control-sm" name="daftar_bmn[${rowCount}][lokasi]"></td>
                <td><input type="text" class="form-control form-control-sm" name="daftar_bmn[${rowCount}][peruntukan]"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeBmnRow(this)"><i class="bi bi-trash"></i></button></td>
            `;
        }

        /**
         * Remove row from Daftar BMN table
         */
        function removeBmnRow(button) {
            const tbody = document.getElementById('daftar-bmn-tbody');
            if (tbody.rows.length > 1) {
                button.closest('tr').remove();
            } else {
                alert('Minimal harus ada 1 baris');
            }
        }

        /**
         * Handle form submissions for all modals
         */
        function initFormHandlers() {
            const utilizationId = {{ $utilization->id }};
            const forms = [
                'surat-konfirmasi',
                'surat-konfirmasi-perpanjangan-sewa',
                'nodin-konfirmasi',
                'nodin-berjenjang',
                'surat-usulan-kpknl',
                'sptjm',
                'surat-pernyataan',
                'daftar-bmn',
                'nodin-persetujuan-kpknl',
                'surat-invoice',
                'perjanjian-sewa',
                'nodin-ttd',
                'nodin-internal'
            ];

            forms.forEach(formName => {
                const form = document.getElementById(`form-${formName}`);
                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();

                        // Get form data
                        const formData = new FormData(this);

                        // Show loading
                        const submitBtn = this.querySelector('button[type="submit"]');
                        const originalText = submitBtn.innerHTML;
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

                        // Submit via AJAX to the correct route with document type
                        fetch(`/utilization-dashboard/${utilizationId}/document/${formName}/save`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Data berhasil disimpan!');
                                // Close modal
                                const modal = bootstrap.Modal.getInstance(this.closest('.modal'));
                                if (modal) {
                                    modal.hide();
                                }
                                // Reload page to refresh status
                                window.location.reload();
                            } else {
                                alert('Error: ' + (data.message || 'Gagal menyimpan data'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan saat menyimpan data.');
                        })
                        .finally(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        });
                    });
                }
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initTerbilang();
            initFormHandlers();
        });

        /**
         * Generate all documents as ZIP
         */
        function generateAllDocuments(id) {
            // Show confirmation
            if (!confirm('Generate semua dokumen? Ini akan membuat file ZIP dengan semua dokumen yang tersedia.')) {
                return;
            }

            // Show loading state
            if(event && event.target) {
                event.target.disabled = true;
                event.target.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Generating ZIP...';
            }

            // Make AJAX request
            fetch(`/utilization-dashboard/${id}/documents/generate-all`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Dokumen berhasil digenerate! Download akan segera dimulai.');
                    // TODO: Trigger ZIP download
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat generate dokumen.');
            })
            .finally(() => {
                // Reset button
                if(event && event.target) {
                    event.target.disabled = false;
                    event.target.innerHTML = '<i class="bi bi-download me-2"></i>Download Semua Dokumen (ZIP)';
                }
            });
        }
    </script>
</body>
</html>
