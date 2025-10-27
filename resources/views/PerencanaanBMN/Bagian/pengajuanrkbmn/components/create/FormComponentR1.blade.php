{{-- resources/views/PerencanaanBMN/Bagian/pengajuanrkbmn/components/FormComponentR1.blade.php --}}
{{-- Form Component untuk R1 - Tanah dan/atau Bangunan Perkantoran --}}

@php
    $sbskEngine = app(\App\Services\SBSKRuleService::class);
    $formConfig = $sbskEngine->getFormConfig('R1');
    $klasifikasiBangunan = $sbskEngine->getKlasifikasiBangunanR1();
    $klasifikasiPejabat = $sbskEngine->getKlasifikasiPejabatR1();
    $golonganOptions = $sbskEngine->getGolongan('R1');
    $tujuanOptions = $sbskEngine->getTujuanRencanaOptions();
    $atrOptions = $sbskEngine->getAtrNonAtrOptions();
@endphp

<div class="form-component-r1" data-jenis="R1">
    {{-- Spesifikasi Gedung Perkantoran --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Spesifikasi Gedung Perkantoran</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="klasifikasi_bangunan">Klasifikasi Bangunan <span
                                class="text-danger">*</span></label>
                        <select name="klasifikasi_bangunan" id="klasifikasi_bangunan" class="form-control" required>
                            <option value="">-- Pilih Klasifikasi Bangunan --</option>
                            @foreach($klasifikasiBangunan as $value => $text)
                                <option value="{{ $value }}">{{ $text }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="klasifikasi_pejabat">Klasifikasi Pejabat <span class="text-danger">*</span></label>
                        <select name="klasifikasi_pejabat" id="klasifikasi_pejabat" class="form-control" required
                                disabled>
                            <option value="">-- Pilih Klasifikasi Pejabat --</option>
                            @foreach($klasifikasiPejabat as $value => $text)
                                <option value="{{ $value }}">{{ $text }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tujuan_rencana">Tujuan <span class="text-danger">*</span></label>
                        <select name="tujuan_rencana" id="tujuan_rencana" class="form-control" required>
                            <option value="">-- Pilih Tujuan --</option>
                            @foreach($tujuanOptions as $value => $text)
                                <option value="{{ $value }}">{{ $text }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4" id="atr_container" style="display: none;">
                    <div class="form-group">
                        <label for="atr_nonatr">ATR/Non ATR <span class="text-danger">*</span></label>
                        <select name="atr_nonatr" id="atr_nonatr" class="form-control">
                            <option value="">-- Pilih ATR/Non ATR --</option>
                            @foreach($atrOptions as $value => $text)
                                <option value="{{ $value }}">{{ $text }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="lokasi">Lokasi <span class="text-danger">*</span></label>
                        <textarea name="lokasi" id="lokasi" class="form-control" rows="2"
                                  placeholder="Masukkan lokasi detail..." required></textarea>
                    </div>
                </div>
            </div>

            {{-- Detail Ruangan --}}
            <h5 class="mt-4 mb-3">Detail Luas Ruangan (m²)</h5>
            <div id="room_limits_container">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="luas_ruang_kerja">Ruang Kerja <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="luas_ruang_kerja" id="luas_ruang_kerja"
                                       class="form-control room-input" min="0" step="0.01" required disabled>
                                <div class="input-group-append">
                                    <span class="input-group-text">m²</span>
                                </div>
                            </div>
                            <small class="form-text text-muted" id="help_luas_ruang_kerja"></small>
                            <small class="form-text text-danger" id="error_luas_ruang_kerja"
                                   style="display: none;"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="luas_ruang_tamu">Ruang Tamu</label>
                            <div class="input-group">
                                <input type="number" name="luas_ruang_tamu" id="luas_ruang_tamu"
                                       class="form-control room-input" min="0" step="0.01" disabled>
                                <div class="input-group-append">
                                    <span class="input-group-text">m²</span>
                                </div>
                            </div>
                            <small class="form-text text-muted" id="help_luas_ruang_tamu"></small>
                            <small class="form-text text-danger" id="error_luas_ruang_tamu"
                                   style="display: none;"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="luas_ruang_rapat">Ruang Rapat</label>
                            <div class="input-group">
                                <input type="number" name="luas_ruang_rapat" id="luas_ruang_rapat"
                                       class="form-control room-input" min="0" step="0.01" disabled>
                                <div class="input-group-append">
                                    <span class="input-group-text">m²</span>
                                </div>
                            </div>
                            <small class="form-text text-muted" id="help_luas_ruang_rapat"></small>
                            <small class="form-text text-danger" id="error_luas_ruang_rapat"
                                   style="display: none;"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="luas_ruang_tunggu">Ruang Tunggu</label>
                            <div class="input-group">
                                <input type="number" name="luas_ruang_tunggu" id="luas_ruang_tunggu"
                                       class="form-control room-input" min="0" step="0.01" disabled>
                                <div class="input-group-append">
                                    <span class="input-group-text">m²</span>
                                </div>
                            </div>
                            <small class="form-text text-muted" id="help_luas_ruang_tunggu"></small>
                            <small class="form-text text-danger" id="error_luas_ruang_tunggu"
                                   style="display: none;"></small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="luas_ruang_istirahat">Ruang Istirahat</label>
                            <div class="input-group">
                                <input type="number" name="luas_ruang_istirahat" id="luas_ruang_istirahat"
                                       class="form-control room-input" min="0" step="0.01" disabled>
                                <div class="input-group-append">
                                    <span class="input-group-text">m²</span>
                                </div>
                            </div>
                            <small class="form-text text-muted" id="help_luas_ruang_istirahat"></small>
                            <small class="form-text text-danger" id="error_luas_ruang_istirahat"
                                   style="display: none;"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="luas_ruang_sekretaris">Ruang Sekretaris</label>
                            <div class="input-group">
                                <input type="number" name="luas_ruang_sekretaris" id="luas_ruang_sekretaris"
                                       class="form-control room-input" min="0" step="0.01" disabled>
                                <div class="input-group-append">
                                    <span class="input-group-text">m²</span>
                                </div>
                            </div>
                            <small class="form-text text-muted" id="help_luas_ruang_sekretaris"></small>
                            <small class="form-text text-danger" id="error_luas_ruang_sekretaris"
                                   style="display: none;"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="luas_ruang_simpan">Ruang Simpan</label>
                            <div class="input-group">
                                <input type="number" name="luas_ruang_simpan" id="luas_ruang_simpan"
                                       class="form-control room-input" min="0" step="0.01" disabled>
                                <div class="input-group-append">
                                    <span class="input-group-text">m²</span>
                                </div>
                            </div>
                            <small class="form-text text-muted" id="help_luas_ruang_simpan"></small>
                            <small class="form-text text-danger" id="error_luas_ruang_simpan"
                                   style="display: none;"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="luas_ruang_toilet">Ruang Toilet</label>
                            <div class="input-group">
                                <input type="number" name="luas_ruang_toilet" id="luas_ruang_toilet"
                                       class="form-control room-input" min="0" step="0.01" disabled>
                                <div class="input-group-append">
                                    <span class="input-group-text">m²</span>
                                </div>
                            </div>
                            <small class="form-text text-muted" id="help_luas_ruang_toilet"></small>
                            <small class="form-text text-danger" id="error_luas_ruang_toilet"
                                   style="display: none;"></small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="luas_ruang_rapat_utama">Ruang Rapat Utama</label>
                            <div class="input-group">
                                <input type="number" name="luas_ruang_rapat_utama" id="luas_ruang_rapat_utama"
                                       class="form-control room-input" min="0" step="0.01" disabled>
                                <div class="input-group-append">
                                    <span class="input-group-text">m²</span>
                                </div>
                            </div>
                            <small class="form-text text-muted" id="help_luas_ruang_rapat_utama"></small>
                            <small class="form-text text-danger" id="error_luas_ruang_rapat_utama"
                                   style="display: none;"></small>
                        </div>
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

            {{-- Anggaran dan Detail --}}
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

<script>
    /**
     * =====================================================
     * R1 COMPONENT - FIXED VERSION (NO DROPDOWN CONFLICT)
     * =====================================================
     * FIXED: Removed duplicate dropdown logic to prevent conflict
     * Focus: Only R1-specific features (room limits, ATR logic, validation)
     * Dropdown handling: Delegated to parent CreateFormRKBMN.blade.php
     */

    $(document).ready(function () {
        // Only initialize if this is the active R1 component
        if ($('.form-component-r1').length > 0) {
            initializeR1Component();
        }
    });

    /**
     * Initialize R1-specific functionality ONLY
     * NO dropdown cascade logic (handled by parent)
     */
    function initializeR1Component() {
        console.log('Initializing R1 Component (Fixed Version)...');

        // R1-specific event handlers ONLY
        setupR1EventHandlers();

        // Initialize form state
        initializeR1FormState();

        console.log('R1 Component initialized successfully');
    }

    /**
     * Setup R1-specific event handlers (NO dropdown events)
     */
    function setupR1EventHandlers() {
        // Klasifikasi bangunan → enable klasifikasi pejabat
        $('#klasifikasi_bangunan').off('change.r1').on('change.r1', handleKlasifikasiBangunanChange);

        // Klasifikasi pejabat → setup room limits & enable form
        $('#klasifikasi_pejabat').off('change.r1').on('change.r1', handleKlasifikasiPejabatChange);

        // Tujuan rencana → show/hide ATR dropdown
        $('#tujuan_rencana').off('change.r1').on('change.r1', handleTujuanRencanaChange);

        // Room input validation
        $('.room-input').off('input.r1').on('input.r1', validateRoomInput);

        // Calculate total (currency inputs)
        $('#kuantitas, #harga_barang').off('input.r1').on('input.r1', calculateTotal);

        console.log('R1 event handlers attached (no dropdown conflicts)');
    }

    /**
     * Initialize R1 form state
     */
    function initializeR1FormState() {
        // Disable room inputs initially
        $('.room-input').prop('disabled', true);

        // Hide ATR container initially
        $('#atr_container').hide();

        console.log('R1 form state initialized');
    }

    /**
     * Handle klasifikasi bangunan change
     */
    function handleKlasifikasiBangunanChange() {
        const value = $('#klasifikasi_bangunan').val();
        console.log('R1: Klasifikasi bangunan changed to:', value);

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
        console.log('R1: Klasifikasi pejabat changed to:', klasifikasi);

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
    function handleTujuanRencanaChange(){
        const value = $('#tujuan_rencana').val();
        console.log('R1: Tujuan rencana changed to:', value);

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
        console.log('R1: Setting up room limits for:', klasifikasi);

        // Call backend to get room limits
        $.ajax({
            url: '{{ route("pengajuanrkbmnbagian.get-form-component") }}',
            method: 'GET',
            data: {
                action: 'getRoomLimits',
                jenis: 'R1',
                klasifikasi: klasifikasi
            },
            success: function (response) {
                if (response.success && response.roomLimits) {
                    applyRoomLimits(response.roomLimits);
                    console.log('R1: Room limits applied successfully');
                }
            },
            error: function (xhr) {
                console.error('R1: Error getting room limits:', xhr);
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
            }
        });
    }

    /**
     * Enable room inputs
     */
    function enableRoomInputs() {
        $('.room-input').prop('disabled', false);
        console.log('R1: Room inputs enabled');
    }

    /**
     * Disable room inputs
     */
    function disableRoomInputs() {
        $('.room-input').prop('disabled', true).val('');
        $('.room-input').removeAttr('max placeholder');
        $('[id^="help_luas_"]').text('');
        $('[id^="error_luas_"]').hide();
        console.log('R1: Room inputs disabled');
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

    /**
     * Global function untuk re-initialization (called by parent)
     */
    window.initializeR1Scripts = function () {
        console.log('Re-initializing R1 scripts...');
        initializeR1Component();
    };

    console.log('R1 Component script loaded (fixed version - no dropdown conflicts)');
</script>
