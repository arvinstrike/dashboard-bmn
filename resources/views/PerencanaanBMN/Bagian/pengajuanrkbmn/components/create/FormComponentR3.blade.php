{{-- resources/views/PerencanaanBMN/Bagian/pengajuanrkbmn/components/FormComponentR3.blade.php --}}
{{-- Form Component untuk R3 - Rumah Negara (FINAL REFACTORED) --}}

@php
    $sbskEngine = app(\App\Services\SBSKRuleService::class);
    $peruntukanPejabat = $sbskEngine->getPeruntukanPejabatR3();
    $peruntukanMapping = $sbskEngine->getPeruntukanToTipeMapping();
    $lokasiRumah = $sbskEngine->getLokasiRumahR3();
    $golonganOptions = $sbskEngine->getGolongan('R3');
    $tujuanOptions = $sbskEngine->getTujuanRencanaOptions();
    $atrOptions = $sbskEngine->getAtrNonAtrOptions();

    $ruanganList = [
        'ruang_tamu' => 'Ruang Tamu', 'ruang_kerja' => 'Ruang Kerja', 'ruang_duduk' => 'Ruang Duduk',
        'ruang_fungsional' => 'Ruang Fungsional', 'ruang_makan' => 'Ruang Makan', 'ruang_tidur' => 'Ruang Tidur',
        'kamar_mandi_wc' => 'Kamar Mandi/WC', 'dapur' => 'Dapur', 'gudang' => 'Gudang', 'garasi' => 'Garasi',
        'ruang_tidur_pramuwisma' => 'Ruang Tidur Pramuwisma', 'ruang_cuci' => 'Ruang Cuci',
        'kamar_mandi_pramuwisma' => 'Kamar Mandi Pramuwisma'
    ];
@endphp

<div class="form-component-r3" data-jenis="R3">
    {{-- Spesifikasi Rumah Negara --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Spesifikasi Rumah Negara</h3>
        </div>
        <div class="card-body">
            {{-- Peruntukan, Tipe, Lokasi --}}
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="peruntukan_pejabat">Peruntukan Pejabat <span class="text-danger">*</span></label>
                        <select name="peruntukan_pejabat" id="peruntukan_pejabat" class="form-control" required>
                            <option value="">-- Pilih Peruntukan --</option>
                            @foreach($peruntukanPejabat as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tipe_rumah">Tipe Rumah</label>
                        <input type="text" name="tipe_rumah" id="tipe_rumah" class="form-control" readonly
                               placeholder="Otomatis">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="lokasi_rumah">Lokasi Rumah <span class="text-danger">*</span></label>
                        <select name="lokasi_rumah" id="lokasi_rumah" class="form-control" required>
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach($lokasiRumah as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Tujuan dan ATR/Non-ATR --}}
            <div class="row">
                <div class="col-md-6">
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
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="atr_nonatr">ATR/Non ATR <span class="text-danger">*</span></label>
                        <select name="atr_nonatr" id="atr_nonatr" class="form-control" required>
                            <option value="">-- Pilih ATR/Non ATR --</option>
                            @foreach($atrOptions as $value => $text)
                                <option value="{{ $value }}">{{ $text }}</option>
                            @endforeach
                        </select>
                    </div>
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
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="luas_bangunan">Input Luas Bangunan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="luas_bangunan" id="luas_bangunan" class="form-control" min="0"
                                   step="0.01" required placeholder="Masukkan luas bangunan">
                            <div class="input-group-append"><span class="input-group-text">m²</span></div>
                        </div>
                        <div id="feedback_luas_bangunan" class="feedback-text"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="luas_tanah">Input Luas Tanah <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="luas_tanah" id="luas_tanah" class="form-control" min="0"
                                   step="0.01" required placeholder="Masukkan luas tanah">
                            <div class="input-group-append"><span class="input-group-text">m²</span></div>
                        </div>
                        <div id="feedback_luas_tanah" class="feedback-text"></div>
                    </div>
                </div>
            </div>

            {{-- Kebutuhan Ruang --}}
            <h5 class="mt-4 mb-3">Detail Kebutuhan Ruangan (Sesuai Standar PMK)</h5>
            <div id="kebutuhan_ruang_section" style="display: none;">
                <div class="row">
                    @foreach($ruanganList as $field => $label)
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="{{ $field }}">{{ $label }}</label>
                                <input type="number" name="{{ $field }}" id="{{ $field }}"
                                       class="form-control room-input" min="0" value="0">
                                <small class="form-text text-muted" id="help_{{ $field }}"></small>
                            </div>
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
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group"><label for="golongan">Golongan <span
                                class="text-danger">*</span></label><select name="golongan" id="golongan"
                                                                            class="form-control" required>
                            <option value="">-- Pilih Golongan --</option>@foreach($golonganOptions as $option)
                                <option value="{{ $option['value'] }}">{{ $option['text'] }}</option>
                            @endforeach</select></div>
                </div>
                <div class="col-md-3">
                    <div class="form-group"><label for="bidang">Bidang <span class="text-danger">*</span></label><select
                            name="bidang" id="bidang" class="form-control" required disabled>
                            <option value="">-- Pilih Bidang --</option>
                        </select></div>
                </div>
                <div class="col-md-3">
                    <div class="form-group"><label for="kelompok">Kelompok <span
                                class="text-danger">*</span></label><select name="kelompok" id="kelompok"
                                                                            class="form-control" required disabled>
                            <option value="">-- Pilih Kelompok --</option>
                        </select></div>
                </div>
                <div class="col-md-3">
                    <div class="form-group"><label for="subkelompok">Sub Kelompok <span
                                class="text-danger">*</span></label><select name="subkelompok" id="subkelompok"
                                                                            class="form-control" required disabled>
                            <option value="">-- Pilih Sub Kelompok --</option>
                        </select></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group"><label for="barang">Barang <span class="text-danger">*</span></label><select
                            name="barang" id="barang" class="form-control" required disabled>
                            <option value="">-- Pilih Barang --</option>
                        </select></div>
                </div>
                <div class="col-md-6">
                    <div class="form-group"><label for="kode_barang">Kode Barang</label><input type="text"
                                                                                               name="kode_barang"
                                                                                               id="kode_barang"
                                                                                               class="form-control"
                                                                                               readonly></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group"><label for="kuantitas">Kuantitas <span
                                class="text-danger">*</span></label><input type="number" name="kuantitas" id="kuantitas"
                                                                           class="form-control" min="1" value="1"
                                                                           required></div>
                </div>
                <div class="col-md-4">
                    <div class="form-group"><label for="harga_barang">Harga Barang (Rp) <span
                                class="text-danger">*</span></label><input type="text" name="harga_barang"
                                                                           id="harga_barang"
                                                                           class="form-control currency-input"
                                                                           placeholder="0" required></div>
                </div>
                <div class="col-md-4">
                    <div class="form-group"><label for="total_anggaran">Total Anggaran (Rp)</label><input type="text"
                                                                                                          name="total_anggaran"
                                                                                                          id="total_anggaran"
                                                                                                          class="form-control currency-input"
                                                                                                          readonly>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group"><label for="uraian_barang">Uraian Barang <span class="text-danger">*</span></label><textarea
                            name="uraian_barang" id="uraian_barang" class="form-control" rows="3"
                            placeholder="Masukkan uraian detail barang..." required></textarea></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group"><label for="keterangan">Keterangan</label><textarea name="keterangan"
                                                                                                id="keterangan"
                                                                                                class="form-control"
                                                                                                rows="2"
                                                                                                placeholder="Masukkan keterangan tambahan..."></textarea>
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
</style>

<script>
    // Pastikan script ini dieksekusi setelah DOM siap dan di dalam scope yang benar
    // Fungsi ini akan dipanggil oleh parent view 'CreateFormRKBMN.blade.php'
    window.initializeR3Scripts = function () {
        console.log('Initializing REFACTORED R3 Component...');

        const peruntukanMapping = @json($peruntukanMapping);
        let standarLuas = {};

        function updateFormState() {
            const peruntukan = $('#peruntukan_pejabat').val();
            const lokasi = $('#lokasi_rumah').val();
            const tipe = peruntukan ? (peruntukanMapping[peruntukan] || '') : '';
            $('#tipe_rumah').val(tipe);

            if (tipe && lokasi) {
                fetchStandards(tipe, lokasi);
            } else {
                $('#standar_luas_info, #kebutuhan_ruang_section').hide();
                $('.room-input').val(0).removeAttr('max');
                $('.feedback-text').hide();
            }
        }

        function fetchStandards(tipe, lokasi) {
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
                // Land
                if (landRes[0].success && landRes[0].landLimits) {
                    standarLuas.tanah = landRes[0].landLimits;
                    $('#luas_tanah_maks_display').text(standarLuas.tanah.luas_maksimal);
                    $('#toleransi_display').text(standarLuas.tanah.toleransi_persen);
                    $('#luas_tanah_toleransi_display').text(standarLuas.tanah.luas_dengan_toleransi);
                    validateInput($('#luas_tanah'), standarLuas.tanah.luas_dengan_toleransi, standarLuas.tanah.luas_maksimal, 'tanah');
                }
                // Building
                if (buildingRes[0].success && typeof buildingRes[0].buildingLimit !== 'undefined') {
                    standarLuas.bangunan = buildingRes[0].buildingLimit;
                    $('#luas_bangunan_maks_display').text(standarLuas.bangunan);
                    validateInput($('#luas_bangunan'), standarLuas.bangunan, standarLuas.bangunan, 'bangunan');
                }
                // Rooms
                if (roomRes[0].success && roomRes[0].kebutuhan) {
                    displayRoomRequirements(roomRes[0].kebutuhan);
                }
                $('#standar_luas_info, #kebutuhan_ruang_section').show();
            }).fail(function (xhr) {
                console.error('Failed to fetch standards:', xhr);
                alert('Gagal mengambil data standar dari server.');
            });
        }

        function displayRoomRequirements(kebutuhan) {
            $('.room-input').each(function () {
                const field = $(this).attr('name');
                const maxValue = kebutuhan[field] ?? 0;
                $(this).attr('max', maxValue).val(maxValue); // Auto-fill dengan standar
                $(`#help_${field}`).text(`Standar: ${maxValue} unit`);
            });
        }

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

        // --- Event Handlers ---
        $('#peruntukan_pejabat, #lokasi_rumah').off('change.r3').on('change.r3', updateFormState);
        $('#luas_bangunan').off('input.r3').on('input.r3', function () {
            if (standarLuas.bangunan !== undefined) validateInput($(this), standarLuas.bangunan, standarLuas.bangunan, 'bangunan');
        });
        $('#luas_tanah').off('input.r3').on('input.r3', function () {
            if (standarLuas.tanah) validateInput($(this), standarLuas.tanah.luas_dengan_toleransi, standarLuas.tanah.luas_maksimal, 'tanah');
        });
        $('.room-input').off('input.r3').on('input.r3', function () {
            const max = parseInt($(this).attr('max')) || 0;
            if (parseInt($(this).val()) > max) $(this).val(max);
            if (parseInt($(this).val()) < 0) $(this).val(0);
        });

        // --- Initial check on load ---
        if ($('#peruntukan_pejabat').val() && $('#lokasi_rumah').val()) {
            updateFormState();
        }
    }
</script>
