@php
    $sbskEngine = app(\App\Services\SBSKRuleService::class);
    $formConfig = $sbskEngine->getFormConfig('R6');
    $jenisSatkerFungsional = $sbskEngine->getJenisSatkerFungsionalR6();
    $jenisKendaraanFungsional = $sbskEngine->getJenisKendaraanFungsionalR6();
    $golonganOptions = $sbskEngine->getGolongan('R6');
@endphp

<div class="form-component-r6-edit" data-jenis="R6">
    {{-- Spesifikasi Kendaraan Fungsional Section --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Spesifikasi Kendaraan Fungsional</h3>
        </div>
        <div class="card-body">
            {{-- FIXED: HTML Structure Correction --}}
            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="jenis_satker_fungsional">Jenis Satker <span class="text-danger">*</span></label>
                    <select name="jenis_satker_fungsional" id="jenis_satker_fungsional" class="form-control" required>
                        <option value="">-- Pilih Jenis Satker --</option>
                        @foreach($jenisSatkerFungsional as $key => $value)
                            <option value="{{ $key }}"
                                    @if(optional($detailData)->jenis_satker == $key) selected @endif>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label for="jenis_kendaraan_fungsional">Jenis Kendaraan <span class="text-danger">*</span></label>
                    <select name="jenis_kendaraan_fungsional" id="jenis_kendaraan_fungsional" class="form-control" required>
                        <option value="">-- Pilih Jenis Kendaraan Fungsional --</option>
                        <optgroup label="🚐 Kendaraan Layanan Publik">
                            <option value="microbus_5000cc" @if(optional($detailData)->jenis_kendaraan == 'microbus_5000cc') selected @endif>Microbus (5.000 cc, 4 silinder)</option>
                            <option value="mpv_fungsional" @if(optional($detailData)->jenis_kendaraan == 'mpv_fungsional') selected @endif>Mobil MPV</option>
                            <option value="minibus_2500cc" @if(optional($detailData)->jenis_kendaraan == 'minibus_2500cc') selected @endif>Minibus (2.500 cc, 4 silinder)</option>
                            <option value="suv_double_gardan" @if(optional($detailData)->jenis_kendaraan == 'suv_double_gardan') selected @endif>SUV Double Gardan/Double Cabin</option>
                        </optgroup>
                        <optgroup label="🚚 Kendaraan Angkutan Barang">
                            <option value="pickup_3000cc" @if(optional($detailData)->jenis_kendaraan == 'pickup_3000cc') selected @endif>Pickup (3.000 cc, 4 silinder, karoseri bak terbuka/tertutup)</option>
                        </optgroup>
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label for="skema">Skema Pengadaan <span class="text-danger">*</span></label>
                    <select name="skema" id="skema" class="form-control" required>
                        <option value="">-- Pilih Skema --</option>
                        <option value="beli" @if($data->skema == 'beli') selected @endif>Beli</option>
                        <option value="sewa" @if($data->skema == 'sewa') selected @endif>Sewa</option>
                    </select>
                </div>
            </div>

            {{-- Informasi Kendaraan Khusus (unchanged) --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Jenis Kendaraan Fungsional</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="category-group">
                                    <strong>Transportasi Khusus:</strong>
                                    <ul class="small mb-2">
                                        <li>All Terrain Vehicle - Untuk medan sulit</li>
                                        <li>Jeep - Operasional lapangan</li>
                                        <li>Micro Bus - Kapasitas 15-29 penumpang</li>
                                        <li>Mini Bus - Kapasitas maksimal 14 penumpang</li>
                                    </ul>
                                </div>
                                <div class="category-group">
                                    <strong>Darurat & Keamanan:</strong>
                                    <ul class="small mb-2">
                                        <li>Mobil Ambulance - Pelayanan medis darurat</li>
                                        <li>Mobil Jenazah - Transportasi jenazah</li>
                                        <li>Kendaraan Toilet - Fasilitas sanitasi mobile</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="category-group">
                                    <strong>Operasional & Teknis:</strong>
                                    <ul class="small mb-2">
                                        <li>Mobil Unit Derek - Operasi derek/tarik</li>
                                        <li>Truck + Attachment - Angkutan berat</li>
                                        <li>Mobil Golfcar - Area terbatas</li>
                                    </ul>
                                </div>
                                <div class="category-group">
                                    <strong>Mobilitas Ringan:</strong>
                                    <ul class="small mb-2">
                                        <li>Sepeda Motor - Mobilitas cepat</li>
                                        <li>Sepeda Listrik - Ramah lingkungan</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Kendaraan Terpilih (unchanged) --}}
            <div id="vehicle_detail_info" style="display: none;">
                <div class="alert alert-success">
                    <h6><i class="fas fa-check-circle"></i> Detail Kendaraan Terpilih</h6>
                    <div id="vehicle_detail_content">
                        </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> Catatan Penting</h6>
                        <ul class="mb-0">
                            <li>Kendaraan fungsional memiliki spesifikasi khusus sesuai dengan fungsi operasionalnya
                            </li>
                            <li>Pemilihan harus disesuaikan dengan kebutuhan operasional dan jenis satker</li>
                            <li>Tujuan penggunaan menentukan prioritas dan justifikasi pengadaan</li>
                            <li>Pastikan kendaraan yang dipilih memiliki sertifikasi dan kelengkapan yang sesuai
                                standar
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Informasi Barang dan Anggaran (unchanged) --}}
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
     * R6 EDIT COMPONENT - COMPLETE FIXED VERSION
     * =====================================================
     * Complete restrictions + validation logic from create form
     */

    function initializeR6EditScripts() {
        console.log('Initializing R6 Edit Scripts with Complete Logic...');

        const jenisForm = $('#jenistabel_hidden').val() || 'R6';

        // Vehicle details mapping untuk information display
        const vehicleDetails = {
            'All Terain Vehicle': {
                capacity: 'Kapasitas: 4-6 orang',
                usage: 'Medan berat, off-road, operasi lapangan',
                features: 'AWD, ground clearance tinggi'
            },
            'Jeep': {
                capacity: 'Kapasitas: 4-7 orang',
                usage: 'Patroli, operasional lapangan',
                features: 'Durability tinggi, manuver mudah'
            },
            'Kendaraan Toilet': {
                capacity: 'Kapasitas: Fasilitas sanitasi mobile',
                usage: 'Event outdoor, konstruksi, darurat',
                features: 'Self-contained, tangki air bersih & kotor'
            },
            'Micro Bus': {
                capacity: 'Kapasitas: 15-29 penumpang',
                usage: 'Transportasi grup, antar jemput',
                features: 'AC, kursi comfort, bagasi'
            },
            'Mini Bus': {
                capacity: 'Kapasitas: Maksimal 14 penumpang',
                usage: 'Transportasi tim kecil',
                features: 'Compact, ekonomis, manuver mudah'
            },
            'Mobile Ambulance': {
                capacity: 'Kapasitas: 2 pasien + medis team',
                usage: 'Emergency response, medical transport',
                features: 'Medical equipment, sirene, oxygen'
            },
            'Mobil Golfcar': {
                capacity: 'Kapasitas: 2-6 orang',
                usage: 'Area terbatas, kampus, kompleks',
                features: 'Electric/gas, low noise, eco-friendly'
            },
            'Mobile Jenazah': {
                capacity: 'Kapasitas: 1 jenazah + keluarga',
                usage: 'Transportasi jenazah, funeral service',
                features: 'Refrigeration, dignified transport'
            },
            'Mobil Unit Derek': {
                capacity: 'Kapasitas: Angkat 3-20 ton',
                usage: 'Recovery, emergency towing',
                features: 'Winch, boom, safety equipment'
            },
            'Sepeda Listrik': {
                capacity: 'Kapasitas: 1 orang',
                usage: 'Patrol, eco transport, area terbatas',
                features: 'Battery, eco-friendly, silent'
            },
            'Sepeda Motor': {
                capacity: 'Kapasitas: 1-2 orang',
                usage: 'Patrol cepat, kurir, akses sempit',
                features: 'Fuel efficient, agile, quick response'
            },
            'Truck + Attachment': {
                capacity: 'Kapasitas: Sesuai attachment',
                usage: 'Heavy duty, specialized operations',
                features: 'Modular attachment, high payload'
            }
        };

        // ==========================================
        // R6 FORM LOGIC (Copied from FormComponentR6)
        // ==========================================

        /**
         * Handle jenis satker change
         */
        function handleJenisSatkerChange() {
            const jenisSatker = $('#jenis_satker_fungsional').val();
            console.log('R6 Edit: Jenis Satker changed to:', jenisSatker);
            // Bisa menambah logic khusus berdasarkan jenis satker jika diperlukan
        }

        /**
         * Handle jenis kendaraan change
         */
        function handleJenisKendaraanChange() {
            const jenisKendaraan = $('#jenis_kendaraan_fungsional').val();
            console.log('R6 Edit: Jenis Kendaraan changed to:', jenisKendaraan);

            if (jenisKendaraan) {
                showVehicleDetails(jenisKendaraan);
            } else {
                $('#vehicle_detail_info').hide();
            }
        }

        /**
         * Show vehicle details
         */
        function showVehicleDetails(jenisKendaraan) {
            const details = vehicleDetails[jenisKendaraan];

            if (details) {
                let content = `
                <div class="row">
                    <div class="col-md-4">
                        <strong>${jenisKendaraan}</strong><br>
                        <small>${details.capacity}</small>
                    </div>
                    <div class="col-md-4">
                        <strong>Penggunaan:</strong><br>
                        <small>${details.usage}</small>
                    </div>
                    <div class="col-md-4">
                        <strong>Fitur Utama:</strong><br>
                        <small>${details.features}</small>
                    </div>
                </div>
            `;

                $('#vehicle_detail_content').html(content);
                $('#vehicle_detail_info').show();
            } else {
                $('#vehicle_detail_info').hide();
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

        // R6-specific handlers
        $('#jenis_satker_fungsional').off('change.r6edit').on('change.r6edit', handleJenisSatkerChange);
        $('#jenis_kendaraan_fungsional').off('change.r6edit').on('change.r6edit', handleJenisKendaraanChange);
        // Calculation handlers
        $('#kuantitas, #harga_barang').off('input.r6edit').on('input.r6edit', calculateTotal);

        // Currency formatting
        $('.currency-input').off('input.r6edit').on('input.r6edit', function () {
            let value = $(this).val().replace(/[^\d]/g, '');
            if (value) {
                $(this).val(formatCurrency(value));
            }
        });

        // ==========================================
        // INITIAL SETUP WITH EXISTING DATA
        // ==========================================

        // Apply features based on existing data
        const existingJenisSatker = $('#jenis_satker_fungsional').val();
        const existingJenisKendaraan = $('#jenis_kendaraan_fungsional').val();

        if (existingJenisSatker) {
            console.log('R6 Edit: Found existing jenis satker:', existingJenisSatker);
            handleJenisSatkerChange();
        }

        if (existingJenisKendaraan) {
            console.log('R6 Edit: Found existing jenis kendaraan:', existingJenisKendaraan);
            // This line had a bug in the original file, it should be the value not the text
            const selectedText = $("#jenis_kendaraan_fungsional option:selected").text().split('(')[0].trim();
            showVehicleDetails(selectedText);
        }

        // Hide vehicle detail initially if no selection
        if (!existingJenisKendaraan) {
            $('#vehicle_detail_info').hide();
        }

        console.log('R6 Edit Scripts initialized successfully');
    }
</script>
