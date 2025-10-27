@extends('layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        {{-- Ganti dengan judul yang sesuai atau biarkan seperti ini --}}
                        <h1 class="m-0">{{ $judul ?? 'Dashboard Pengajuan RKBMN' }}</h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                         <h3 class="card-title">{{ $judul ?? 'Data Pengajuan Kebutuhan BMN' }}</h3>
                        <div class="btn-group float-sm-right" role="group">
                            <a class="btn btn-success float-sm-right" href="{{ route('pengajuanrkbmnbagian.create') }}">
                                <i class="fas fa-plus mr-1"></i>Tambah Data
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="tabelbagian" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Aksi</th>
                                        <th>Kode / Jenis</th>
                                        <th>Uraian Barang</th>
                                        <th>Bagian Pelaksana</th>
                                        <th>Total Anggaran</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th><input type="text" class="form-control form-control-sm filter-input" data-column="1" placeholder="Filter kode..."></th>
                                        <th><input type="text" class="form-control form-control-sm filter-input" data-column="2" placeholder="Filter uraian..."></th>
                                        <th><input type="text" class="form-control form-control-sm filter-input" data-column="3" placeholder="Filter pelaksana..."></th>
                                        <th><input type="text" class="form-control form-control-sm filter-input" data-column="4" placeholder="Filter anggaran..."></th>
                                        <th><input type="text" class="form-control form-control-sm filter-input" data-column="5" placeholder="Filter tanggal..."></th>
                                        <th>
                                            <select class="form-control form-control-sm filter-input" data-column="6">
                                                <option value="">Semua Status</option>
                                                <option value="Draft">Draft</option>
                                                <option value="Diajukan">Diajukan</option>
                                                <option value="Disetujui">Disetujui</option>
                                                <option value="Ditolak">Ditolak</option>
                                            </select>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                {{-- Data akan dimuat via AJAX --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom classes for table striping */
        #tabelbagian tbody tr.bg-stripe-even {
            background-color: #ffffff;
        }
        #tabelbagian tbody tr.bg-stripe-odd {
            background-color: rgba(0,0,0,.05);
        }

        /* Action buttons */
        #tabelbagian td .d-flex {
            gap: 0.25rem;
        }

        #tabelbagian td .d-flex .btn + .btn {
            margin-left: 0.25rem;
        }

        .table th {
            background-color: #f8f9fa;
            border-top: none;
        }

        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #dee2e6;
        }

        .badge {
            font-size: 0.75em;
        }

        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }
            .d-flex .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.8rem;
            }
        }
    </style>

    <script>
        $(document).ready(function () {
            // Map untuk deskripsi jenis pengajuan
            const jenisMap = {
                'R1': 'Gedung Perkantoran',
                'R2': 'Pendidikan',
                'R3': 'Rumah Negara',
                'R4': 'Kendaraan Jabatan',
                'R5': 'Kendaraan Operasional',
                'R6': 'Kendaraan Fungsional'
            };

            // Fungsi format rupiah
            function formatRupiah(amount) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(amount);
            };

            // Inisialisasi DataTables
            $('#tabelbagian').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('pengajuanrkbmnbagian.index') }}",
                // PERUBAHAN: Konfigurasi Kolom Disesuaikan Total
                columns: [
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                    {
                        data: 'kode_jenis_pengajuan',
                        name: 'kode_jenis_pengajuan',
                        render: function(data, type, row) {
                            if (!data) return '-';
                            const jenis = data.substring(0, 2);
                            const deskripsi = jenisMap[jenis] || 'Lainnya';
                            return `<strong>${data}</strong><br><small class="text-muted">${deskripsi}</small>`;
                        }
                    },
                    {
                        data: 'uraian_barang',
                        name: 'uraian_barang',
                        render: function(data) {
                            // Batasi panjang teks untuk kerapian
                            return data ? (data.length > 50 ? data.substr(0, 50) + '...' : data) : '-';
                        }
                    },
                    { data: 'idbagianpelaksana', name: 'idbagianpelaksana' },
                    {
                        data: 'total_anggaran',
                        name: 'total_anggaran',
                        render: function(data) {
                            return data ? formatRupiah(parseFloat(data)) : 'Rp 0';
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data) {
                            return data ? new Date(data).toLocaleDateString('id-ID') : '-';
                        }
                    },
                        {
                            data: 'status',
                            name: 'status',
                            render: function (data) {
                                let badgeClass = 'secondary'; // Abu-abu (Default)
                                if (data === 'Draft') {
                                    badgeClass = 'info'; // Biru
                                } else if (data && data.includes('Disetujui')) { // <-- PERUBAHAN UTAMA
                                    badgeClass = 'success'; // Hijau
                                } else if (data && data.includes('Ditolak')) {
                                    badgeClass = 'danger'; // Merah
                                } else if (data && data.includes('Diajukan')) {
                                    badgeClass = 'warning'; // Kuning
                                }
                                return `<span class="badge badge-${badgeClass} badge-sm">${data || '-'}</span>`;
                            }
                        }
                ],
                language: {
                    processing: "Memuat...",
                    lengthMenu: "_MENU_ per halaman",
                    zeroRecords: "Tidak ada data yang cocok",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    search: "Cari:",
                    paginate: { next: "›", previous: "‹" }
                },
                pageLength: 10,
                order: [[5, 'desc']] // Urutkan berdasarkan tanggal terbaru
            });

            // Apply initial striping to the table
            applyTableStriping();

            // Get all filter inputs
            const filterInputs = document.querySelectorAll('.filter-input');

            // Add event listener to each filter input
            filterInputs.forEach(input => {
                input.addEventListener('keyup', function() {
                    filterTable();
                });

                // For select elements, use change event
                if (input.tagName === 'SELECT') {
                    input.addEventListener('change', function() {
                        filterTable();
                    });
                }
            });

            // Function to apply striping to visible rows
            function applyTableStriping() {
                const table = document.getElementById('tabelbagian');
                const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
                let visibleCount = 0;

                for (let i = 0; i < rows.length; i++) {
                    if (rows[i].style.display !== 'none') {
                        rows[i].classList.remove('bg-stripe-even', 'bg-stripe-odd');
                        if (visibleCount % 2 === 0) {
                            rows[i].classList.add('bg-stripe-even');
                        } else {
                            rows[i].classList.add('bg-stripe-odd');
                        }
                        visibleCount++;
                    }
                }
            }

            function filterTable() {
                const table = document.getElementById('tabelbagian');
                const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

                // Get filter values
                const filters = [];
                filterInputs.forEach(input => {
                    filters[parseInt(input.getAttribute('data-column'))] = input.value.toLowerCase();
                });

                // Check each row against filters
                for (let i = 0; i < rows.length; i++) {
                    let rowVisible = true;
                    const cells = rows[i].getElementsByTagName('td');

                    // Skip if empty state row
                    if (cells.length === 1 && cells[0].getAttribute('colspan')) {
                        continue;
                    }

                    // Check each cell that has a corresponding filter
                    for (let j = 0; j < cells.length; j++) {
                        if (filters[j] && filters[j] !== '') {
                            let cellText = '';

                            // Handle status column (has badge)
                            if (j === 6) {
                                const badge = cells[j].querySelector('.badge');
                                cellText = badge ? badge.textContent.toLowerCase() : '';
                            } else {
                                cellText = cells[j].textContent.toLowerCase();
                            }

                            if (cellText.indexOf(filters[j]) === -1) {
                                rowVisible = false;
                                break;
                            }
                        }
                    }

                    rows[i].style.display = rowVisible ? '' : 'none';
                }

                // Reapply striping after filtering
                applyTableStriping();
            }
        });
    </script>
@endsection
