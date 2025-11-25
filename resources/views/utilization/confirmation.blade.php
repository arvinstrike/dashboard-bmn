<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Penyewa - BMN</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Custom Alert System --}}
    @include('includes.custom-alert')

    <style>
        :root {
            --bs-primary-rgb: 79, 70, 229;
            --bs-body-font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #f8f9fa;
            font-family: var(--bs-body-font-family);
            padding-top: 20px;
            padding-bottom: 20px;
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 0.5rem;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            padding: 1rem 1.5rem;
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }

        .stat-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            border: 1px solid #e9ecef;
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #4f46e5;
            margin: 0.5rem 0;
        }

        .stat-title {
            color: #6b7280;
            font-weight: 500;
            margin: 0;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: #4b5563;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .action-buttons .btn {
            margin: 0 0.125rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .status-menunggu { background-color: #fef3c7; color: #d97706; }
        .status-disetujui { background-color: #d1fae5; color: #065f46; }
        .status-ditolak { background-color: #fee2e2; color: #dc2626; }
        .status-proses { background-color: #dbeafe; color: #1e40af; }
        .status-aktif { background-color: #ede9fe; color: #7c3aed; }
        .status-selesai { background-color: #e0e7ff; color: #4f46e5; }

        .btn-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }

        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }

        .table-container {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .table-responsive {
            border-radius: 0.5rem;
        }

        .text-primary-dark {
            color: #4f46e5 !important;
        }

        .text-success-dark {
            color: #065f46 !important;
        }

        .text-warning-dark {
            color: #d97706 !important;
        }

        .text-danger-dark {
            color: #dc2626 !important;
        }

        .add-more-btn {
            background: #4f46e5;
            border: none;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }

        .add-more-btn:hover {
            background: #4338ca;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold mb-1 text-dark">
                    <i class="bi bi-check-circle text-primary-dark me-2"></i>Konfirmasi Penyewa
                </h1>
                <p class="text-muted mb-0">Proses konfirmasi dan persetujuan penyewa BMN untuk pemanfaatan</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mb-4">
            <div class="col">
                <div class="stat-card h-100">
                    <p class="stat-title mb-1">
                        <i class="bi bi-people text-primary-dark me-2"></i>
                        Total Penyewa
                    </p>
                    <p class="stat-value mb-0">24</p>
                </div>
            </div>
            <div class="col">
                <div class="stat-card h-100">
                    <p class="stat-title mb-1">
                        <i class="bi bi-clock-history text-warning-dark me-2"></i>
                        Menunggu
                    </p>
                    <p class="stat-value mb-0">8</p>
                </div>
            </div>
            <div class="col">
                <div class="stat-card h-100">
                    <p class="stat-title mb-1">
                        <i class="bi bi-check2-circle text-success-dark me-2"></i>
                        Disetujui
                    </p>
                    <p class="stat-value mb-0">12</p>
                </div>
            </div>
            <div class="col">
                <div class="stat-card h-100">
                    <p class="stat-title mb-1">
                        <i class="bi bi-x-circle text-danger-dark me-2"></i>
                        Ditolak
                    </p>
                    <p class="stat-value mb-0">4</p>
                </div>
            </div>
        </div>

        <!-- Main Content - Full Width Table -->
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="h5 mb-0 text-dark">
                    <i class="bi bi-check2-square text-primary-dark me-2"></i>Daftar Konfirmasi Penyewa
                </h3>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" style="max-width: 150px;">
                        <option>Semua Status</option>
                        <option>Menunggu</option>
                        <option>Diproses</option>
                        <option>Disetujui</option>
                        <option>Ditolak</option>
                    </select>
                    <button class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i> Tambah
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Mitra</th>
                            <th scope="col">Uraian</th>
                            <th scope="col">Periode</th>
                            <th scope="col">Jenis Usaha</th>
                            <th scope="col">Lokasi</th>
                            <th scope="col">Biaya Sewa</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>
                                <strong>PT. Maju Jaya Abadi</strong><br>
                                <small>PIC: Budi Santoso</small>
                            </td>
                            <td>Tanah Kavling A1</td>
                            <td>Jan 2024 - Des 2024</td>
                            <td>Warung Makan</td>
                            <td>Jakarta Pusat</td>
                            <td>Rp 12.000.000</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" title="Setujui">
                                        <i class="bi bi-check"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" title="Tolak">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>
                                <strong>CV. Sinar Mandiri</strong><br>
                                <small>PIC: Siti Aminah</small>
                            </td>
                            <td>Bangunan Ruko B2</td>
                            <td>Mar 2024 - Mar 2025</td>
                            <td>Kantor Swasta</td>
                            <td>Jakarta Selatan</td>
                            <td>Rp 18.500.000</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" title="Setujui">
                                        <i class="bi bi-check"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>
                                <strong>UD. Berkah Jaya</strong><br>
                                <small>PIC: Anwar Kusuma</small>
                            </td>
                            <td>Lahan Parkir C3</td>
                            <td>Feb 2024 - Feb 2025</td>
                            <td>Tempat Parkir</td>
                            <td>Jakarta Barat</td>
                            <td>Rp 8.000.000</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" title="Cetak" disabled>
                                        <i class="bi bi-printer"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Form Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-file-check text-primary-dark me-2"></i>Formulir Konfirmasi
                        </h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="confirmation-number" class="form-label">Nomor Konfirmasi</label>
                                        <input type="text" class="form-control" id="confirmation-number" placeholder="Nomor konfirmasi...">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="confirmation-date" class="form-label">Tanggal</label>
                                        <input type="date" class="form-control" id="confirmation-date">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirmation-documents" class="form-label">Dokumen</label>
                                <textarea class="form-control" id="confirmation-documents" rows="3" placeholder="Deskripsi dokumen..."></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirmation-notes" class="form-label">Catatan</label>
                                <textarea class="form-control" id="confirmation-notes" rows="3" placeholder="Catatan tambahan..."></textarea>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-success">
                                    <i class="bi bi-check-circle me-2"></i>Konfirmasi Penyewa
                                </button>
                                <button type="button" class="btn btn-primary">
                                    <i class="bi bi-printer me-2"></i>Cetak
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Info Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-shield-lock text-primary-dark me-2"></i>Keamanan Verifikasi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Pastikan semua dokumen telah diverifikasi sebelum melakukan konfirmasi
                        </div>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Identitas Penyewa</span>
                                <span class="badge bg-success">Verifikasi</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Kelengkapan Dokumen</span>
                                <span class="badge bg-success">Verifikasi</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Legalitas Usaha</span>
                                <span class="badge bg-warning">Pending</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Nilai Sewa Pasar</span>
                                <span class="badge bg-info">Ditinjau</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="{{ asset('js/sweetalert2-config.js') }}"></script>

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