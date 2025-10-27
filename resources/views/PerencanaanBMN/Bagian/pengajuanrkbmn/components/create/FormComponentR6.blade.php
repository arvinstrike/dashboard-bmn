{{-- resources/views/PerencanaanBMN/Bagian/pengajuanrkbmn/components/FormComponentR6.blade.php --}}
{{-- Form Component untuk R6 - Kendaraan Fungsional/Alat Angkutan Bermotor di Atas Air --}}

@php
    $sbskEngine = app(\App\Services\SBSKRuleService::class);
    $formConfig = $sbskEngine->getFormConfig('R6');
    $jenisSatkerFungsional = $sbskEngine->getJenisSatkerFungsionalR6();
    $jenisKendaraanFungsional = $sbskEngine->getJenisKendaraanFungsionalR6();
    $golonganOptions = $sbskEngine->getGolongan('R6');
@endphp

<div class="form-component-r6" data-jenis="R6">
    {{-- Spesifikasi Kendaraan Fungsional Section --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Spesifikasi Kendaraan Fungsional</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="jenis_satker_fungsional">Jenis Satker <span class="text-danger">*</span></label>
                        <select name="jenis_satker_fungsional" id="jenis_satker_fungsional" class="form-control"
                                required>
                            <option value="">-- Pilih Jenis Satker --</option>
                            @foreach($jenisSatkerFungsional as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="jenis_kendaraan_fungsional">Jenis Kendaraan <span class="text-danger">*</span></label>
                        {{-- SIMPLE VERSION: Basic optgroup without JavaScript enhancements --}}
                        <select name="jenis_kendaraan_fungsional" id="jenis_kendaraan_fungsional" class="form-control" required>
                            <option value="">-- Pilih Jenis Kendaraan Fungsional --</option>
                            <optgroup label="Kendaraan Layanan Publik">
                                <option value="microbus_5000cc">Microbus (5.000 cc, 4 silinder)</option>
                                <option value="mpv_fungsional">Mobil MPV</option>
                                <option value="minibus_2500cc">Minibus (2.500 cc, 4 silinder)</option>
                                <option value="suv_double_gardan">SUV Double Gardan/Double Cabin</option>
                            </optgroup>
                            <optgroup label="Kendaraan Angkutan Barang">
                                <option value="pickup_3000cc">Pickup (3.000 cc, 4 silinder, karoseri bak terbuka/tertutup)</option>
                            </optgroup>
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

            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> Catatan Penting</h6>
                        <ul class="mb-0">
                            <li>Kendaraan fungsional memiliki spesifikasi khusus sesuai dengan fungsi operasionalnya</li>
                            <li>Pemilihan harus disesuaikan dengan kebutuhan operasional dan jenis satker</li>
                            <li>Tujuan penggunaan menentukan prioritas dan justifikasi pengadaan</li>
                            <li>Pastikan kendaraan yang dipilih memiliki sertifikasi dan kelengkapan yang sesuai standar</li>
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
    .category-group {
        margin-bottom: 15px;
    }

    .category-group strong {
        color: #0c5460;
        display: block;
        margin-bottom: 5px;
    }

    .category-group ul {
        margin-left: 15px;
    }

    .alert-info h6, .alert-warning h6, .alert-success h6 {
        margin-bottom: 10px;
    }

    .alert-warning ul {
        padding-left: 20px;
    }

    .small {
        font-size: 0.875rem;
    }
</style>

<script>
    /**
     * =====================================================
     * R6 KENDARAAN FUNGSIONAL COMPONENT - SIMPLE VERSION
     * =====================================================
     * SIMPLE: Basic functionality without enhancements
     */

    const R6_COMPONENT = {
        jenis: 'R6',
        initialized: false,

        init: function () {
            if (this.initialized) {
                this.cleanup();
            }

            console.log('Initializing R6 Component (Simple Version)...');
            this.setupEventHandlers();
            this.initializeForm();
            this.initialized = true;
            console.log('R6 Component initialized');
        },

        cleanup: function () {
            console.log('Cleaning up R6 Component...');

            // Unbind R6-specific events
            $('#jenis_satker_fungsional').off('change.r6');
            $('#jenis_kendaraan_fungsional').off('change.r6');
            $('#tujuan_penggunaan').off('change.r6');
            $('#kuantitas, #harga_barang').off('input.r6');

            this.initialized = false;
        },

        setupEventHandlers: function () {
            // Basic event handlers
            $('#jenis_satker_fungsional').on('change.r6', this.handleJenisSatkerChange.bind(this));
            $('#jenis_kendaraan_fungsional').on('change.r6', this.handleJenisKendaraanChange.bind(this));

            // Calculation handlers
            $('#kuantitas, #harga_barang').on('input.r6', this.calculateTotal.bind(this));
        },

        initializeForm: function () {
            console.log('R6 form initialized - ready for input');
        },

        handleJenisSatkerChange: function () {
            const jenisSatker = $('#jenis_satker_fungsional').val();
            console.log('Jenis Satker changed to:', jenisSatker);
        },

        handleJenisKendaraanChange: function () {
            const jenisKendaraan = $('#jenis_kendaraan_fungsional').val();
            console.log('Jenis Kendaraan changed to:', jenisKendaraan);
        },

        calculateTotal: function () {
            const kuantitas = parseInt($('#kuantitas').val()) || 0;
            const harga = parseInt($('#harga_barang').val().replace(/[^\d]/g, '')) || 0;
            const total = kuantitas * harga;

            if (total > 0) {
                $('#total_anggaran').val(this.formatCurrency(total));
            } else {
                $('#total_anggaran').val('');
            }

            console.log('R6 Total calculated:', this.formatCurrency(total));
        },

        formatCurrency: function (amount) {
            return new Intl.NumberFormat('id-ID').format(amount);
        }
    };

    // Initialize when document ready and this is R6 component
    $(document).ready(function () {
        if ($('.form-component-r6').length > 0) {
            R6_COMPONENT.init();
        }
    });

    // Cleanup when component changes
    $(document).on('component:change', function (e, newJenis) {
        if (newJenis !== 'R6' && R6_COMPONENT.initialized) {
            R6_COMPONENT.cleanup();
        }
    });
</script>
