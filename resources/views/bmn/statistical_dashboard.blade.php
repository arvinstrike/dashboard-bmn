<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Statistik Pengajuan BMN</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --bs-primary-rgb: 79, 70, 229; /* Indigo */
            --bs-body-font-family: 'Inter', sans-serif;
        }
        body {
            background-color: #F7F9FC;
        }

        /* Minimalist Stat Card */
        .stat-card {
            border: 1px solid #E5E7EB;
            border-radius: 0.75rem;
            padding: 1.5rem;
            background-color: #fff;
            transition: all 0.2s ease-in-out;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .stat-card .stat-title {
            font-weight: 500;
            color: #6B7280; /* Muted gray */
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }
        .stat-card .stat-title .bi {
            font-size: 1.1rem;
            margin-right: 0.5rem;
        }
        .stat-card .stat-value {
            font-size: 2rem; /* Consistent font size */
            font-weight: 600;
            color: #111827;
            margin-top: 0.25rem;
        }

        /* Highlight with Border */
        .stat-card-highlight-border {
            border-left: 5px solid rgb(var(--bs-primary-rgb));
        }
        .stat-card-highlight-border .stat-title .bi,
        .stat-card-highlight-border .stat-value {
            color: rgb(var(--bs-primary-rgb));
        }

        /* Icon Colors */
        .text-primary-dark { color: #2563eb !important; }
        .text-warning-dark { color: #ea580c !important; }
        .text-success-dark { color: #059669 !important; }
        .text-danger-dark { color: #dc3545 !important; }
        .text-gray-dark { color: #6B7280 !important; }


        /* Chart Container */
        .chart-container {
            position: relative;
            height: 400px;
            margin-bottom: 2rem;
        }

        /* Filter Section */
        .filter-header a {
            text-decoration: none;
            color: inherit;
        }
        .filter-header .chevron-icon {
            transition: transform 0.3s ease;
        }
        .filter-header a[aria-expanded="true"] .chevron-icon {
            transform: rotate(180deg);
        }

        /* Force the filter card to be on top and allow its children to overflow */
        .card.mb-4 { /* Targeting the filter card specifically */
            z-index: 20; /* A higher z-index to lift it above the next card */
            position: relative; /* z-index requires a position other than static */
        }

        #filterCollapse, #filterCollapse .card-body {
            overflow: visible !important; /* Force overflow visibility on the collapsible area and its body */
        }

        /* Fix for dropdowns being cut off - Robust solution */
        .card {
            overflow: visible;
        }
        
        .card-body {
            overflow: visible;
            position: relative;
        }
        
        /* Ensure select dropdowns show properly */
        .form-select {
            z-index: 1050 !important; /* Higher than Bootstrap's modal z-index */
            position: relative;
        }
        
        /* Additional fix for dropdown menu positioning */
        .dropdown-menu {
            z-index: 1060 !important; /* Higher than form-select to ensure visibility */
        }
        
        /* Specific fix for select dropdown that appears cut off */
        .select-wrapper {
            position: relative;
            z-index: 1040;
        }

        /* Badge Styles */
        .badge-status {
            font-size: .8rem; font-weight: 500; padding: .4em .8em; border-radius: 9999px;
        }
        .badge-green { background-color: #D1FAE5; color: #065F46; }
        .badge-red { background-color: #FEE2E2; color: #991B1B; }
        .badge-yellow { background-color: #FEF3C7; color: #92400E; }
        .badge-blue { background-color: #DBEAFE; color: #1E40AF; }
        .badge-gray { background-color: #F3F4F6; color: #374151; }
    </style>
</head>
<body>
    <div class="container-fluid p-4 p-lg-5">
        <h1 class="h2 fw-bold mb-4" style="color: rgb(var(--bs-primary-rgb));"> Dashboard Pengajuan RKBMN</h1>

        <!-- Stat Cards -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-5 g-4 mb-4">
            <div class="col">
                <div class="card stat-card h-100">
                    <p class="stat-title mb-1">
                        <i class="bi bi-journal-text text-primary-dark"></i>
                        Total Pengajuan
                    </p>
                    <p class="stat-value mb-0">{{ $stats['total_pengajuan'] }}</p>
                </div>
            </div>
            <div class="col">
                <div class="card stat-card h-100">
                    <p class="stat-title mb-1">
                        <i class="bi bi-clock-history text-warning-dark"></i>
                        Menunggu Persetujuan
                    </p>
                    <p class="stat-value mb-0">{{ $stats['menunggu_persetujuan'] }}</p>
                </div>
            </div>
            <div class="col">
                <div class="card stat-card h-100">
                    <p class="stat-title mb-1">
                        <i class="bi bi-check2-circle text-success-dark"></i>
                        Disetujui
                    </p>
                    <p class="stat-value mb-0">{{ $stats['approved'] }}</p>
                </div>
            </div>
            <div class="col">
                <div class="card stat-card h-100">
                    <p class="stat-title mb-1">
                        <i class="bi bi-x-circle @if($stats['rejected'] > 0) text-danger-dark @else text-gray-dark @endif"></i>
                        Ditolak
                    </p>
                    <p class="stat-value mb-0">{{ $stats['rejected'] }}</p>
                </div>
            </div>
            <div class="col">
                <div class="card stat-card stat-card-highlight-border h-100">
                    <p class="stat-title mb-1">
                        <i class="bi bi-wallet2"></i>
                        Anggaran Disetujui
                    </p>
                    <p class="stat-value mb-0">
                        @php
                            $anggaran = $stats["anggaran_disetujui"];
                            if($anggaran >= 1000000000000) {
                                echo 'Rp '.number_format($anggaran/1000000000000, 2, ',', '.').'T';
                            } elseif($anggaran >= 1000000000) {
                                echo 'Rp '.number_format($anggaran/1000000000, 2, ',', '.').'M';
                            } else {
                                echo 'Rp '.number_format($anggaran/1000000, 2, ',', '.').'JT';
                            }
                        @endphp
                    </p>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-header filter-header">
                <a href="#filterCollapse" data-bs-toggle="collapse" role="button" aria-expanded="true" aria-controls="filterCollapse" class="d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-funnel me-2"></i>Filter Pengajuan</span>
                    <i class="bi bi-chevron-down chevron-icon"></i>
                </a>
            </div>
            <div class="collapse show" id="filterCollapse">
                <div class="card-body p-4">
                    <form action="{{ route('bmn.statistical_dashboard') }}" method="GET" class="row g-3">
                        <!-- Filter fields here -->
                        <div class="col-md-4">
                            <label for="jenis_pengajuan" class="form-label">Jenis Pengajuan</label>
                            <select name="jenis_pengajuan" id="jenis_pengajuan" class="form-select">
                                <option value="">Semua Jenis</option>
                                @foreach($jenisPengajuanOptions as $k => $v)
                                    <option value="{{$k}}" {{($filters['jenis_pengajuan']??'')==$k?'selected':''}}>{{$v}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="bagian" class="form-label">Bagian Pengusul</label>
                            <select name="bagian" id="bagian" class="form-select">
                                <option value="">Semua Bagian</option>
                                @foreach($bagianOptions as $b)
                                    <option value="{{$b->id}}" {{($filters['bagian']??'')==$b->id?'selected':''}}>{{$b->uraianbagian}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">Semua Status</option>
                                @foreach($statusOptions as $s)
                                    <option value="{{$s->status}}" {{($filters['status']??'')==$s->status?'selected':''}}>{{ucfirst($s->status)}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="tahun_anggaran" class="form-label">Tahun Anggaran</label>
                            <select name="tahun_anggaran" id="tahun_anggaran" class="form-select">
                                <option value="">Semua Tahun</option>
                                @foreach($tahunAnggaranOptions as $t)
                                    <option value="{{$t->tahun_anggaran}}" {{($filters['tahun_anggaran']??'')==$t->tahun_anggaran?'selected':''}}>{{$t->tahun_anggaran}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="atr_non_atr" class="form-label">ATR/Non-ATR</label>
                            <select name="atr_non_atr" id="atr_non_atr" class="form-select">
                                <option value="">Semua Jenis</option>
                                @foreach($atrOptions as $a)
                                    <option value="{{$a->atr_nonatr}}" {{($filters['atr_non_atr']??'')==$a->atr_nonatr?'selected':''}}>{{$a->atr_nonatr}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="skema" class="form-label">Skema</label>
                            <select name="skema" id="skema" class="form-select">
                                <option value="">Semua Skema</option>
                                @foreach($skemaOptions as $s)
                                    <option value="{{$s->skema}}" {{($filters['skema']??'')==$s->skema?'selected':''}}>{{$s->skema}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mt-4 d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i> Terapkan Filter</button>
                                <a href="{{ route('bmn.statistical_dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset</a>
                            </div>
                            <div>
                                <a href="{{ route('bmn.statistical_dashboard.export.excel') }}?{{ http_build_query($filters) }}" class="btn btn-success me-2"><i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Excel</a>
                                <a href="{{ route('bmn.statistical_dashboard.export.pdf') }}?{{ http_build_query($filters) }}" class="btn btn-danger"><i class="bi bi-file-earmark-pdf me-1"></i> Export PDF</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row g-4 mb-4">
            <!-- Status Distribution Chart -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="bi bi-bar-chart me-2"></i>Distribusi Status Pengajuan
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Year Distribution Chart -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="bi bi-bar-chart me-2"></i>Pengajuan per Tahun Anggaran
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="yearChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Bagian Distribution Chart -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="bi bi-bar-chart me-2"></i>Pengajuan per Bagian Pengusul
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="bagianChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Jenis Pengajuan Distribution Chart -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="bi bi-bar-chart me-2"></i>Distribusi Jenis Pengajuan
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="jenisChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- ATR vs Non-ATR Distribution Chart -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="bi bi-bar-chart-line me-2"></i>Total Anggaran per Bagian
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="anggaranBagianChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Skema Distribution Chart -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="bi bi-pie-chart me-2"></i>Distribusi Berdasarkan Skema
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="skemaChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript to generate charts
        document.addEventListener('DOMContentLoaded', function() {
            // Status Chart - Pie Chart
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            
            const statusLabels = [
                @foreach($stats['status_stats'] as $status => $count)
                    '{{ ucfirst(str_replace('_', ' ', $status)) }}',
                @endforeach
            ];

            const statusColorMap = {
                'Approved': 'rgba(16, 185, 129, 0.8)',    // Emerald
                'Completed': 'rgba(79, 70, 229, 0.8)',    // Indigo
                'Rejected': 'rgba(239, 68, 68, 0.8)',     // Red
                'Pending': 'rgba(245, 158, 11, 0.8)',    // Amber
                'In progress': 'rgba(59, 130, 246, 0.8)', // Blue
                'Draft': 'rgba(107, 114, 128, 0.8)',   // Gray
            };

            const statusBorderColorMap = {
                'Approved': 'rgba(16, 185, 129, 1)',
                'Completed': 'rgba(79, 70, 229, 1)',
                'Rejected': 'rgba(239, 68, 68, 1)',
                'Pending': 'rgba(245, 158, 11, 1)',
                'In progress': 'rgba(59, 130, 246, 1)',
                'Draft': 'rgba(107, 114, 128, 1)',
            };

            const statusChart = new Chart(statusCtx, {
                type: 'pie',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        label: 'Jumlah Pengajuan',
                        data: [
                            @foreach($stats['status_stats'] as $count)
                                {{ $count }},
                            @endforeach
                        ],
                        backgroundColor: statusLabels.map(label => statusColorMap[label] || 'rgba(156, 163, 175, 0.8)'),
                        borderColor: statusLabels.map(label => statusBorderColorMap[label] || 'rgba(156, 163, 175, 1)'),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        title: {
                            display: true,
                            text: 'Distribusi Status Pengajuan'
                        }
                    }
                }
            });

            // Year Chart - Bar Chart
            const yearCtx = document.getElementById('yearChart').getContext('2d');
            const yearChart = new Chart(yearCtx, {
                type: 'bar',
                data: {
                    labels: [
                        @foreach($stats['tahun_stats'] as $tahun)
                            '{{ $tahun->tahun_anggaran }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Jumlah Pengajuan',
                        data: [
                            @foreach($stats['tahun_stats'] as $tahun)
                                {{ $tahun->count }},
                            @endforeach
                        ],
                        backgroundColor: 'rgba(79, 70, 229, 0.6)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Jumlah Pengajuan per Tahun Anggaran'
                        },
                        datalabels: {
                            display: false // Hide data labels on bars
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            display: false // Hide y-axis labels
                        },
                        x: {
                            display: false // Hide x-axis labels
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });

            // Bagian Chart - Horizontal Bar Chart
            const bagianCtx = document.getElementById('bagianChart').getContext('2d');
            const bagianChart = new Chart(bagianCtx, {
                type: 'bar',
                data: {
                    labels: [
                        @foreach($stats['bagian_stats'] as $bagian)
                            '{{ $bagian->nama_bagian }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Jumlah Pengajuan',
                        data: [
                            @foreach($stats['bagian_stats'] as $bagian)
                                {{ $bagian->count }},
                            @endforeach
                        ],
                        backgroundColor: 'rgba(245, 158, 11, 0.6)',
                        borderColor: 'rgba(245, 158, 11, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Jumlah Pengajuan per Bagian Pengusul'
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            display: false // Hide x-axis labels
                        },
                        y: {
                            display: false // Hide y-axis labels
                        }
                    }
                }
            });

            // Jenis Chart - Bar Chart
            const jenisCtx = document.getElementById('jenisChart').getContext('2d');
            const jenisChart = new Chart(jenisCtx, {
                type: 'bar',
                data: {
                    labels: [
                        @foreach($stats['jenis_stats'] as $jenis)
                            '{{ $jenisPengajuanOptions[$jenis->jenis_pengajuan] ?? $jenis->jenis_pengajuan }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Jumlah Pengajuan',
                        data: [
                            @foreach($stats['jenis_stats'] as $jenis)
                                {{ $jenis->count }},
                            @endforeach
                        ],
                        backgroundColor: 'rgba(16, 185, 129, 0.6)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Distribusi Jenis Pengajuan'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            display: false // Hide y-axis labels
                        },
                        x: {
                            display: false // Hide x-axis labels
                        }
                    }
                }
            });

            // Anggaran per Bagian Chart
            const anggaranBagianCtx = document.getElementById('anggaranBagianChart').getContext('2d');
            const anggaranBagianChart = new Chart(anggaranBagianCtx, {
                type: 'bar',
                data: {
                    labels: [
                        @foreach($stats['bagian_stats'] as $bagian)
                            '{{ $bagian->nama_bagian }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Total Anggaran',
                        data: [
                            @foreach($stats['bagian_stats'] as $bagian)
                                {{ $bagian->total_anggaran }},
                            @endforeach
                        ],
                        backgroundColor: 'rgba(239, 68, 68, 0.6)', // Red
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Total Anggaran per Bagian'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            display: false, // Hide y-axis labels
                            ticks: {
                                callback: function(value, index, values) {
                                    if (value >= 1000000000) {
                                        return 'Rp ' + (value / 1000000000) + 'M';
                                    } else if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000) + 'JT';
                                    }
                                    return 'Rp ' + value;
                                }
                            }
                        },
                        x: {
                            display: false // Hide x-axis labels
                        }
                    }
                }
            });

            // Skema Chart - Pie Chart
            const skemaCtx = document.getElementById('skemaChart').getContext('2d');
            const skemaChart = new Chart(skemaCtx, {
                type: 'pie',
                data: {
                    labels: [
                        @foreach($stats['skema_stats'] as $skema)
                            '{{ $skema->skema }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Jumlah Pengajuan',
                        data: [
                            @foreach($stats['skema_stats'] as $skema)
                                {{ $skema->count }},
                            @endforeach
                        ],
                        backgroundColor: [
                            'rgba(79, 70, 229, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(139, 92, 246, 0.8)',
                            'rgba(16, 158, 149, 0.8)',
                            'rgba(241, 112, 110, 0.8)',
                            'rgba(99, 102, 241, 0.8)'
                        ],
                        borderColor: [
                            'rgba(79, 70, 229, 1)',
                            'rgba(239, 68, 68, 1)',
                            'rgba(245, 158, 11, 1)',
                            'rgba(16, 185, 129, 1)',
                            'rgba(139, 92, 246, 1)',
                            'rgba(16, 158, 149, 1)',
                            'rgba(241, 112, 110, 1)',
                            'rgba(99, 102, 241, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        title: {
                            display: true,
                            text: 'Distribusi Berdasarkan Skema'
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>