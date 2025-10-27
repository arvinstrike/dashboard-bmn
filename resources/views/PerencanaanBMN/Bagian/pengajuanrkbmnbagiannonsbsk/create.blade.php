{{--resources/views/PerencanaanBMN/Bagian/pengajuanrkbmnbagiannonsbsk/CreateFormRKBMN.blade.php--}}
@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content">
        <div class="container">
            <h1>Tambah Data Pengajuan</h1>

            <!-- Informasi Biro dan Operator Bagian (Readonly) -->
            <div class="mb-3 row">
                <div class="col-sm-6">
                    <label for="biro">Biro:</label>
                    <input type="text" id="biro" class="form-control" value="{{ $uraianBiro }}" readonly>
                </div>
                <div class="col-sm-6">
                    <label for="operator">Operator Bagian:</label>
                    <input type="text" id="operator" class="form-control" value="{{ $uraianBagian }}" readonly>
                </div>
            </div>

            <!-- Form Pengajuan -->
            <form action="{{ route('pengajuan.store') }}" method="POST" id="formPengajuan">
                @csrf
                <div class="form-group row">
                    <div class="col-sm-6">
                        <label for="tahun_anggaran">Tahun Anggaran</label>
                        <select name="tahun_anggaran" id="tahun_anggaran" class="form-control" required>
                            <option value="">-- Pilih Tahun Anggaran --</option>
                            <option value="{{ $tahunAnggaran }}">{{ $tahunAnggaran }}</option>
                            <option value="{{ $tahunAnggaran + 1 }}">{{ $tahunAnggaran + 1 }}</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label for="tipe_pengajuan">Tipe Pengajuan</label>
                        <select name="tipe_pengajuan" id="tipe_pengajuan" class="form-control" required readonly>
                            <option value="">-- Tipe Pengajuan --</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-12">
                        <label for="keterangan">Keterangan</label>
                        <div id="keterangan" contenteditable="true" style="border: 1px solid #ccc;background-color:white; padding: 10px; min-height: 100px;"></div>
                        <input type="hidden" name="keterangan" id="keteranganInput">

                    </div>
                </div>

                <!-- Section Informasi Umum -->
                <div id="infoUmum">
                    <div class="form-grou row">
                        <div class="col-sm-6">
                            <label for="bagian_pelaksana">Pelaksana Pengadaan</label>
                            <select name="id_bagian_pelaksana" id="bagian_pelaksana" class="form-control">
                                <option value="">-- Pilih Bagian Pelaksana --</option>
                                @foreach($pelaksanaOptions as $option)
                                    <option value="{{ $option->id }}">{{ $option->uraianbagian }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Informasi Usulan -->
                </div>

                <!-- Section Input Barang -->
                <h3>Input Barang</h3>
                <div class="table-responsive" >
                    <table class="table table-bordered" id="tabelBarang" style="min-width: 1600px;">
                        <thead>
                            <tr>
                                <th class="">No</th>
                                <th class="col-sm-2">Pengguna</th>
                                <th class="col-sm-2">Perlengkapan</th>
                                <th class="col-sm-2">Jumlah Barang di DBR</th>
                                <th class="col-sm-2">Jumlah yang dapat diajukan</th>
                                <th class="col-sm-1">Kuantitas</th>
                                <th class="col-sm-1">Harga Barang</th>
                                <th class="col-sm-1">Total</th>
                                <th class="col-sm-1">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <!-- Baris template -->
                            <tr class="barang-row">
                                <td class="nomor">1</td>
                                <td>
                                    <select name="barang[0][pengguna]" class="form-control dropdown-pengguna">
                                        <option value="">-- Pilih Pengguna --</option>
                                        @foreach($penggunaOptions as $option)
                                            <option value="{{ $option->kode_pengguna }}">{{ $option->deskripsi_pengguna }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="barang[0][kode_perlengkapan]" class="form-control dropdown-perlengkapan">
                                        <option value="">-- Pilih Perlengkapan --</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="barang[0][dbr]" class="form-control" readonly>
                                </td>
                                <td>
                                    <input type="number" name="barang[0][jumlah_dapat_diajukan]" class="form-control" readonly>
                                </td>
                                <td>
                                    <input type="number" name="barang[0][kuantitas]" class="form-control" min="0" value="" disabled>
                                </td>
                                <td>
                                    <input type="text" name="barang[0][harga]" class="form-control" value="" min="0" disabled>
                                </td>
                                <td>
                                    <input type="text" name="barang[0][total]" class="form-control" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger remove-row">Hapus</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-secondary" id="tambahBaris">Tambah Baris</button>
                <br><br>
                <button type="submit" class="btn btn-success">Simpan Pengajuan</button>
            </form>
        </div>
    </div>
</div>

<script>

function formatNumber(value) {
    // Cek apakah nilai adalah angka yang valid dan format dengan pemisah ribuan
    if (!isNaN(value)) {
        return value.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    return value;
}

// Fungsi untuk menghapus thousand separator dan mengembalikan format raw
function removeComma(value) {
    // Hapus semua karakter selain angka dan titik
    return value.replace(/[^0-9.]/g, '');
}

// Menambahkan thousand separator saat mengetik
$(document).on('input', 'input[name*="[harga]"], input[name*="[total]"]', function() {
    // Mengambil nilai mentah tanpa pemisah ribuan
    var rawValue = $(this).val().replace(/[^0-9.]/g, '');
    $(this).val(formatNumber(rawValue));

    const $row = $(this).closest('tr');
    calculateRowTotal($row);
});

$(document).on('change', '.dropdown-perlengkapan', function() {
    const kodePerlengkapan = $(this).val();  // Ambil kode_perlengkapan yang dipilih
    const $row = $(this).closest('tr');     // Ambil baris yang berisi dropdown
    const kodePengguna = $row.find('.dropdown-pengguna').val(); // Ambil kode pengguna

    // Reset fields
    $row.find('input[name*="[dbr]"]').val('');
    $row.find('input[name*="[jumlah_dapat_diajukan]"]').val('');
    $row.find('input[name*="[kuantitas]"]').val('').prop('disabled', true);
    $row.find('input[name*="[harga]"]').val('').prop('disabled', true);
    $row.find('input[name*="[total]"]').val('');

    // Pastikan kode_perlengkapan dan kode_pengguna ada
    if (!kodePerlengkapan || !kodePengguna) return;

    // Lakukan AJAX request untuk mengambil data DBR
    $.ajax({
        url: "{{ route('pengajuan.getDBR') }}",
        type: "POST",
        data: {
            kode_perlengkapan: kodePerlengkapan,
            _token: "{{ csrf_token() }}"
        },
        dataType: "json",
        success: function(data) {
            // Tampilkan nilai DBR di input field
            $row.find('input[name*="[dbr]"]').val(data.dbr);
        },
        error: function(xhr, status, error) {
            console.error("Gagal mengambil data DBR.");
            console.error("Response:", xhr.responseText);
        }
        });
    });


$(document).ready(function() {

    // Update dropdown options to disable already selected items
    function updateDropdownOptions() {
        const selectedItems = [];

        // Collect all selected values
        $('#tabelBarang .dropdown-perlengkapan').each(function() {
            const selectedVal = $(this).val();
            if (selectedVal) {
                selectedItems.push(selectedVal);
            }
        });

        // Update each dropdown
        $('#tabelBarang .dropdown-perlengkapan').each(function() {
            const currentSelect = $(this);
            const currentVal = currentSelect.val();

            currentSelect.find('option').each(function() {
                const optionVal = $(this).attr('value');
                if (optionVal && selectedItems.indexOf(optionVal) > -1 && optionVal !== currentVal) {
                    $(this).prop('disabled', true);
                } else {
                    $(this).prop('disabled', false);
                }
            });
        });
    }

    // Calculate row total (quantity * price)
    function calculateRowTotal($row) {
        const quantity = parseInt($row.find('input[name*="[kuantitas]"]').val()) || 0;
        const price = parseFloat(removeComma($row.find('input[name*="[harga]"]').val())) || 0; // Menghapus koma dari harga
        const total = quantity * price;

        // Format total dengan thousand separator dan show it
        $row.find('input[name*="[total]"]').val(formatNumber(total.toFixed(2))); // Gunakan formatNumber untuk total
    }

    // Toggle form sections based on selected year
    $('#tahun_anggaran').on('change', function() {
        const tahun = parseInt($(this).val());
        const tahunSession = parseInt({{ $tahunAnggaran }});
        const $tipePengajuan = $('#tipe_pengajuan');

        if (tahun === tahunSession) {
            $tipePengajuan.html('<option value="revisi" selected>Revisi Anggaran Barang Non SBSK</option>');
            $('#infoRevisi').show();
            $('#infoUsulan').hide();
        } else {
            $tipePengajuan.html('<option value="usulan" selected>Usulan Anggaran Barang Non SBSK</option>');
            $('#infoRevisi').hide();
            $('#infoUsulan').show();
        }
    });

    // Fetch perlengkapan data when pengguna selection changes
    $('#tabelBarang').on('change', '.dropdown-pengguna', function() {
        const kode_pengguna = $(this).val();
        const $row = $(this).closest('tr');

        // Reset fields
        $row.find('.dropdown-perlengkapan').empty().append('<option value="">-- Pilih Perlengkapan --</option>');
        $row.find('input[name*="[jumlah_dapat_diajukan]"]').val('');
        $row.find('input[name*="[kuantitas]"]').val('1').prop('disabled', true);
        $row.find('input[name*="[harga]"]').val('').prop('disabled', true);
        $row.find('input[name*="[total]"]').val('');

        if (!kode_pengguna) return;

        $.ajax({
            url: "{{ route('perlengkapan.byPengguna') }}",
            type: "GET",
            data: { kode_pengguna: kode_pengguna },
            dataType: "json",
            success: function(data) {
                const $selectPerlengkapan = $row.find('.dropdown-perlengkapan');
                $selectPerlengkapan.empty().append('<option value="">-- Pilih Perlengkapan --</option>');

                $.each(data, function(index, item) {
                    $selectPerlengkapan.append(
                        '<option value="' + item.kode_perlengkapan + '" data-batasan="' + item.batasan_jumlah + '">' +
                        item.deskripsi_perlengkapan + '</option>'
                    );
                });

                updateDropdownOptions();
            },
            error: function(xhr, status, error) {
                console.error("Error fetching perlengkapan:", status, error);
                console.error("Response:", xhr.responseText);
            }
        });
    });

    // Fetch maximum quantity when perlengkapan selection changes
    $('#tabelBarang').on('change', '.dropdown-perlengkapan', function() {
        const $select = $(this);
        const kodePerlengkapan = $select.val();
        const $row = $select.closest('tr');
        const kodePengguna = $row.find('.dropdown-pengguna').val();

        // Reset fields
        $row.find('input[name*="[jumlah_dapat_diajukan]"]').val('');
        $row.find('input[name*="[kuantitas]"]').val('1').prop('disabled', true);
        $row.find('input[name*="[harga]"]').val('').prop('disabled', true);
        $row.find('input[name*="[total]"]').val('');

        if (!kodePerlengkapan || !kodePengguna) return;

        $.ajax({
            url: "{{ route('pengajuan.getKuantitasMaksimal') }}",
            type: "POST",
            data: {
                kode_pengguna: kodePengguna,
                kode_perlengkapan: kodePerlengkapan,
                _token: "{{ csrf_token() }}"
            },
            dataType: "json",
            success: function(data) {
                // Display max quantity
                $row.find('input[name*="[jumlah_dapat_diajukan]"]').val(data.kuantitas_maksimal);

                // Enable/disable inputs based on availability
                if (data.dapat_diinput) {
                    $row.find('input[name*="[kuantitas]"]')
                        .prop('disabled', false)
                        .attr('max', data.kuantitas_maksimal)
                        .attr('title', 'Maksimal: ' + data.kuantitas_maksimal);

                    $row.find('input[name*="[harga]"]').prop('disabled', false);
                } else {
                    $row.find('input[name*="[kuantitas]"]').prop('disabled', true);
                    $row.find('input[name*="[harga]"]').prop('disabled', true);

                    Swal.fire({
                        title: 'Informasi',
                        text: 'Kuantitas maksimal sudah terpenuhi untuk item ini.',
                        icon: 'info'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("Gagal mengambil data kuantitas maksimal.");
                console.error("Response:", xhr.responseText);
            }
        });

        updateDropdownOptions();
    });

    // Handle quantity input changes
    $('#tabelBarang').on('input', 'input[name*="[kuantitas]"]', function() {
        const $input = $(this);
        const $row = $input.closest('tr');
        const kuantitasMaksimal = parseInt($row.find('input[name*="[jumlah_dapat_diajukan]"]').val()) || 0;

        // Allow empty input during typing (remove the minimum validation here)
        let currentVal = $input.val() === '' ? '' : (parseInt($input.val()) || 0);

        // Only validate maximum while typing
        if (currentVal !== '' && currentVal > kuantitasMaksimal) {
            Swal.fire({
                title: 'Peringatan',
                text: 'Kuantitas tidak boleh melebihi ' + kuantitasMaksimal + '.',
                icon: 'warning'
            });
            $input.val(kuantitasMaksimal);
            currentVal = kuantitasMaksimal;
        }

        calculateRowTotal($row);
    });

    // Handle price input changes
    $('#tabelBarang').on('input', 'input[name*="[harga]"]', function() {
        calculateRowTotal($(this).closest('tr'));
    });

    // Ensure minimum quantity when focus is lost
    $('#tabelBarang').on('blur', 'input[name*="[kuantitas]"]', function() {
        const $input = $(this);
        const val = $input.val();

        if (val === '' || parseInt(val, 10) < 1) {
            $input.val(1);
            calculateRowTotal($input.closest('tr'));
        }
    });

    // Add new row to table
    $('#tambahBaris').on('click', function() {
        const $tableBody = $('#tabelBarang tbody');
        const $newRow = $tableBody.find('.barang-row').first().clone();
        const rowCount = $tableBody.find('tr').length;

        $newRow.find('.nomor').text(rowCount + 1);

        // Update input names and reset values
        $newRow.find('input, select').each(function() {
            const name = $(this).attr('name');
            if (name) {
                const newName = name.replace(/\d+/, rowCount);
                $(this).attr('name', newName);

                if ($(this).is('input') && $(this).attr('type') !== 'hidden') {
                    $(this).val('');
                } else if ($(this).is('select')) {
                    $(this).prop('selectedIndex', 0);
                }
            }
        });

        // Reset specific fields
        $newRow.find('input[name*="[jumlah_dapat_diajukan]"]').val('');
        $newRow.find('input[name*="[kuantitas]"]').val('').prop('disabled', true);
        $newRow.find('input[name*="[harga]"]').val('').prop('disabled', true);
        $newRow.find('input[name*="[total]"]').val('');

        $tableBody.append($newRow);
        updateDropdownOptions();
    });

    // Remove row from table
    $('#tabelBarang').on('click', '.remove-row', function() {
        const $rows = $('#tabelBarang tbody tr');

        if ($rows.length > 1) {
            $(this).closest('tr').remove();

            // Update row numbers
            $('#tabelBarang tbody tr').each(function(index) {
                $(this).find('.nomor').text(index + 1);
            });

            updateDropdownOptions();
        }
    });

    // Load outputs based on selected kegiatan
    $('#kode_kegiatan').on('change', function() {
        const kodekegiatan = $(this).val();

        $.ajax({
            url: '{{ route("pengajuan.getOutputByKegiatan") }}',
            type: 'GET',
            data: { kodekegiatan: kodekegiatan },
            dataType: 'json',
            success: function(outputs) {
                const $outputSelect = $('#kode_output');
                $outputSelect.empty().append('<option value="">-- Pilih Kode Output --</option>');

                $.each(outputs, function(index, output) {
                    $outputSelect.append(
                        '<option value="' + output.kode + '">' + output.kode + ' | ' + output.deskripsi + '</option>'
                    );
                });
            },
            error: function(xhr, status, error) {
                console.error("Gagal mengambil data output.");
            }
        });
    });

    // Submit form via AJAX
    $('#formPengajuan').on('submit', function(e) {
        e.preventDefault();

        $('input[name*="[harga]"], input[name*="[total]"]').each(function() {
            var rawValue = removeComma($(this).val()); // Remove comma
            $(this).val(rawValue); // Set value in raw format
        });

        $('#keteranganInput').val($('#keterangan').html().trim());

        const formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.status === 'berhasil') {
                    Swal.fire({
                    title: 'Sukses',
                    text: 'Data pengajuan berhasil disimpan.',
                    icon: 'success'
                }).then(() => {
                    window.location.href = response.redirect;
                });
                    $('#formPengajuan').trigger("reset");
                    updateDropdownOptions();
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Data pengajuan gagal disimpan.',
                        icon: 'error'
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = '';

                if (xhr.status === 0) {
                    errorMessage = "Tidak ada koneksi internet. Silakan periksa koneksi Anda.";
                } else if (xhr.status >= 400 && xhr.status < 500) {
                    const response = xhr.responseJSON;

                    if (response && response.errors) {
                        const errorsArr = [];
                        $.each(response.errors, function(key, value) {
                            errorsArr.push(value);
                        });
                        errorMessage = errorsArr.join("<br>");
                    } else if (response && response.message) {
                        errorMessage = response.message;
                    } else {
                        errorMessage = "Terjadi kesalahan pada permintaan Anda.";
                    }
                } else if (xhr.status >= 500) {
                    errorMessage = "Terjadi kesalahan pada server. Silakan coba lagi nanti.";
                } else {
                    errorMessage = "Terjadi kesalahan yang tidak diketahui.";
                }

                Swal.fire({
                    title: 'Error!',
                    html: errorMessage,
                    icon: 'error'
                });
            }
        });
    });
});
</script>
@endsection
