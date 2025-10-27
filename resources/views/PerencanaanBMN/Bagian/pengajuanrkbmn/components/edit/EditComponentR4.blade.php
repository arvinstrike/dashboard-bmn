@php
    $sbskEngine = app(\App\Services\SBSKRuleService::class);
    $formConfig = $sbskEngine->getFormConfig('R4');
    $klasifikasiPejabatKendaraan = $sbskEngine->getKlasifikasiPejabatKendaraanR4();
    $allSpesifikasiKendaraan = $sbskEngine->getAllSpesifikasiKendaraanR4();
    $golonganOptions = $sbskEngine->getGolongan('R4');
    $pejabatToSpesifikasiMapping = $sbskEngine->getPejabatToSpesifikasiMappingR4();
@endphp

<div class="form-component-r4-edit" data-jenis="R4">
    {{-- Spesifikasi Kendaraan Jabatan Section --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Spesifikasi Kendaraan Jabatan</h3>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- UPDATED: Layout changed to col-md-4 --}}
                <div class="col-md-4 form-group">
                    <label for="klasifikasi_pejabat_kendaraan">Klasifikasi Pejabat <span
                            class="text-danger">*</span></label>
                    <select name="klasifikasi_pejabat_kendaraan" id="klasifikasi_pejabat_kendaraan" class="form-control"
                            required>
                        <option value="">-- Pilih Klasifikasi Pejabat --</option>
                        @foreach($klasifikasiPejabatKendaraan as $key => $value)
                            <option value="{{ $key }}"
                                    @if(optional($detailData)->klasifikasi_pejabat == $key) selected @endif>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- UPDATED: Layout changed to col-md-4 --}}
                <div class="col-md-4 form-group">
                    <label for="spesifikasi_kendaraan">Spesifikasi Kendaraan <span class="text-danger">*</span></label>
                    <select name="spesifikasi_kendaraan" id="spesifikasi_kendaraan" class="form-control" required
                            disabled>
                        <option value="">-- Pilih Spesifikasi Kendaraan --</option>
                    </select>
                    <div id="spesifikasi_feedback" class="feedback-text"></div>
                </div>
                {{-- ADDED: Skema Pengadaan dropdown --}}
                <div class="col-md-4 form-group">
                    <label for="skema">Skema Pengadaan <span class="text-danger">*</span></label>
                    <select name="skema" id="skema" class="form-control" required>
                        <option value="">-- Pilih Skema --</option>
                        <option value="beli" @if($data->skema == 'beli') selected @endif>Beli</option>
                        <option value="sewa" @if($data->skema == 'sewa') selected @endif>Sewa</option>
                    </select>
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
                <div class="col-md-3 form-group">
                    <label for="golongan">Golongan <span class="text-danger">*</span></label>
                    <select name="golongan" id="golongan" class="form-control" required>
                        <option value="">-- Pilih Golongan --</option>
                        @foreach($golonganOptions as $option)
                            <option value="{{ $option['value'] }}">{{ $option['text'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label for="bidang">Bidang <span class="text-danger">*</span></label>
                    <select name="bidang" id="bidang" class="form-control" required disabled>
                        <option value="">-- Pilih Bidang --</option>
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label for="kelompok">Kelompok <span class="text-danger">*</span></label>
                    <select name="kelompok" id="kelompok" class="form-control" required disabled>
                        <option value="">-- Pilih Kelompok --</option>
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label for="subkelompok">Sub Kelompok <span class="text-danger">*</span></label>
                    <select name="subkelompok" id="subkelompok" class="form-control" required disabled>
                        <option value="">-- Pilih Sub Kelompok --</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="barang">Barang <span class="text-danger">*</span></label>
                    <select name="barang" id="barang" class="form-control" required disabled>
                        <option value="">-- Pilih Barang --</option>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label for="kode_barang">Kode Barang</label>
                    <input type="text" name="kode_barang" id="kode_barang" class="form-control"
                           value="{{ $data->kode_barang }}" readonly>
                </div>
            </div>

            {{-- Anggaran dan Detail --}}
            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="kuantitas">Kuantitas <span class="text-danger">*</span></label>
                    <input type="number" name="kuantitas" id="kuantitas" class="form-control"
                           min="1" value="{{ $data->kuantitas }}" required>
                </div>
                <div class="col-md-4 form-group">
                    <label for="harga_barang">Harga Barang (Rp) <span class="text-danger">*</span></label>
                    <input type="text" name="harga_barang" id="harga_barang" class="form-control currency-input"
                           value="{{ number_format($data->harga_barang, 0, ',', '.') }}" required>
                </div>
                <div class="col-md-4 form-group">
                    <label for="total_anggaran">Total Anggaran (Rp)</label>
                    <input type="text" name="total_anggaran" id="total_anggaran" class="form-control currency-input"
                           value="{{ number_format($data->total_anggaran, 0, ',', '.') }}" readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 form-group">
                    <label for="uraian_barang">Uraian Barang <span class="text-danger">*</span></label>
                    <textarea name="uraian_barang" id="uraian_barang" class="form-control" rows="3"
                              required>{{ $data->uraian_barang }}</textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" class="form-control"
                              rows="2">{{ $data->keterangan }}</textarea>
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

    .spec-type-group {
        margin-bottom: 10px;
    }

    .spec-type-title {
        font-weight: bold;
        color: #0c5460;
        margin-bottom: 5px;
    }

    .spec-item {
        font-size: 0.875rem;
        padding: 2px 0;
    }

    .spec-item.allowed {
        color: #0c5460;
    }
</style>

<script>
    /**
     * =====================================================
     * R4 EDIT COMPONENT - COMPLETE FIXED VERSION
     * =====================================================
     * Complete restrictions + validation logic from create form
     */

    function initializeR4EditScripts() {
        console.log('Initializing R4 Edit Scripts with Complete Restrictions...');

        const jenisForm = $('#jenistabel_hidden').val() || 'R4';
        const pejabatToSpesifikasiMapping = @json($pejabatToSpesifikasiMapping);
        const allSpesifikasiKendaraan = @json($allSpesifikasiKendaraan);

        // ==========================================
        // R4 RESTRICTION LOGIC (Copied from FormComponentR4)
        // ==========================================

        /**
         * Handle klasifikasi pejabat change
         */
        function handleKlasifikasiChange() {
            const klasifikasi = $('#klasifikasi_pejabat_kendaraan').val();
            console.log('R4 Edit: Klasifikasi changed to:', klasifikasi);

            if (klasifikasi) {
                loadAllowedSpesifikasi(klasifikasi);
                showAllowedSpecsInfo(klasifikasi);
            } else {
                resetSpesifikasiDropdown();
                $('#allowed_specs_info').hide();
            }
        }

        /**
         * Load allowed spesifikasi based on klasifikasi
         */
        function loadAllowedSpesifikasi(klasifikasi) {
            const allowedSpecs = pejabatToSpesifikasiMapping[klasifikasi] || [];
            console.log('R4 Edit: Loading allowed specs for:', klasifikasi, allowedSpecs);

            const $spesifikasiSelect = $('#spesifikasi_kendaraan');
            $spesifikasiSelect.prop('disabled', false);

            let options = '<option value="">-- Pilih Spesifikasi Kendaraan --</option>';
            allowedSpecs.forEach(spec => {
                options += `<option value="${spec}">${spec}</option>`;
            });

            $spesifikasiSelect.html(options);

            // Pre-select existing spesifikasi if it exists and is allowed
            const existingSpesifikasi = '{{ optional($detailData)->spesifikasi_kendaraan }}';
            if (existingSpesifikasi && allowedSpecs.includes(existingSpesifikasi)) {
                $spesifikasiSelect.val(existingSpesifikasi);
                validateSpesifikasi(); // Validate the pre-selected value
            }

            setFeedback('spesifikasi', '', '');
        }

        /**
         * Reset spesifikasi dropdown
         */
        function resetSpesifikasiDropdown() {
            $('#spesifikasi_kendaraan')
                .prop('disabled', true)
                .html('<option value="">-- Pilih Spesifikasi Kendaraan --</option>');
            setFeedback('spesifikasi', '', '');
        }

        /**
         * Show allowed specs info
         */
        function showAllowedSpecsInfo(klasifikasi) {
            const allowedSpecs = pejabatToSpesifikasiMapping[klasifikasi] || [];

            let content = '<div class="row">';
            const groupedSpecs = groupSpecificationsByType(allowedSpecs);

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
        }

        /**
         * Group specifications by type
         */
        function groupSpecificationsByType(specs) {
            const grouped = {};
            specs.forEach(spec => {
                const type = spec.charAt(0);
                if (!grouped[type]) grouped[type] = [];
                grouped[type].push(spec);
            });
            return grouped;
        }

        /**
         * Validate spesifikasi against klasifikasi
         */
        function validateSpesifikasi() {
            const klasifikasi = $('#klasifikasi_pejabat_kendaraan').val();
            const spesifikasi = $('#spesifikasi_kendaraan').val();

            if (!klasifikasi || !spesifikasi) {
                setFeedback('spesifikasi', '', '');
                return;
            }

            const allowedSpecs = pejabatToSpesifikasiMapping[klasifikasi] || [];
            if (allowedSpecs.includes(spesifikasi)) {
                setFeedback('spesifikasi', 'Spesifikasi sesuai dengan klasifikasi pejabat', 'success');
            } else {
                setFeedback('spesifikasi', 'Spesifikasi tidak diizinkan untuk klasifikasi pejabat ini', 'danger');
            }
        }

        /**
         * Set feedback message
         */
        function setFeedback(field, message, type) {
            const feedbackEl = $(`#${field}_feedback`);
            feedbackEl.removeClass('text-success text-danger text-warning');
            if (message) {
                feedbackEl.addClass(`text-${type}`).text(message).show();
            } else {
                feedbackEl.hide();
            }
        }

        // ==========================================
        // UTILITY FUNCTIONS
        // ==========================================

        /**
         * Calculate total anggaran
         */
        function calculateTotal() {
            const kuantitas = parseInt($('#kuantitas').val()) || 0;
            const hargaText = $('#harga_barang').val().replace(/[^\d]/g, '');
            const harga = parseInt(hargaText) || 0;
            const total = kuantitas * harga;

            $('#total_anggaran').val(formatCurrency(total));
        }

        /**
         * Format currency
         */
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID').format(amount);
        }

        // ==========================================
        // EVENT HANDLERS SETUP
        // ==========================================

        // R4-specific restriction handlers
        $('#klasifikasi_pejabat_kendaraan').off('change.r4edit').on('change.r4edit', handleKlasifikasiChange);
        $('#spesifikasi_kendaraan').off('change.r4edit').on('change.r4edit', validateSpesifikasi);

        // Calculation handlers
        $('#kuantitas, #harga_barang').off('input.r4edit').on('input.r4edit', calculateTotal);

        // Currency formatting
        $('.currency-input').off('input.r4edit').on('input.r4edit', function () {
            let value = $(this).val().replace(/[^\d]/g, '');
            if (value) {
                $(this).val(formatCurrency(value));
            }
        });

        // ==========================================
        // INITIAL SETUP WITH EXISTING DATA
        // ==========================================

        // Apply restrictions based on existing data
        const existingKlasifikasi = $('#klasifikasi_pejabat_kendaraan').val();

        if (existingKlasifikasi) {
            console.log('R4 Edit: Applying initial restrictions with existing klasifikasi:', existingKlasifikasi);
            handleKlasifikasiChange();
        } else {
            console.log('R4 Edit: No existing klasifikasi data found');
            $('#spesifikasi_kendaraan').prop('disabled', true);
            $('#allowed_specs_info').hide();
        }

        console.log('R4 Edit Scripts initialized successfully');
    }
</script>
