<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi Digital - Pengajuan RKBMN</title>

    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .verification-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .verification-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 1200px;
            width: 100%;
        }

        .card-header-custom {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .countdown-timer {
            background: #ff6b6b;
            color: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.2em;
            font-weight: bold;
        }

        .countdown-timer.warning {
            background: #ffa726;
        }

        .countdown-timer.expired {
            background: #ef5350;
        }

        .countdown-timer.success {
            background: #10b981;
        }

        .document-tabs {
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 20px;
        }

        .document-tab {
            display: inline-block;
            padding: 12px 20px;
            margin-right: 10px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-bottom: none;
            border-radius: 8px 8px 0 0;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .document-tab.active {
            background: #4f46e5;
            color: white;
            border-color: #4f46e5;
        }

        .document-tab:hover {
            background: #e9ecef;
        }

        .document-tab.active:hover {
            background: #4338ca;
        }

        .document-content {
            display: none;
        }

        .document-content.active {
            display: block;
        }

        .pdf-preview {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            height: 500px;
            width: 100%;
            background: #f8f9fa;
        }

        .info-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .verification-form {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 25px;
            margin-top: 20px;
        }

        .btn-verify-all {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            padding: 15px 40px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            font-size: 1.1em;
            transition: all 0.3s ease;
        }

        .btn-verify-all:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(16, 185, 129, 0.3);
            color: white;
        }

        .btn-verify-all:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .status-page {
            text-align: center;
            padding: 60px 20px;
        }

        .status-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .status-icon.success { color: #10b981; }
        .status-icon.error { color: #ef4444; }
        .status-icon.warning { color: #f59e0b; }

        .passphrase-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .verification-container {
                padding: 10px;
            }

            .card-header-custom {
                padding: 20px;
            }

            .pdf-preview {
                height: 400px;
            }

            .document-tab {
                font-size: 0.9em;
                padding: 10px 15px;
                margin-right: 5px;
                margin-bottom: 10px;
            }

            .verification-form {
                padding: 15px;
            }
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #4f46e5;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    @php
        // Tentukan variabel utama untuk daftar dokumen.
        // Gunakan $documentsToShow jika ada; jika tidak, gunakan $documentsToSign.
        // Ini membuat view kompatibel dengan kedua controller (Operator & Pelaksana).
        $dokumenUntukDitampilkan = $documentsToShow ?? ($documentsToSign ?? []);
    @endphp

    <div class="verification-container">
        <div class="verification-card">
            @if($status === 'error' || $status === 'expired' || $status === 'already_verified')
                {{-- Error/Status Pages --}}
                <div class="status-page">
                    @if($status === 'error')
                        <div class="status-icon error">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3 class="text-danger">Terjadi Kesalahan</h3>
                        <p class="text-muted">{{ $message }}</p>
                    @elseif($status === 'expired')
                        <div class="status-icon warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3 class="text-warning">Link Kedaluwarsa</h3>
                        <p class="text-muted">{{ $message }}</p>
                        <small class="text-muted">Silakan hubungi administrator untuk mendapatkan link verifikasi baru.</small>
                    @elseif($status === 'already_verified')
                        <div class="status-icon success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3 class="text-success">Sudah Terverifikasi</h3>
                        <p class="text-muted">{{ $message }}</p>
                        <small class="text-muted">Dokumen telah ditandatangani sebelumnya.</small>
                    @endif
                </div>
            @else
                {{-- Valid Verification Page --}}
                <div class="card-header-custom">
                    <h2><i class="fas fa-signature mr-3"></i>Verifikasi Digital</h2>
                    <p class="mb-0">Pengajuan RKBMN Reguler #{{ $pengajuan->id }}</p>
                </div>

                <div class="card-body p-4">
                    {{-- Countdown Timer --}}
                    <div class="countdown-timer" id="countdown-timer">
                        <i class="fas fa-clock mr-2"></i>
                        <span id="countdown-text">Menghitung waktu...</span>
                    </div>

                    {{-- Pengajuan Info --}}
                    <div class="info-section">
                        <h5><i class="fas fa-info-circle mr-2 text-primary"></i>Informasi Pengajuan</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Nomor Pengajuan:</small>
                                <div class="font-weight-bold">#{{ $pengajuan->id }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Tipe:</small>
                                <div class="font-weight-bold">{{ ucfirst($pengajuan->tipe_pengajuan) }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Bagian Pengusul:</small>
                                <div class="font-weight-bold">{{ $bagianPengusul->uraianbagian ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Total Anggaran:</small>
                                <div class="font-weight-bold text-success">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Penanggung Jawab:</small>
                                <div class="font-weight-bold">{{ $verification->eselon_iii_name }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">NIP:</small>
                                <div class="font-weight-bold">{{ $verification->eselon_iii_nip }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Document Tabs --}}
                    <div class="document-tabs">
                        @foreach($dokumenUntukDitampilkan as $index => $document)
                            <div class="document-tab {{ $index === 0 ? 'active' : '' }}"
                                 data-document="{{ $document }}"
                                 onclick="switchDocumentTab('{{ $document }}')">
                                <i class="fas fa-file-pdf mr-2"></i>
                                @if($document === 'berita_acara')
                                    Berita Acara
                                @elseif($document === 'surat_rekomendasi')
                                    Surat Rekomendasi
                                @elseif($document === 'tor')
                                    Terms of Reference
                                @elseif($document === 'lampiran')
                                    Lampiran
                                @endif

                                @if(in_array($document, $documentsToSign))
                                    <span class="badge badge-warning ml-1">Perlu Tanda Tangan</span>
                                @else
                                    <span class="badge badge-success ml-1">Sudah Ditandatangani</span>
                                @endif
                            </div>
                        @endforeach

                        {{-- Pastikan blok @if ini ada dan variabelnya tertulis dengan benar --}}
                        @if($has_dokumen_pendukung)
                            <div class="document-tab" data-document="dokumen_pendukung" onclick="switchDocumentTab('dokumen_pendukung')">
                                <i class="fas fa-paperclip mr-2"></i>
                                Dokumen Pendukung
                                <span class="badge badge-info ml-1">Review</span>
                            </div>
                        @endif
                        </div>

                    {{-- Document Content --}}
                    @foreach($dokumenUntukDitampilkan as $index => $document)
                        <div class="document-content {{ $index === 0 ? 'active' : '' }}" id="content-{{ $document }}">
                            <div class="row">
                                <div class="col-lg-8">
                                    <h6 class="font-weight-bold mb-3">
                                        <i class="fas fa-eye mr-2"></i>Preview Dokumen
                                        @if($document === 'berita_acara')
                                            Berita Acara
                                        @elseif($document === 'tor')
                                            Terms of Reference
                                        @elseif($document === 'lampiran')
                                            Lampiran
                                        @endif
                                    </h6>

                                    @php
                                        $previewUrl = '';
                                        if (isset($verification_level) && $verification_level === 'operator') {
                                            $route_name = 'pengajuan.reguler.preview-' . str_replace('_', '-', $document);
                                            $previewUrl = route($route_name, ['id' => $pengajuan->id]);
                                        } else {
                                            $previewUrl = route('magic-link-validation.preview-document', [
                                                'encrypted_id' => $encrypted_token,
                                                'document_type' => str_replace('_', '-', $document)
                                            ]);
                                        }
                                    @endphp

                                    <iframe class="pdf-preview"
                                            id="pdf-{{ $document }}"
                                            src="{{ $previewUrl }}"
                                            loading="lazy">
                                    </iframe>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Pastikan blok @if ini juga ada untuk menampilkan kontennya --}}
                    @if($has_dokumen_pendukung)
                        <div class="document-content" id="content-dokumen_pendukung">
                            <div class="row">
                                <div class="col-lg-8">
                                    <h6 class="font-weight-bold mb-3">
                                        <i class="fas fa-eye mr-2"></i>Preview Dokumen Pendukung
                                    </h6>

                                    <iframe class="pdf-preview"
                                            id="pdf-dokumen_pendukung"
                                            src="{{ route('pengajuan.reguler.previewDokumen', ['id' => $pengajuan->id]) }}"
                                            loading="lazy">
                                    </iframe>
                                    </div>
                            </div>
                        </div>
                    @endif
                    {{-- Passphrase Global Section (untuk semua dokumen) --}}
                    <div class="passphrase-section">
                        <h5 class="font-weight-bold mb-3">
                            <i class="fas fa-signature mr-2 text-primary"></i>Tanda Tangan Digital - Semua Dokumen
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="global-passphrase">
                                        <i class="fas fa-key mr-1"></i>Passphrase untuk Semua Dokumen
                                    </label>
                                    <input type="password"
                                           id="global-passphrase"
                                           class="form-control form-control-lg"
                                           placeholder="Masukkan passphrase e-sign">
                                    <small class="form-text text-muted">
                                        Passphrase ini akan digunakan untuk menandatangani semua dokumen sekaligus.
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="button"
                                        class="btn btn-verify-all btn-block"
                                        id="verify-all-btn"
                                        onclick="verifyAllDocuments()">
                                    <i class="fas fa-check-double mr-2"></i>
                                    Tanda Tangani Semua Dokumen ({{ count($documentsToSign) }} dokumen)
                                </button>
                            </div>
                        </div>
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <strong>Penting:</strong> Dengan menekan tombol ini, Anda akan menandatangani SEMUA dokumen sekaligus.
                            Pastikan Anda telah memeriksa semua dokumen dengan teliti.
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Success Modal --}}
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center p-5">
                    <div class="status-icon success mb-3">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h4 class="text-success">Verifikasi Berhasil!</h4>
                    <p id="success-message" class="text-muted"></p>
                    <button type="button" class="btn btn-success" onclick="closeSuccessModal()">
                        <i class="fas fa-check mr-2"></i>Selesai
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        // Global variables
        const expiresAt = {{ $expires_at ?? 0 }};
        const encryptedToken = '{{ $encrypted_token ?? '' }}';
        const documentsToSign = @json($documentsToSign ?? []);
        let countdownInterval;
        let signedDocuments = [];

        $(document).ready(function() {
            @if($status === 'valid')
                startCountdown();

                // Auto-sync global passphrase to individual passphrase fields
                $('#global-passphrase').on('input', function() {
                    const globalPassphrase = $(this).val();
                    documentsToSign.forEach(doc => {
                        $(`#passphrase-${doc}`).val(globalPassphrase);
                    });
                    updateVerifyButton();
                });

                // Sync individual passphrase back to global if they're all the same
                $('input[id^="passphrase-"]').on('input', function() {
                    syncIndividualToGlobal();
                    updateVerifyButton();
                });
            @endif
        });

        function startCountdown() {
            const expiryTime = expiresAt * 1000; // Convert to milliseconds

            countdownInterval = setInterval(function() {
                const now = new Date().getTime();
                const distance = expiryTime - now;

                if (distance > 0) {
                    const hours = Math.floor(distance / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    // Format tampilan waktu
                    let timeText = '';
                    if (hours > 0) {
                        timeText = `${hours} jam ${minutes} menit ${seconds} detik`;
                    } else {
                        timeText = `${minutes} menit ${seconds} detik`;
                    }

                    $('#countdown-text').text(`Sisa waktu: ${timeText}`);

                    // Change color based on remaining time (disesuaikan untuk 12 jam)
                    const $timer = $('#countdown-timer');
                    const totalMinutes = hours * 60 + minutes;

                    if (totalMinutes < 30) { // Kurang dari 30 menit = merah (expired)
                        $timer.removeClass('warning').addClass('expired');
                    } else if (totalMinutes < 60) { // Kurang dari 1 jam = orange (warning)
                        $timer.removeClass('expired').addClass('warning');
                    } else { // Lebih dari 1 jam = normal
                        $timer.removeClass('warning expired');
                    }
                } else {
                    clearInterval(countdownInterval);
                    handleExpiry();
                }
            }, 1000);
        }

        function handleExpiry() {
            $('#countdown-text').text('Link telah kedaluwarsa');
            $('#countdown-timer').removeClass('warning').addClass('expired');

            // Disable all inputs and buttons
            $('input[type="password"], .btn-verify-all').prop('disabled', true);

            Swal.fire({
                icon: 'error',
                title: 'Link Kedaluwarsa',
                text: 'Link verifikasi telah kedaluwarsa. Silakan hubungi administrator.',
                allowOutsideClick: false,
                confirmButtonText: 'OK'
            });
        }

        function switchDocumentTab(document) {
            // Remove active class from all tabs and contents
            $('.document-tab').removeClass('active');
            $('.document-content').removeClass('active');

            // Add active class to selected tab and content
            $(`[data-document="${document}"]`).addClass('active');
            $(`#content-${document}`).addClass('active');
        }

        function syncIndividualToGlobal() {
            // Check if all individual passphrases are the same
            const passphrases = [];
            documentsToSign.forEach(doc => {
                passphrases.push($(`#passphrase-${doc}`).val());
            });

            // If all passphrases are the same and not empty, sync to global
            if (passphrases.length > 0 && passphrases.every(p => p === passphrases[0]) && passphrases[0] !== '') {
                $('#global-passphrase').val(passphrases[0]);
            } else if (passphrases.every(p => p === '')) {
                $('#global-passphrase').val('');
            }
        }

        function updateVerifyButton() {
            const globalPassphrase = $('#global-passphrase').val();
            const btn = $('#verify-all-btn');

            if (globalPassphrase.length > 0) {
                btn.removeClass('btn-secondary')
                   .addClass('btn-verify-all')
                   .prop('disabled', false)
                   .html('<i class="fas fa-check-double mr-2"></i>Tanda Tangani Semua Dokumen (' + documentsToSign.length + ' dokumen)');
            } else {
                btn.removeClass('btn-verify-all')
                   .addClass('btn-secondary')
                   .prop('disabled', true)
                   .html('<i class="fas fa-lock mr-2"></i>Masukkan Passphrase untuk Melanjutkan');
            }
        }

        function verifyAllDocuments() {
            const globalPassphrase = $('#global-passphrase').val();

            if (!globalPassphrase) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Passphrase Diperlukan',
                    text: 'Silakan masukkan passphrase untuk melanjutkan.'
                });
                return;
            }

            // Sync global passphrase to all individual fields
            documentsToSign.forEach(doc => {
                $(`#passphrase-${doc}`).val(globalPassphrase);
            });

            // Confirm action
            Swal.fire({
                icon: 'question',
                title: 'Konfirmasi Tanda Tangan',
                html: `Anda akan menandatangani <strong>${documentsToSign.length} dokumen</strong> sekaligus:<br><br>` +
                    documentsToSign.map(doc => {
                        let docName = '';
                        if (doc === 'berita_acara') docName = 'Berita Acara';
                        else if (doc === 'surat_rekomendasi') docName = 'Surat Rekomendasi'; // <-- PERBAIKAN DI SINI
                        else if (doc === 'tor') docName = 'Terms of Reference';
                        else if (doc === 'lampiran') docName = 'Lampiran';
                        return `• ${docName}`;
                    }).join('<br>') +
                    '<br><br>Apakah Anda yakin ingin melanjutkan?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tanda Tangani Semua',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#10b981'
            }).then((result) => {
                if (result.isConfirmed) {
                    processAllDocuments(globalPassphrase);
                }
            });
        }

        function processAllDocuments(passphrase) {
            const btn = $('#verify-all-btn');
            const originalText = btn.html();

            btn.prop('disabled', true).html('<span class="loading-spinner mr-2"></span>Memproses...');

            Swal.fire({
                title: 'Memproses Semua Dokumen...',
                text: 'Mohon tunggu, semua dokumen sedang ditandatangani.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: `/magic-link-verification/${encryptedToken}/process-esign`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify({
                    passphrase: passphrase,
                    documents: documentsToSign
                }),
                success: function(response) {
                    if (response.success) {
                        signedDocuments = response.signed_documents || documentsToSign;

                        // Mark all documents as signed in UI
                        signedDocuments.forEach(doc => markDocumentSigned(doc));

                        // Show success modal
                        Swal.fire({
                            icon: 'success',
                            title: 'Verifikasi Berhasil!',
                            text: response.message,
                            confirmButtonText: 'Lanjutkan',
                            allowOutsideClick: false
                        }).then(() => {
                            // Baru kemudian show modal success
                            showSuccessModal(response.message);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Terjadi kesalahan saat memproses verifikasi.'
                        });

                        btn.prop('disabled', false).html(originalText);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseText);

                    let errorMessage = 'Terjadi kesalahan saat memproses verifikasi.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });

                    btn.prop('disabled', false).html(originalText);
                }
            });
        }

        function markDocumentSigned(document) {
            // Update UI to show document is signed
            const $tab = $(`[data-document="${document}"]`);

            if (!$tab.hasClass('text-success')) {
                $tab.addClass('text-success').prepend('<i class="fas fa-check-circle mr-1"></i>');
            }

            // Disable corresponding passphrase input
            $(`#passphrase-${document}`).prop('disabled', true).addClass('bg-light');
        }

        function showSuccessModal(message) {
            $('#success-message').text(message);
            $('#successModal').modal('show');

            // Clear countdown
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }

            $('#countdown-timer').removeClass('warning expired')
                .addClass('success')
                .html('<i class="fas fa-check-circle mr-2"></i>Verifikasi Selesai');

            // Disable all inputs
            $('input[type="password"], .btn-verify-all').prop('disabled', true);
        }

        function closeSuccessModal() {
            $('#successModal').modal('hide');

            // Optional: Redirect or close window
            setTimeout(() => {
                window.close(); // Close the window/tab
                // Or redirect: window.location.href = '/some-success-page';
            }, 1000);
        }

        // Handle Enter key for passphrase inputs
        $(document).on('keypress', 'input[type="password"]', function(e) {
            if (e.which === 13) { // Enter key
                if ($(this).attr('id') === 'global-passphrase') {
                    verifyAllDocuments();
                }
            }
        });

        // Handle PDF loading errors
        $(document).on('error', 'iframe.pdf-preview', function() {
            $(this).replaceWith(`
                <div class="pdf-preview d-flex align-items-center justify-content-center bg-light">
                    <div class="text-center text-muted">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <p>Gagal memuat preview dokumen</p>
                        <small>Dokumen tetap dapat ditandatangani</small>
                    </div>
                </div>
            `);
        });
    </script>
</body>
</html>
