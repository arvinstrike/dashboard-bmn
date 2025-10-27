<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi Digital - Pengajuan RKBMN SBSK</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">

    <style>
        body { background: linear-gradient(135deg, #0f2e64 0%, #3e1b6b 100%); min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .verification-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .verification-card { background: white; border-radius: 15px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); overflow: hidden; max-width: 1200px; width: 100%; }
        .card-header-custom { background: linear-gradient(135deg, #0b3d91 0%, #4a235a 100%); color: white; padding: 30px; text-align: center; }
        .countdown-timer { background: #ff6b6b; color: white; padding: 15px; border-radius: 10px; text-align: center; margin-bottom: 20px; font-size: 1.2em; font-weight: bold; }
        .countdown-timer.warning { background: #ffa726; }
        .countdown-timer.expired { background: #ef5350; }
        .countdown-timer.success { background: #10b981; }
        .document-tabs { border-bottom: 1px solid #dee2e6; margin-bottom: 20px; }
        .document-tab { display: inline-block; padding: 12px 20px; margin-right: 5px; background: #f8f9fa; border: 1px solid #dee2e6; border-bottom: none; border-radius: 8px 8px 0 0; cursor: pointer; transition: all 0.3s ease; }
        .document-tab.active { background: #0b3d91; color: white; border-color: #0b3d91; }
        .document-tab:hover { background: #e9ecef; }
        .document-tab.active:hover { background: #0a347a; }
        .document-content { display: none; }
        .document-content.active { display: block; }
        .pdf-preview { border: 1px solid #dee2e6; border-radius: 8px; height: 500px; width: 100%; background: #f8f9fa; }
        .info-section { background: #f8f9fa; border-radius: 10px; padding: 20px; margin-bottom: 20px; }
        .btn-verify { background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; padding: 15px 40px; border-radius: 8px; color: white; font-weight: 600; font-size: 1.1em; transition: all 0.3s ease; }
        .btn-verify:hover { transform: translateY(-2px); box-shadow: 0 8px 15px rgba(16, 185, 129, 0.3); color: white; }
        .status-page { text-align: center; padding: 60px 20px; }
        .status-icon { font-size: 4rem; margin-bottom: 20px; }
        .status-icon.success { color: #10b981; }
        .status-icon.error { color: #ef4444; }
        .status-icon.warning { color: #f59e0b; }
    </style>
</head>
<body>

    <div class="verification-container">
        <div class="verification-card">
            @if($status !== 'valid')
                {{-- Halaman Status (Error, Expired, dll.) --}}
                <div class="status-page">
                    @if($status === 'error')
                        <div class="status-icon error"><i class="fas fa-exclamation-triangle"></i></div>
                        <h3 class="text-danger">Terjadi Kesalahan</h3>
                    @elseif($status === 'expired')
                        <div class="status-icon warning"><i class="fas fa-clock"></i></div>
                        <h3 class="text-warning">Link Kedaluwarsa</h3>
                    @elseif($status === 'already_verified')
                        <div class="status-icon success"><i class="fas fa-check-circle"></i></div>
                        <h3 class="text-success">Sudah Terverifikasi</h3>
                    @endif
                    <p class="text-muted">{{ $message }}</p>
                </div>
            @else
                {{-- Halaman Verifikasi Utama --}}
                <div class="card-header-custom">
                    <h2><i class="fas fa-signature mr-3"></i>Verifikasi Digital</h2>
                    <p class="mb-0">Pengajuan RKBMN SBSK ({{ $pengajuan->kode_jenis_pengajuan }})</p>
                </div>

                <div class="card-body p-4">
                    <div class="countdown-timer" id="countdown-timer">
                        <i class="fas fa-clock mr-2"></i> <span id="countdown-text">Menghitung waktu...</span>
                    </div>

                    {{-- Informasi Pengajuan Umum --}}
                    <div class="info-section">
                        <h5><i class="fas fa-info-circle mr-2 text-primary"></i>Informasi Pengajuan</h5>
                        <div class="row">
                            <div class="col-md-6"><small class="text-muted">Kode Pengajuan:</small><div class="font-weight-bold">{{ $pengajuan->kode_jenis_pengajuan }}</div></div>
                            <div class="col-md-6"><small class="text-muted">Tahun Anggaran:</small><div class="font-weight-bold">{{ $pengajuan->tahun_anggaran }}</div></div>
                            <div class="col-md-6"><small class="text-muted">Bagian Pengusul:</small><div class="font-weight-bold">{{ $bagianPengusul->uraianbagian ?? '-' }}</div></div>
                            <div class="col-md-6"><small class="text-muted">Total Anggaran:</small><div class="font-weight-bold text-success">Rp {{ number_format($pengajuan->total_anggaran, 0, ',', '.') }}</div></div>
                        </div>
                    </div>

                    {{-- Rangkuman Detail SBSK --}}
                    <div class="info-section">
                        <h5><i class="fas fa-list-alt mr-2 text-primary"></i>Rangkuman Pengajuan SBSK</h5>
                        <div class="row">
                            <div class="col-md-12"><small class="text-muted">Uraian Barang:</small><div class="font-weight-bold">{{ $pengajuan->uraian_barang ?? '-' }}</div></div>
                            {{-- Tampilkan detail spesifik berdasarkan jenis pengajuan --}}
                            @if($jenisPengajuan === 'R1' && isset($detailData))
                                <div class="col-md-6"><small class="text-muted">Klasifikasi Bangunan:</small><div class="font-weight-bold">{{ $detailData->klasifikasi_bangunan ?? '-' }}</div></div>
                                <div class="col-md-6"><small class="text-muted">Klasifikasi Pejabat:</small><div class="font-weight-bold">{{ $detailData->klasifikasi_pejabat ?? '-' }}</div></div>
                            @elseif($jenisPengajuan === 'R3' && isset($detailData))
                                <div class="col-md-6"><small class="text-muted">Peruntukan Pejabat:</small><div class="font-weight-bold">{{ $detailData->peruntukan_pejabat ?? '-' }}</div></div>
                                <div class="col-md-6"><small class="text-muted">Lokasi Rumah:</small><div class="font-weight-bold">{{ $detailData->lokasi ?? '-' }}</div></div>
                            @elseif($jenisPengajuan === 'R4' && isset($detailData))
                                <div class="col-md-6"><small class="text-muted">Klasifikasi Pejabat:</small><div class="font-weight-bold">{{ $detailData->klasifikasi_pejabat ?? '-' }}</div></div>
                                <div class="col-md-6"><small class="text-muted">Spesifikasi Kendaraan:</small><div class="font-weight-bold">{{ $detailData->spesifikasi_kendaraan ?? '-' }}</div></div>
                            @endif
                        </div>
                    </div>

                    {{-- Antarmuka Tab untuk Dokumen --}}
                    <div class="document-tabs">
                        @foreach($documentsToShow as $doc)
                            <div class="document-tab {{ $loop->first ? 'active' : '' }}" data-document="{{ $doc }}" onclick="switchDocumentTab('{{ $doc }}')">
                                <i class="fas {{ $doc === 'berita_acara' ? 'fa-file-signature' : 'fa-paperclip' }} mr-2"></i>
                                {{ $doc === 'berita_acara' ? 'Berita Acara' : 'Dokumen Pendukung' }}
                                @if($doc === 'berita_acara')
                                    <span class="badge badge-warning ml-1">Perlu Tanda Tangan</span>
                                @else
                                    <span class="badge badge-info ml-1">Review</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Konten Dokumen (Preview PDF) --}}
                    @foreach($documentsToShow as $doc)
                        <div class="document-content {{ $loop->first ? 'active' : '' }}" id="content-{{ $doc }}">
                            <iframe class="pdf-preview" src="{{ route('magic-link-sbsk.preview-document', ['encrypted_id' => $encrypted_token, 'document_type' => $doc]) }}"></iframe>
                        </div>
                    @endforeach

                    {{-- Form Tanda Tangan --}}
                    <div class="info-section mt-4">
                         <h5><i class="fas fa-key mr-2 text-primary"></i>Tanda Tangan Digital</h5>
                         <div class="form-group mb-2">
                             <label for="passphrase">Passphrase Tanda Tangan Elektronik</label>
                             <input type="password" id="passphrase" class="form-control form-control-lg" placeholder="Masukkan passphrase Anda">
                         </div>
                         <button type="button" class="btn btn-verify btn-block mt-3" id="verify-btn" onclick="verifyDocument()">
                             <i class="fas fa-check-double mr-2"></i> Tanda Tangani Berita Acara
                         </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        const expiresAt = {{ $expires_at ?? 0 }};
        const encryptedToken = '{{ $encrypted_token ?? '' }}';
        let countdownInterval;

        // Fungsi untuk memulai countdown
        function startCountdown() {
            const expiryTime = expiresAt * 1000;
            countdownInterval = setInterval(() => {
                const distance = expiryTime - new Date().getTime();
                if (distance < 0) {
                    clearInterval(countdownInterval);
                    $('#countdown-text').text('Link telah kedaluwarsa');
                    $('#countdown-timer').removeClass('warning').addClass('expired');
                    $('#passphrase, #verify-btn').prop('disabled', true);
                    return;
                }
                const hours = Math.floor(distance / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                $('#countdown-text').text(`Sisa waktu: ${hours} jam ${minutes} menit`);
                if (hours < 1) $('#countdown-timer').addClass('warning');
            }, 1000);
        }

        // Fungsi untuk berpindah tab dokumen
        function switchDocumentTab(doc) {
            $('.document-tab, .document-content').removeClass('active');
            $(`[data-document="${doc}"], #content-${doc}`).addClass('active');
        }

        // Fungsi untuk memproses tanda tangan
        function verifyDocument() {
            const passphrase = $('#passphrase').val();
            if (!passphrase) {
                Swal.fire('Error', 'Passphrase tidak boleh kosong.', 'error');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Tanda Tangan',
                text: 'Anda akan menandatangani dokumen Berita Acara. Aksi ini tidak dapat dibatalkan. Lanjutkan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tanda Tangani',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Memproses...', text: 'Mohon tunggu, dokumen sedang ditandatangani.', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                    $.ajax({
                        url: "{{ route('pengajuanrkbmnbagian.magic-link-sbsk.process-esign', ['encrypted_id' => $encrypted_token]) }}",
                        type: 'POST',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        data: {
                            passphrase: passphrase,
                            documents: ['berita_acara'] // Hanya kirim dokumen yang akan ditandatangani
                        },
                        success: (response) => {
                            if (response.success) {
                                Swal.fire('Berhasil!', response.message, 'success').then(() => window.location.reload());
                            } else {
                                Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                            }
                        },
                        error: (xhr) => {
                            const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan pada server.';
                            Swal.fire('Error!', errorMsg, 'error');
                        }
                    });
                }
            });
        }

        @if($status === 'valid')
            startCountdown();
        @endif
    </script>
</body>
</html>

