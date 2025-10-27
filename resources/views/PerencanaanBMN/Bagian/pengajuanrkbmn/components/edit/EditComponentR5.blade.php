@php
    $sbskEngine = app(\App\Services\SBSKRuleService::class);
    $formConfig = $sbskEngine->getFormConfig('R5');
    $jenisSatkerOperasional = $sbskEngine->getJenisSatkerOperasionalR5();
    $jenisKendaraanOperasional = $sbskEngine->getJenisKendaraanOperasionalR5();
    $golonganOptions = $sbskEngine->getGolongan('R5');
    $tujuanOptions = $sbskEngine->getTujuanRencanaOptions();
    $atrOptions = $sbskEngine->getAtrNonAtrOptions();
@endphp

<div class="form-component-r5-edit" data-jenis="R5">
    {{-- Spesifikasi Kendaraan Operasional Section --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Spesifikasi Kendaraan Operasional</h3>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- UPDATED: Layout changed to col-md-4 --}}
                <div class="col-md-4 form-group">
                    <label for="jenis_satker_operasional">Jenis Satker <span class="text-danger">*</span></label>
                    <select name="jenis_satker_operasional" id="jenis_satker_operasional" class="form-control" required>
                        <option value="">-- Pilih Jenis Satker --</option>
                        @foreach($jenisSatkerOperasional as $key => $value)
                            <option value="{{ $key }}"
                                    @if(optional($detailData)->jenis_satker == $key) selected @endif>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- UPDATED: Layout changed to col-md-4 --}}
                <div class="col-md-4 form-group">
                    <label for="jenis_kendaraan_operasional">Jenis Kendaraan <span class="text-danger">*</span></label>
                    <select name="jenis_kendaraan_operasional" id="jenis_kendaraan_operasional" class="form-control"
                            required>
                        <option value="">-- Pilih Jenis Kendaraan --</option>
                        @foreach($jenisKendaraanOperasional as $key => $value)
                            <option value="{{ $key }}"
                                    @if(optional($detailData)->jenis_kendaraan == $key) selected @endif>{{ $value }}</option>
                        @endforeach
                    </select>
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

            {{-- Info Section --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Informasi Kendaraan Operasional</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Sepeda Motor 225 cc</strong><br>
                                <small>Untuk keperluan operasional harian dan mobilitas staff</small>
                            </div>
                            <div class="col-md-4">
                                <strong>Mobil MPV 1.500 cc</strong><br>
                                <small>Untuk keperluan operasional dan transportasi tim</small>
                            </div>
                            <div class="col-md-4">
                                <strong>Kendaraan Jabatan Dialihfungsikan</strong><br>
                                <small>Kendaraan jabatan yang dialihfungsikan untuk operasional</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> Catatan Penting</h6>
                        <ul class="mb-0">
                            <li>Kendaraan operasional dapat berupa <strong>ATR</strong> atau <strong>Non ATR</strong>
                                tergantung kebutuhan
                            </li>
                            <li>Jenis satker menentukan kebutuhan operasional kendaraan yang diperlukan</li>
                            <li>Sepeda motor digunakan untuk mobilitas individual, MPV untuk keperluan tim</li>
                            <li>Kendaraan jabatan yang dialihfungsikan harus memenuhi kriteria operasional</li>
                        </ul>
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
                    <textarea name="uraian_barang" id="uraian_barang" class="form-control"
                              rows="3" required>{{ $data->uraian_barang }}</textarea>
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
    .alert-info h6, .alert-warning h6 {
        margin-bottom: 10px;
    }

    .alert-info .row .col-md-4 {
        border-right: 1px solid #bee5eb;
    }

    .alert-info .row .col-md-4:last-child {
        border-right: none;
    }

    .alert-warning ul {
        padding-left: 20px;
    }
</style>

<script>
    /**
     * =====================================================
     * R5 EDIT COMPONENT - COMPLETE FIXED VERSION
     * =====================================================
     * Complete form logic from create form
     */

    function initializeR5EditScripts() {
        console.log('Initializing R5 Edit Scripts with Complete Logic...');

        const jenisForm = $('#jenistabel_hidden').val() || 'R5';

        // ==========================================
        // R5 FORM LOGIC (Copied from FormComponentR5)
        // ==========================================

        /**
         * Handle jenis satker change
         */
        function handleJenisSatkerChange() {
            const jenisSatker = $('#jenis_satker_operasional').val();
            console.log('R5 Edit: Jenis Satker changed to:', jenisSatker);
            showSatkerInfo(jenisSatker);
        }

        /**
         * Handle jenis kendaraan change
         */
        function handleJenisKendaraanChange() {
            const jenisKendaraan = $('#jenis_kendaraan_operasional').val();
            console.log('R5 Edit: Jenis Kendaraan changed to:', jenisKendaraan);
            showKendaraanInfo(jenisKendaraan);
        }

        /**
         * Show satker information (placeholder for future enhancement)
         */
        function showSatkerInfo(jenisSatker) {
            // Future enhancement: Show specific info based on satker type
            console.log('R5 Edit: Satker info for:', jenisSatker);
        }

        /**
         * Show kendaraan information (placeholder for future enhancement)
         */
        function showKendaraanInfo(jenisKendaraan) {
            // Future enhancement: Show specific info based on vehicle type
            console.log('R5 Edit: Kendaraan info for:', jenisKendaraan);
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

        // R5-specific handlers
        $('#jenis_satker_operasional').off('change.r5edit').on('change.r5edit', handleJenisSatkerChange);
        $('#jenis_kendaraan_operasional').off('change.r5edit').on('change.r5edit', handleJenisKendaraanChange);

        // Calculation handlers
        $('#kuantitas, #harga_barang').off('input.r5edit').on('input.r5edit', calculateTotal);

        // Currency formatting
        $('.currency-input').off('input.r5edit').on('input.r5edit', function () {
            let value = $(this).val().replace(/[^\d]/g, '');
            if (value) {
                $(this).val(formatCurrency(value));
            }
        });

        // ==========================================
        // INITIAL SETUP WITH EXISTING DATA
        // ==========================================

        // Log existing data for debugging
        const existingJenisSatker = $('#jenis_satker_operasional').val();
        const existingJenisKendaraan = $('#jenis_kendaraan_operasional').val();

        if (existingJenisSatker) {
            console.log('R5 Edit: Found existing jenis satker:', existingJenisSatker);
            showSatkerInfo(existingJenisSatker);
        }

        if (existingJenisKendaraan) {
            console.log('R5 Edit: Found existing jenis kendaraan:', existingJenisKendaraan);
            showKendaraanInfo(existingJenisKendaraan);
        }

        console.log('R5 Edit Scripts initialized successfully');
    }
</script>
