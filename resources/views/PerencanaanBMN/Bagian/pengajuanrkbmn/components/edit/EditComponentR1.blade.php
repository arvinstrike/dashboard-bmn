@php
    $sbskEngine = app(\App\Services\SBSKRuleService::class);
    $klasifikasiBangunan = $sbskEngine->getKlasifikasiBangunanR1();
    $klasifikasiPejabat = $sbskEngine->getKlasifikasiPejabatR1();
    $tujuanOptions = $sbskEngine->getTujuanRencanaOptions();
    $atrOptions = $sbskEngine->getAtrNonAtrOptions();
    $golonganOptions = $sbskEngine->getGolongan('R1');  // FIXED: Add golongan options
    $roomFields = [
        'luas_ruang_kerja' => 'Ruang Kerja',
        'luas_ruang_tamu' => 'Ruang Tamu',
        'luas_ruang_rapat' => 'Ruang Rapat',
        'luas_ruang_tunggu' => 'Ruang Tunggu',
        'luas_ruang_istirahat' => 'Ruang Istirahat',
        'luas_ruang_sekretaris' => 'Ruang Sekretaris',
        'luas_ruang_simpan' => 'Ruang Simpan',
        'luas_ruang_toilet' => 'Ruang Toilet',
        'luas_ruang_rapat_utama' => 'Ruang Rapat Utama'
    ];
@endphp

<div class="form-component-r1-edit" data-jenis="R1">
    {{-- Card Spesifikasi Gedung Perkantoran --}}
    <div class="card mb-4">
        <div class="card-header"><h3 class="card-title">Spesifikasi Gedung Perkantoran</h3></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="klasifikasi_bangunan">Klasifikasi Bangunan <span class="text-danger">*</span></label>
                    <select name="klasifikasi_bangunan" id="klasifikasi_bangunan" class="form-control" required>
                        <option value="">-- Pilih Klasifikasi Bangunan --</option>
                        @foreach($klasifikasiBangunan as $value => $text)
                            <option value="{{ $value }}"
                                    @if(optional($detailData)->klasifikasi_bangunan == $value) selected @endif>{{ $text }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label for="klasifikasi_pejabat">Klasifikasi Pejabat <span class="text-danger">*</span></label>
                    <select name="klasifikasi_pejabat" id="klasifikasi_pejabat" class="form-control" required>
                        <option value="">-- Pilih Klasifikasi Pejabat --</option>
                        @foreach($klasifikasiPejabat as $value => $text)
                            <option value="{{ $value }}"
                                    @if(optional($detailData)->klasifikasi_pejabat == $value) selected @endif>{{ $text }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="tujuan_rencana">Tujuan <span class="text-danger">*</span></label>
                    <select name="tujuan_rencana" id="tujuan_rencana" class="form-control" required>
                        <option value="">-- Pilih Tujuan --</option>
                        @foreach($tujuanOptions as $value => $text)
                            <option value="{{ $value }}"
                                    @if(optional($data)->tujuan_rencana == $value) selected @endif>{{ $text }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 form-group" id="atr_container"
                     style="{{ optional($data)->tujuan_rencana != 'Perluasan' ? 'display: none;' : '' }}">
                    <label for="atr_nonatr">ATR/Non ATR <span class="text-danger">*</span></label>
                    <select name="atr_nonatr" id="atr_nonatr"
                            class="form-control" {{ optional($data)->tujuan_rencana == 'Perluasan' ? 'required' : '' }}>
                        <option value="">-- Pilih ATR/Non ATR --</option>
                        @foreach($atrOptions as $value => $text)
                            <option value="{{ $value }}"
                                    @if(optional($data)->atr_nonatr == $value) selected @endif>{{ $text }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label for="lokasi">Lokasi <span class="text-danger">*</span></label>
                    <textarea name="lokasi" id="lokasi" class="form-control" rows="2"
                              required>{{ optional($detailData)->lokasi }}</textarea>
                </div>
            </div>

            <h5 class="mt-4 mb-3">Detail Luas Ruangan (m²)</h5>
            <div id="room_limits_container">
                @foreach (array_chunk($roomFields, 4, true) as $chunk)
                    <div class="row">
                        @foreach ($chunk as $field => $label)
                            <div class="col-md-3 form-group">
                                <label for="{{ $field }}">{{ $label }} @if($field === 'luas_ruang_kerja')
                                        <span class="text-danger">*</span>
                                    @endif</label>
                                <div class="input-group">
                                    <input type="number" name="{{ $field }}" id="{{ $field }}"
                                           class="form-control room-input" min="0" step="0.01"
                                           value="{{ optional($detailData)->$field ?? 0 }}"
                                           @if($field === 'luas_ruang_kerja') required @endif>
                                    <div class="input-group-append"><span class="input-group-text">m²</span></div>
                                </div>
                                <small class="form-text text-muted" id="help_{{ $field }}"></small>
                                <small class="form-text text-danger" id="error_{{ $field }}"
                                       style="display: none;"></small>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Card Informasi Barang dan Anggaran --}}
    <div class="card mb-4">
        <div class="card-header"><h3 class="card-title">Informasi Barang dan Anggaran</h3></div>
        <div class="card-body">
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

<script>
    /**
     * =====================================================
     * R1 EDIT COMPONENT - FIXED WITH RESTRICTIONS
     * =====================================================
     * Fixed: Added complete restriction logic from create form
     */

    function initializeR1EditScripts() {
        console.log('Initializing R1 Edit Scripts with Restrictions...');

        const jenisForm = $('#jenistabel_hidden').val() || 'R1';

        // ==========================================
        // RESTRICTION LOGIC (Copied from FormComponentR1)
        // ==========================================

        /**
         * Handle klasifikasi bangunan change
         */
        function handleKlasifikasiBangunanChange() {
            const value = $('#klasifikasi_bangunan').val();
            console.log('R1 Edit: Klasifikasi bangunan changed to:', value);

            if (value) {
                $('#klasifikasi_pejabat').prop('disabled', false);
            } else {
                $('#klasifikasi_pejabat').prop('disabled', true).val('');
                disableRoomInputs();
            }
        }

        /**
         * Handle klasifikasi pejabat change
         */
        function handleKlasifikasiPejabatChange() {
            const klasifikasi = $('#klasifikasi_pejabat').val();
            console.log('R1 Edit: Klasifikasi pejabat changed to:', klasifikasi);

            if (klasifikasi) {
                setupRoomLimits(klasifikasi);
                enableRoomInputs();
            } else {
                disableRoomInputs();
            }
        }

        /**
         * Handle tujuan rencana change
         */
        function handleTujuanRencanaChange() {
            const value = $('#tujuan_rencana').val();
            console.log('R1 Edit: Tujuan rencana changed to:', value);

            if (value === 'Tambah Unit') {
                $('#atr_container').show();
                $('#atr_nonatr').attr('required', true);
            } else {
                $('#atr_container').hide();
                $('#atr_nonatr').removeAttr('required').val('');
            }
        }

        /**
         * Setup room limits for selected klasifikasi
         */
        function setupRoomLimits(klasifikasi) {
            console.log('R1 Edit: Setting up room limits for:', klasifikasi);

            // Call backend to get room limits
            $.ajax({
                url: '{{ route("pengajuanrkbmnbagian.get-form-component") }}',
                method: 'GET',
                data: {
                    action: 'getRoomLimits',
                    jenis: jenisForm,
                    klasifikasi: klasifikasi
                },
                success: function (response) {
                    if (response.success && response.roomLimits) {
                        applyRoomLimits(response.roomLimits);
                        console.log('R1 Edit: Room limits applied successfully');
                    }
                },
                error: function (xhr) {
                    console.error('R1 Edit: Error getting room limits:', xhr);
                }
            });
        }

        /**
         * Apply room limits to inputs
         */
        function applyRoomLimits(limits) {
            Object.keys(limits).forEach(field => {
                const input = $(`#luas_${field}`);
                const maxValue = limits[field];

                if (input.length) {
                    input.attr('max', maxValue);
                    input.attr('placeholder', `Maksimal: ${maxValue} m²`);
                    $(`#help_luas_${field}`).text(`Maksimal: ${maxValue} m²`);

                    // Validate current value against new limit
                    validateRoomInput({target: input[0]});
                }
            });
        }

        /**
         * Enable room inputs
         */
        function enableRoomInputs() {
            $('.room-input').prop('disabled', false);
            console.log('R1 Edit: Room inputs enabled');
        }

        /**
         * Disable room inputs
         */
        function disableRoomInputs() {
            $('.room-input').prop('disabled', true).val('');
            $('.room-input').removeAttr('max placeholder');
            $('[id^="help_luas_"]').text('');
            $('[id^="error_luas_"]').hide();
            console.log('R1 Edit: Room inputs disabled');
        }

        /**
         * Validate room input
         */
        function validateRoomInput(e) {
            const input = $(e.target);
            const fieldName = input.attr('name');
            const value = parseFloat(input.val());
            const maxValue = parseFloat(input.attr('max'));
            const errorElement = $(`#error_${fieldName}`);

            // Clear previous errors
            input.removeClass('is-invalid');
            errorElement.hide();

            // Validate negative values
            if (value < 0) {
                input.val(0);
                return;
            }

            // Validate max value
            if (maxValue && value > maxValue) {
                input.addClass('is-invalid');
                errorElement.text(`Luas tidak boleh melebihi ${maxValue} m²`).show();
                input.val(maxValue);
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

        // Restriction event handlers
        $('#klasifikasi_bangunan').off('change.r1edit').on('change.r1edit', handleKlasifikasiBangunanChange);
        $('#klasifikasi_pejabat').off('change.r1edit').on('change.r1edit', handleKlasifikasiPejabatChange);
        $('#tujuan_rencana').off('change.r1edit').on('change.r1edit', handleTujuanRencanaChange);

        // Room validation
        $('.room-input').off('input.r1edit').on('input.r1edit', validateRoomInput);

        // Calculation handlers
        $('#kuantitas, #harga_barang').off('input.r1edit').on('input.r1edit', calculateTotal);

        // Currency formatting
        $('.currency-input').off('input.r1edit').on('input.r1edit', function () {
            let value = $(this).val().replace(/[^\d]/g, '');
            if (value) {
                $(this).val(formatCurrency(value));
            }
        });

        // ==========================================
        // INITIAL SETUP WITH EXISTING DATA
        // ==========================================

        // Setup restrictions based on existing data
        const existingKlasifikasiBangunan = $('#klasifikasi_bangunan').val();
        const existingKlasifikasiPejabat = $('#klasifikasi_pejabat').val();

        if (existingKlasifikasiBangunan) {
            $('#klasifikasi_pejabat').prop('disabled', false);

            if (existingKlasifikasiPejabat) {
                setupRoomLimits(existingKlasifikasiPejabat);
                enableRoomInputs();
            }
        }

        // Setup ATR visibility based on existing tujuan
        const existingTujuan = $('#tujuan_rencana').val();
        if (existingTujuan === 'Perluasan') {
            $('#atr_container').show();
            $('#atr_nonatr').attr('required', true);
        }

        console.log('R1 Edit Scripts initialized with existing data');
    }
</script>
