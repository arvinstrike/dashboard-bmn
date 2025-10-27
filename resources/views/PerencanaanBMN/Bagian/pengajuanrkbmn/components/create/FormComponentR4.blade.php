{{-- resources/views/PerencanaanBMN/Bagian/pengajuanrkbmn/components/FormComponentR4.blade.php --}}
{{-- Form Component untuk R4 - Kendaraan Jabatan --}}

@php
    $sbskEngine = app(\App\Services\SBSKRuleService::class);
    $formConfig = $sbskEngine->getFormConfig('R4');
    $klasifikasiPejabatKendaraan = $sbskEngine->getKlasifikasiPejabatKendaraanR4();
    $allSpesifikasiKendaraan = $sbskEngine->getAllSpesifikasiKendaraanR4();
    $golonganOptions = $sbskEngine->getGolongan('R4');
    $pejabatToSpesifikasiMapping = $sbskEngine->getPejabatToSpesifikasiMappingR4();
@endphp

<div class="form-component-r4" data-jenis="R4">
    {{-- Spesifikasi Kendaraan Jabatan Section --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Spesifikasi Kendaraan Jabatan</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="klasifikasi_pejabat_kendaraan">Klasifikasi Pejabat <span
                                    class="text-danger">*</span></label>
                        <select name="klasifikasi_pejabat_kendaraan" id="klasifikasi_pejabat_kendaraan"
                                class="form-control" required>
                            <option value="">-- Pilih Klasifikasi Pejabat --</option>
                            @foreach($klasifikasiPejabatKendaraan as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="spesifikasi_kendaraan">Spesifikasi Kendaraan <span
                                    class="text-danger">*</span></label>
                        <select name="spesifikasi_kendaraan" id="spesifikasi_kendaraan" class="form-control" required
                                disabled>
                            <option value="">-- Pilih Spesifikasi Kendaraan --</option>
                        </select>
                        <div id="spesifikasi_feedback" class="feedback-text"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="skema">Skema Pengadaan <span class="text-danger">*</span></label>
                        <select name="skema" id="skema" class="form-control" required>
                            <option value="">-- Pilih Skema --</option>
                            <option value="beli">Beli</option>
                            <option value="sewa">Sewa</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Info Spesifikasi yang Diizinkan --}}
            <div id="allowed_specs_info" style="display: none;">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> Spesifikasi Kendaraan yang Diizinkan</h6>
                    <div id="allowed_specs_list">
                        </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> Catatan Penting</h6>
                        <p class="mb-0">Kendaraan dinas jabatan adalah <strong>Non ATR</strong> dan hanya dapat
                            digunakan oleh pejabat sesuai dengan tingkat klasifikasinya berdasarkan aturan SBSK.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Informasi Barang dan Anggaran --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Informasi Barang dan Anggaran</h3>
        </div>
        <div class="card-body">
            {{-- Dropdown Barang SBSK --}}
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="golongan">Golongan <span class="text-danger">*</span></label>
                        <select name="golongan" id="golongan" class="form-control" required>
                            <option value="">-- Pilih Golongan --</option>
                            @foreach($golonganOptions as $option)
                                <option value="{{ $option['value'] }}">{{ $option['text'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="bidang">Bidang <span class="text-danger">*</span></label>
                        <select name="bidang" id="bidang" class="form-control" required disabled>
                            <option value="">-- Pilih Bidang --</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="kelompok">Kelompok <span class="text-danger">*</span></label>
                        <select name="kelompok" id="kelompok" class="form-control" required disabled>
                            <option value="">-- Pilih Kelompok --</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="subkelompok">Sub Kelompok <span class="text-danger">*</span></label>
                        <select name="subkelompok" id="subkelompok" class="form-control" required disabled>
                            <option value="">-- Pilih Sub Kelompok --</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="barang">Barang <span class="text-danger">*</span></label>
                        <select name="barang" id="barang" class="form-control" required disabled>
                            <option value="">-- Pilih Barang --</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="kode_barang">Kode Barang</label>
                        <input type="text" name="kode_barang" id="kode_barang" class="form-control" readonly>
                    </div>
                </div>
            </div>

            {{-- FIXED: Anggaran dan Detail sesuai format R1 --}}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="kuantitas">Kuantitas <span class="text-danger">*</span></label>
                        <input type="number" name="kuantitas" id="kuantitas" class="form-control"
                               min="1" value="1" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="harga_barang">Harga Barang (Rp) <span class="text-danger">*</span></label>
                        <input type="text" name="harga_barang" id="harga_barang" class="form-control currency-input"
                               placeholder="0" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="total_anggaran">Total Anggaran (Rp)</label>
                        <input type="text" name="total_anggaran" id="total_anggaran" class="form-control currency-input"
                               readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="uraian_barang">Uraian Barang <span class="text-danger">*</span></label>
                        <textarea name="uraian_barang" id="uraian_barang" class="form-control"
                                  rows="3" placeholder="Masukkan uraian detail barang..." required></textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control"
                                  rows="2" placeholder="Masukkan keterangan tambahan..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .feedback-text {
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .feedback-text.text-success {
        color: #28a745 !important;
    }

    .feedback-text.text-danger {
        color: #dc3545 !important;
    }

    .feedback-text.text-warning {
        color: #ffc107 !important;
    }

    .alert-info h6, .alert-warning h6 {
        margin-bottom: 10px;
        color: #0c5460;
    }

    .alert-warning ul {
        padding-left: 20px;
    }
</style>

<script>
    /**
     * =====================================================
     * R4 KENDARAAN JABATAN COMPONENT - FIXED VERSION
     * =====================================================
     * FIXED: Standardized field structure dan calculation
     */

    window.R4_COMPONENT = window.R4_COMPONENT || {
        jenis: 'R4',
        initialized: false,
        pejabatToSpesifikasiMapping: @json($pejabatToSpesifikasiMapping),

        init: function () {
            if (this.initialized) {
                this.cleanup();
            }

            console.log('Initializing R4 Component...');
            this.setupEventHandlers();
            this.initializeForm();
            this.initialized = true;
            console.log('R4 Component initialized successfully');
        },

        cleanup: function () {
            console.log('Cleaning up R4 Component...');

            // Unbind R4-specific events dengan namespace
            $('#klasifikasi_pejabat_kendaraan').off('change.r4');
            $('#spesifikasi_kendaraan').off('change.r4');
            $('#kuantitas, #harga_barang').off('input.r4');

            // Reset UI state
            $('#spesifikasi_kendaraan').prop('disabled', true).html('<option value="">-- Pilih Spesifikasi Kendaraan --</option>');
            $('#allowed_specs_info').hide();
            this.clearAllFeedback();

            this.initialized = false;
        },

        setupEventHandlers: function () {
            // R4-specific event handlers dengan namespace
            $('#klasifikasi_pejabat_kendaraan').on('change.r4', this.handleKlasifikasiChange.bind(this));
            $('#spesifikasi_kendaraan').on('change.r4', this.validateSpesifikasi.bind(this));

            // FIXED: Calculation handlers
            $('#kuantitas, #harga_barang').on('input.r4', this.calculateTotal.bind(this));
        },

        initializeForm: function () {
            $('#allowed_specs_info').hide();
            this.clearAllFeedback();
        },

        handleKlasifikasiChange: function () {
            const klasifikasi = $('#klasifikasi_pejabat_kendaraan').val();
            console.log('Klasifikasi changed to:', klasifikasi);

            if (klasifikasi) {
                this.loadAllowedSpesifikasi(klasifikasi);
                this.showAllowedSpecsInfo(klasifikasi);
            } else {
                this.resetSpesifikasiDropdown();
                $('#allowed_specs_info').hide();
            }
        },

        loadAllowedSpesifikasi: function (klasifikasi) {
            const allowedSpecs = this.pejabatToSpesifikasiMapping[klasifikasi] || [];
            console.log('Loading allowed specs for:', klasifikasi, allowedSpecs);

            const $spesifikasiSelect = $('#spesifikasi_kendaraan');
            $spesifikasiSelect.prop('disabled', false);

            let options = '<option value="">-- Pilih Spesifikasi Kendaraan --</option>';
            allowedSpecs.forEach(spec => {
                options += `<option value="${spec}">${spec}</option>`;
            });

            $spesifikasiSelect.html(options);
            this.setFeedback('spesifikasi', '', '');
        },

        resetSpesifikasiDropdown: function () {
            $('#spesifikasi_kendaraan')
                .prop('disabled', true)
                .html('<option value="">-- Pilih Spesifikasi Kendaraan --</option>');
            this.setFeedback('spesifikasi', '', '');
        },

        showAllowedSpecsInfo: function (klasifikasi) {
            const allowedSpecs = this.pejabatToSpesifikasiMapping[klasifikasi] || [];

            let content = '<div class="row">';
            const groupedSpecs = this.groupSpecificationsByType(allowedSpecs);

            Object.keys(groupedSpecs).forEach(type => {
                content += `<div class="col-md-6 col-lg-4">`;
                content += `<div class="spec-type-group">`;
                content += `<div class="spec-type-title">Tipe ${type}</div>`;
                groupedSpecs[type].forEach(spec => {
                    content += `<div class="spec-item allowed">✓ ${spec}</div>`;
                });
                content += `</div></div>`;
            });

            content += '</div>';
            $('#allowed_specs_list').html(content);
            $('#allowed_specs_info').show();
        },

        groupSpecificationsByType: function (specs) {
            const grouped = {};
            specs.forEach(spec => {
                const type = spec.charAt(0);
                if (!grouped[type]) grouped[type] = [];
                grouped[type].push(spec);
            });
            return grouped;
        },

        validateSpesifikasi: function () {
            const klasifikasi = $('#klasifikasi_pejabat_kendaraan').val();
            const spesifikasi = $('#spesifikasi_kendaraan').val();

            if (!klasifikasi || !spesifikasi) {
                this.setFeedback('spesifikasi', '', '');
                return;
            }

            const allowedSpecs = this.pejabatToSpesifikasiMapping[klasifikasi] || [];
            if (allowedSpecs.includes(spesifikasi)) {
                this.setFeedback('spesifikasi', 'Spesifikasi sesuai dengan klasifikasi pejabat', 'success');
            } else {
                this.setFeedback('spesifikasi', 'Spesifikasi tidak diizinkan untuk klasifikasi pejabat ini', 'danger');
            }
        },

        // FIXED: Calculate total method sesuai format R1
        calculateTotal: function () {
            const kuantitas = parseInt($('#kuantitas').val()) || 0;
            const hargaText = $('#harga_barang').val().replace(/[^\d]/g, '');
            const harga = parseInt(hargaText) || 0;
            const total = kuantitas * harga;
            $('#total_anggaran').val(this.formatCurrency(total));
        },

        setFeedback: function (field, message, type) {
            const feedbackEl = $(`#${field}_feedback`);
            feedbackEl.removeClass('text-success text-danger text-warning');
            if (message) {
                feedbackEl.addClass(`text-${type}`).text(message).show();
            } else {
                feedbackEl.hide();
            }
        },

        clearAllFeedback: function () {
            $('.feedback-text').hide().removeClass('text-success text-danger text-warning');
        },

        // FIXED: Format currency sesuai R1
        formatCurrency: function (amount) {
            return new Intl.NumberFormat('id-ID').format(amount);
        },

        capitalizeFirst: function (str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
    };

    // Auto-initialize when document ready
    $(document).ready(function () {
        if ($('.form-component-r4').length > 0) {
            window.R4_COMPONENT.init();
        }
    });

    // Global function untuk re-initialization
    function initializeR4Scripts() {
        console.log('Re-initializing R4 scripts...');
        if (window.R4_COMPONENT) {
            window.R4_COMPONENT.init();
        }
    }
</script>
