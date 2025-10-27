{{-- resources/views/PerencanaanBMN/Bagian/pengajuanrkbmn/components/FormComponentR2.blade.php --}}
{{-- Form Component untuk R2 - Tanah dan/atau Bangunan Pendidikan (PLACEHOLDER) --}}

<div class="form-component-r2">
    {{-- Placeholder untuk R2 yang belum diimplementasi --}}
    <div class="card mb-4">
        <div class="card-header bg-warning">
            <h3 class="card-title text-dark">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                R2 - Tanah dan/atau Bangunan Pendidikan
            </h3>
        </div>
        <div class="card-body text-center">
            <div class="alert alert-warning mb-4">
                <h5><i class="fas fa-construction mr-2"></i>Fitur Dalam Pengembangan</h5>
                <p class="mb-0">
                    Form untuk jenis pengajuan R2 (Tanah dan/atau Bangunan Pendidikan)
                    sedang dalam tahap pengembangan dan belum tersedia.
                </p>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-info">
                        <div class="card-body">
                            <h6 class="card-title">Informasi Pengembangan</h6>
                            <p class="card-text">
                                Fitur ini akan mencakup:
                            </p>
                            <ul class="list-unstyled text-left">
                                <li><i class="fas fa-check-circle text-muted mr-2"></i>Spesifikasi bangunan pendidikan</li>
                                <li><i class="fas fa-check-circle text-muted mr-2"></i>Klasifikasi gedung sekolah/universitas</li>
                                <li><i class="fas fa-check-circle text-muted mr-2"></i>Detail ruang kelas dan fasilitas</li>
                                <li><i class="fas fa-check-circle text-muted mr-2"></i>Validasi standar pendidikan</li>
                                <li><i class="fas fa-check-circle text-muted mr-2"></i>Integrasi dengan SBSK pendidikan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <p class="text-muted">
                    <strong>Estimasi Penyelesaian:</strong> Q2 2025<br>
                    <strong>Status:</strong> Menunggu spesifikasi detail dari stakeholder
                </p>
            </div>

            <div class="mt-4">
                <button type="button" class="btn btn-secondary" onclick="goBackToSelection()">
                    <i class="fas fa-arrow-left mr-1"></i>Kembali ke Pilihan Jenis Pengajuan
                </button>
            </div>
        </div>
    </div>

    {{-- Hidden inputs untuk compatibility --}}
    <input type="hidden" name="jenis_pengajuan_status" value="not_implemented">
    <input type="hidden" name="implementation_status" value="placeholder">
</div>

<script>
$(document).ready(function() {
    // Initialize R2 placeholder
    initializeR2Placeholder();

    function initializeR2Placeholder() {
        console.log('R2 Placeholder initialized - Feature not yet implemented');

        // Disable form submission for R2
        disableFormSubmission();

        // Show notification
        showImplementationNotice();
    }

    function disableFormSubmission() {
        // Disable submit button when R2 is selected
        $('#submitBtn').prop('disabled', true).html('<i class="fas fa-ban mr-1"></i>Fitur Belum Tersedia');

        // Add warning class
        $('#submitBtn').removeClass('btn-success').addClass('btn-warning');
    }

    function showImplementationNotice() {
        // Show notification about R2 implementation status
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: 'Form R2 (Bangunan Pendidikan) sedang dalam pengembangan. Silakan pilih jenis pengajuan lain.',
                timer: 5000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    }

    // Global function to go back to selection
    window.goBackToSelection = function() {
        // Reset jenis pengajuan dropdown
        $('#jenistabel').val('').trigger('change');

        // Show success message
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'Silakan Pilih Jenis Pengajuan Lain',
                text: 'Pilih jenis pengajuan yang tersedia (R1, R3, R4, R5, atau R6)',
                timer: 3000,
                showConfirmButton: false
            });
        }
    };

    console.log('R2 Placeholder script loaded');
});
</script>

<style>
/* Custom styles for R2 placeholder */
.form-component-r2 .card-header.bg-warning {
    border-bottom: 2px solid #856404;
}

.form-component-r2 .alert-warning {
    border-left: 4px solid #ffc107;
}

.form-component-r2 .list-unstyled li {
    padding: 5px 0;
}

.form-component-r2 .card.border-info {
    border-width: 2px !important;
}

/* Animation for the construction icon */
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.form-component-r2 .fa-construction {
    animation: bounce 2s infinite;
}
</style>
