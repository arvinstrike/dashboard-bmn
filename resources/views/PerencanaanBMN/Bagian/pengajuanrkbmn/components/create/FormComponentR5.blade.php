{{-- resources/views/PerencanaanBMN/Bagian/pengajuanrkbmn/components/FormComponentR5.blade.php --}}
{{-- Form Component untuk R5 - Kendaraan Operasional --}}

@php
    $sbskEngine = app(\App\Services\SBSKRuleService::class);
    $formConfig = $sbskEngine->getFormConfig('R5');
    $jenisSatkerOperasional = $sbskEngine->getJenisSatkerOperasionalR5();
    $jenisKendaraanOperasional = $sbskEngine->getJenisKendaraanOperasionalR5();
    $golonganOptions = $sbskEngine->getGolongan('R5');
@endphp

<div class="form-component-r5" data-jenis="R5">
    {{-- Spesifikasi Kendaraan Operasional Section --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Spesifikasi Kendaraan Operasional</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="jenis_satker_operasional">Jenis Satker <span class="text-danger">*</span></label>
                        <select name="jenis_satker_operasional" id="jenis_satker_operasional" class="form-control"
                                required>
                            <option value="">-- Pilih Jenis Satker --</option>
                            @foreach($jenisSatkerOperasional as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="jenis_kendaraan_operasional">Jenis Kendaraan <span
                                    class="text-danger">*</span></label>
                        <select name="jenis_kendaraan_operasional" id="jenis_kendaraan_operasional" class="form-control"
                                required>
                            <option value="">-- Pilih Jenis Kendaraan --</option>
                            @foreach($jenisKendaraanOperasional as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
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

            {{-- DIHAPUS: Blok input Tujuan Rencana dan ATR/Non ATR telah dihapus --}}

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
     * R5 KENDARAAN OPERASIONAL COMPONENT - FIXED VERSION
     * =====================================================
     * FIXED: Standardized field structure dan calculation
     */

    var R5_COMPONENT = {
        jenis: 'R5',
        initialized: false,

        init: function () {
            if (this.initialized) {
                this.cleanup();
            }

            console.log('Initializing R5 Component...');
            this.setupEventHandlers();
            this.initializeForm();
            this.initialized = true;
            console.log('R5 Component initialized');
        },

        cleanup: function () {
            console.log('Cleaning up R5 Component...');
            $('#jenis_satker_operasional').off('change.r5');
            $('#jenis_kendaraan_operasional').off('change.r5');
            $('#kuantitas, #harga_barang').off('input.r5');
            this.initialized = false;
        },

        setupEventHandlers: function () {
            $('#jenis_satker_operasional').on('change.r5', this.handleJenisSatkerChange.bind(this));
            $('#jenis_kendaraan_operasional').on('change.r5', this.handleJenisKendaraanChange.bind(this));

            // FIXED: Calculation handlers
            $('#kuantitas, #harga_barang').on('input.r5', this.calculateTotal.bind(this));
        },

        initializeForm: function () {
            console.log('R5 form initialized - ready for input');
        },

        handleJenisSatkerChange: function () {
            const jenisSatker = $('#jenis_satker_operasional').val();
            console.log('Jenis Satker changed to:', jenisSatker);
            this.showSatkerInfo(jenisSatker);
        },

        handleJenisKendaraanChange: function () {
            const jenisKendaraan = $('#jenis_kendaraan_operasional').val();
            console.log('Jenis Kendaraan changed to:', jenisKendaraan);
            this.showKendaraanInfo(jenisKendaraan);
        },

        showSatkerInfo: function (jenisSatker) {
            // Bisa dikembangkan untuk menampilkan info khusus per satker
        },

        showKendaraanInfo: function (jenisKendaraan) {
            // Placeholder untuk pengembangan di masa depan
            console.log('Info untuk kendaraan:', jenisKendaraan);
        },

        // FIXED: Calculate total method sesuai format R1
        calculateTotal: function () {
            const kuantitas = parseInt($('#kuantitas').val()) || 0;
            const hargaText = $('#harga_barang').val().replace(/[^\d]/g, '');
            const harga = parseInt(hargaText) || 0;
            const total = kuantitas * harga;

            $('#total_anggaran').val(this.formatCurrency(total));
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
        if ($('.form-component-r5').length > 0) {
            R5_COMPONENT.init();
        }
    });

    // Global function untuk re-initialization setelah AJAX load
    function initializeR5Scripts() {
        console.log('Re-initializing R5 scripts...');
        if (typeof R5_COMPONENT !== 'undefined') {
            R5_COMPONENT.init();
        }
    }
</script>
