@php
    $sbskEngine = app(\App\Services\SBSKRuleService::class);
    $peruntukanPejabat = $sbskEngine->getPeruntukanPejabatR3();
    $peruntukanMapping = $sbskEngine->getPeruntukanToTipeMapping();
    $lokasiRumah = $sbskEngine->getLokasiRumahR3();
    $golonganOptions = $sbskEngine->getGolongan('R3');
    $tujuanOptions = $sbskEngine->getTujuanRencanaOptions();

    $ruanganList = [
        'ruang_kerja' => 'Ruang Kerja', 'ruang_duduk' => 'Ruang Duduk',
        'ruang_fungsional' => 'Ruang Fungsional', 'ruang_makan' => 'Ruang Makan',
        'ruang_tidur' => 'Ruang Tidur', 'ruang_wc' => 'Kamar Mandi/WC',
        'dapur' => 'Dapur', 'gudang' => 'Gudang', 'garasi' => 'Garasi',
        'ruang_tidur_pramuwisma' => 'Ruang Tidur Pramuwisma', 'ruang_cuci' => 'Ruang Cuci',
        'kamar_mandi_pramuwisma' => 'Kamar Mandi Pramuwisma'
    ];
@endphp

<div class="form-component-r3-edit" data-jenis="R3">
    {{-- Spesifikasi Rumah Negara --}}
    <div class="card mb-4">
        <div class="card-header"><h3 class="card-title">Spesifikasi Rumah Negara</h3></div>
        <div class="card-body">
            {{-- Peruntukan, Tipe, Lokasi --}}
            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="peruntukan_pejabat">Peruntukan Pejabat <span class="text-danger">*</span></label>
                    <select name="peruntukan_pejabat" id="peruntukan_pejabat" class="form-control" required>
                        <option value="">-- Pilih Peruntukan --</option>
                        @foreach($peruntukanPejabat as $key => $value)
                            <option value="{{ $key }}"
                                    @if(optional($detailData)->peruntukan_pejabat == $key) selected @endif>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label for="jenis_rumah">Tipe Rumah</label>
                    <input type="text" name="jenis_rumah" id="jenis_rumah" class="form-control"
                           value="{{ optional($detailData)->jenis_rumah }}" readonly placeholder="Otomatis">
                </div>
                <div class="col-md-4 form-group">
                    <label for="lokasi">Lokasi Rumah <span class="text-danger">*</span></label>
                    <select name="lokasi" id="lokasi" class="form-control" required>
                        <option value="">-- Pilih Lokasi --</option>
                        @foreach($lokasiRumah as $key => $value)
                            <option value="{{ $key }}"
                                    @if(optional($detailData)->lokasi == $key) selected @endif>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Tujuan dan ATR (Tabel Utama) --}}
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="tujuan_rencana">Tujuan <span class="text-danger">*</span></label>
                    <select name="tujuan_rencana" id="tujuan_rencana" class="form-control" required>
                        <option value="">-- Pilih Tujuan --</option>
                        @foreach($tujuanOptions as $value => $text)
                            <option value="{{ $value }}" @if(optional($data)->tujuan_rencana == $value) selected @endif>
                                {{ $text }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label for="atr_nonatr">ATR/Non ATR <span class="text-danger">*</span></label>
                    <select name="atr_nonatr" id="atr_nonatr" class="form-control" required>
                        <option value="">-- Pilih ATR/Non ATR --</option>
                        <option value="ATR" @if(optional($data)->atr_nonatr == 'ATR') selected @endif>ATR</option>
                        <option value="Non ATR" @if(optional($data)->atr_nonatr == 'Non ATR') selected @endif>Non ATR
                        </option>
                    </select>
                </div>
            </div>

            {{-- Info Standar Luas --}}
            <div id="standar_luas_info" class="alert alert-info" style="display: none;">
                <h6><i class="fas fa-info-circle"></i> Standar Luas (Berdasarkan PMK 172/2020)</h6>
                <div class="row">
                    <div class="col-md-4"><strong>Luas Bangunan Maks:</strong> <span
                            id="luas_bangunan_maks_display">-</span> m²
                    </div>
                    <div class="col-md-4"><strong>Luas Tanah Maks:</strong> <span id="luas_tanah_maks_display">-</span>
                        m²
                    </div>
                    <div class="col-md-4"><strong>Luas Tanah + Toleransi (<span
                                id="toleransi_display">-</span>%):</strong> <span
                            id="luas_tanah_toleransi_display">-</span> m²
                    </div>
                </div>
            </div>

            {{-- Input Luas Tanah & Bangunan --}}
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="luas_bangunan">Input Luas Bangunan <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" name="luas_bangunan" id="luas_bangunan" class="form-control"
                               min="0" step="0.01" value="{{ optional($detailData)->luas_bangunan ?? 0 }}" required
                               placeholder="Masukkan luas bangunan">
                        <div class="input-group-append"><span class="input-group-text">m²</span></div>
                    </div>
                    <div id="feedback_luas_bangunan" class="feedback-text"></div>
                </div>
                <div class="col-md-6 form-group">
                    <label for="luas_tanah">Input Luas Tanah <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" name="luas_tanah" id="luas_tanah" class="form-control"
                               min="0" step="0.01" value="{{ optional($detailData)->luas_tanah ?? 0 }}" required
                               placeholder="Masukkan luas tanah">
                        <div class="input-group-append"><span class="input-group-text">m²</span></div>
                    </div>
                    <div id="feedback_luas_tanah" class="feedback-text"></div>
                </div>
            </div>

            {{-- Kebutuhan Ruang --}}
            <h5 class="mt-4 mb-3">Detail Kebutuhan Ruangan (Sesuai Standar PMK)</h5>
            <div id="kebutuhan_ruang_section">
                <div class="row">
                    @foreach($ruanganList as $field => $label)
                        <div class="col-md-3 form-group">
                            <label for="jumlah_{{ $field }}">{{ $label }}</label>
                            <input type="number" name="jumlah_{{ $field }}" id="jumlah_{{ $field }}"
                                   class="form-control room-input"
                                   min="0" value="{{ optional($detailData)->{'jumlah_'.$field} ?? 0 }}">
                            <small class="form-text text-muted" id="help_jumlah_{{ $field }}"></small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Informasi Barang dan Anggaran --}}
    <div class="card mb-4">
        <div class="card-header"><h3 class="card-title">Informasi Barang dan Anggaran</h3></div>
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
            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="kuantitas">Kuantitas <span class="text-danger">*</span></label>
                    <input type="number" name="kuantitas" id="kuantitas" class="form-control"
                           value="{{ $data->kuantitas }}" min="1" required>
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
</style>

<script>
    /**
     * =====================================================
     * R3 EDIT COMPONENT - COMPLETE FIXED VERSION
     * =====================================================
     * Complete restrictions + validation logic from create form
     */

    function initializeR3EditScripts() {
        console.log('Initializing R3 Edit Scripts with Complete Restrictions...');

        const jenisForm = $('#jenistabel_hidden').val() || 'R3';
        const peruntukanMapping = @json($peruntukanMapping);
        let standarLuas = {};

        // ==========================================
        // R3 RESTRICTION LOGIC (Copied from FormComponentR3)
        // ==========================================

        /**
         * Update form state based on peruntukan and lokasi
         */
        function updateFormState() {
            const peruntukan = $('#peruntukan_pejabat').val();
            const lokasi = $('#lokasi').val();  // FIXED: use correct ID
            const tipe = peruntukan ? (peruntukanMapping[peruntukan] || '') : '';

            $('#jenis_rumah').val(tipe);  // FIXED: use correct ID
            console.log('R3 Edit: Form state updated - Peruntukan:', peruntukan, 'Lokasi:', lokasi, 'Tipe:', tipe);

            if (tipe && lokasi) {
                fetchStandards(tipe, lokasi);
            } else {
                $('#standar_luas_info, #kebutuhan_ruang_section').hide();
                $('.room-input').val(0).removeAttr('max');
                $('.feedback-text').hide();
            }
        }

        /**
         * Fetch standards from server
         */
        function fetchStandards(tipe, lokasi) {
            console.log('R3 Edit: Fetching standards for tipe:', tipe, 'lokasi:', lokasi);

            // Gabungkan beberapa request AJAX menjadi satu promise
            $.when(
                $.get('{{ route("pengajuanrkbmnbagian.get-form-component") }}', {
                    action: 'getHouseLandLimits',
                    tipe,
                    lokasi
                }),
                $.get('{{ route("pengajuanrkbmnbagian.get-form-component") }}', {action: 'getBuildingLimits', tipe}),
                $.get('{{ route("pengajuanrkbmnbagian.get-form-component") }}', {action: 'getRoomRequirements', tipe})
            ).done(function (landRes, buildingRes, roomRes) {
                // Land standards
                if (landRes[0].success && landRes[0].landLimits) {
                    standarLuas.tanah = landRes[0].landLimits;
                    $('#luas_tanah_maks_display').text(standarLuas.tanah.luas_maksimal);
                    $('#toleransi_display').text(standarLuas.tanah.toleransi_persen);
                    $('#luas_tanah_toleransi_display').text(standarLuas.tanah.luas_dengan_toleransi);
                    validateInput($('#luas_tanah'), standarLuas.tanah.luas_dengan_toleransi, standarLuas.tanah.luas_maksimal, 'tanah');
                }

                // Building standards
                if (buildingRes[0].success && typeof buildingRes[0].buildingLimit !== 'undefined') {
                    standarLuas.bangunan = buildingRes[0].buildingLimit;
                    $('#luas_bangunan_maks_display').text(standarLuas.bangunan);
                    validateInput($('#luas_bangunan'), standarLuas.bangunan, standarLuas.bangunan, 'bangunan');
                }

                // Room requirements
                if (roomRes[0].success && roomRes[0].kebutuhan) {
                    displayRoomRequirements(roomRes[0].kebutuhan);
                }

                $('#standar_luas_info, #kebutuhan_ruang_section').show();
                console.log('R3 Edit: Standards applied successfully');

            }).fail(function (xhr) {
                console.error('R3 Edit: Failed to fetch standards:', xhr);
                alert('Gagal mengambil data standar dari server.');
            });
        }

        /**
         * Display room requirements
         */
        function displayRoomRequirements(kebutuhan) {
            $('.room-input').each(function () {
                const fieldId = $(this).attr('id');  // e.g., "jumlah_ruang_tamu"
                const fieldKey = fieldId.replace('jumlah_', '');  // e.g., "ruang_tamu"
                const maxValue = kebutuhan[fieldKey] ?? 0;
                $(this).attr('max', maxValue).val(maxValue); // Auto-fill dengan standar atau keep existing
                $(`#help_${fieldId}`).text(`Standar: ${maxValue} unit`);
            });
        }

        /**
         * Validate input against standards
         */
        function validateInput(inputEl, maxVal, standardVal, type) {
            const value = parseFloat(inputEl.val()) || 0;
            const feedbackEl = $(`#feedback_luas_${type}`);

            if (value === 0) {
                feedbackEl.text('').hide();
                return;
            }

            if (value > maxVal) {
                feedbackEl.text(`Melebihi batas: ${maxVal} m²`).removeClass('text-success text-warning').addClass('text-danger').show();
            } else if (type === 'tanah' && value > standardVal) {
                feedbackEl.text('Dalam batas toleransi').removeClass('text-success text-danger').addClass('text-warning').show();
            } else {
                feedbackEl.text('Sesuai standar').removeClass('text-danger text-warning').addClass('text-success').show();
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

        // R3-specific restriction handlers
        $('#peruntukan_pejabat, #lokasi').off('change.r3edit').on('change.r3edit', updateFormState);  // FIXED: use correct ID

        // Luas validation handlers
        $('#luas_bangunan').off('input.r3edit').on('input.r3edit', function () {
            if (standarLuas.bangunan !== undefined) {
                validateInput($(this), standarLuas.bangunan, standarLuas.bangunan, 'bangunan');
            }
        });

        $('#luas_tanah').off('input.r3edit').on('input.r3edit', function () {
            if (standarLuas.tanah) {
                validateInput($(this), standarLuas.tanah.luas_dengan_toleransi, standarLuas.tanah.luas_maksimal, 'tanah');
            }
        });

        // Room input validation
        $('.room-input').off('input.r3edit').on('input.r3edit', function () {
            const max = parseInt($(this).attr('max')) || 0;
            const currentVal = parseInt($(this).val());
            if (currentVal > max) $(this).val(max);
            if (currentVal < 0) $(this).val(0);
        });

        // Calculation handlers
        $('#kuantitas, #harga_barang').off('input.r3edit').on('input.r3edit', calculateTotal);

        // Currency formatting
        $('.currency-input').off('input.r3edit').on('input.r3edit', function () {
            let value = $(this).val().replace(/[^\d]/g, '');
            if (value) {
                $(this).val(formatCurrency(value));
            }
        });

        // ==========================================
        // INITIAL SETUP WITH EXISTING DATA
        // ==========================================

        // Apply restrictions based on existing data
        const existingPeruntukan = $('#peruntukan_pejabat').val();
        const existingLokasi = $('#lokasi').val();  // FIXED: use correct ID

        if (existingPeruntukan && existingLokasi) {
            console.log('R3 Edit: Applying initial restrictions with existing data');
            updateFormState();
        } else {
            console.log('R3 Edit: No existing peruntukan/lokasi data found');
        }

        console.log('R3 Edit Scripts initialized successfully');
    }
</script>
