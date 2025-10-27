$(document).ready(function() {

    $('#tabelbagian').on('click', '.review-pengajuan', function() {
        var id = $(this).data('id');
        var url = baseUrl + "/" + id;

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
            if (response.error) {
                alert("Error: " + response.error);
                return;
            }
                $('#save-approval').data('id', response.data.id);

                var jenisForm = response.data.kode_jenis_pengajuan.substring(0, 2);
                // Informasi Umum
                $('#modal-bagian-pelaksana').text(response.uraianBagianPelaksana || '-');
                $('#modal-biro-pelaksana').text(response.uraianBiroPelaksana || '-');
                $('#modal-tanggal-pengajuan').text(response.tanggalPengajuan || '-');
                $('#modal-skema').text(response.data.skema || '-');
                $('#modal-program').text(response.data.program || '-');
                $('#modal-kegiatan').text(response.data.kegiatan || '-');
                $('#modal-output').text(response.data.output || '-');
                $('#modal-kode-barang').text(response.data.kode_barang || '-');
                $('#modal-uraian-barang').text(response.uraianBarang || '-');
                $('#modal-keterangan').text(response.data.keterangan || '-');

                $('#modal-status-usulan').text(response.data.status || '-');
                // $('#modal-dokumen-pendukung').html(renderDokumenPendukungSederhana(response.data.dokumenpendukung));
                $('#modal-harga').text(response.data.harga_barang || '-');
                $('#modal-jumlah').text(response.data.quantitas || '-');
                $('#modal-total-anggaran').text(response.data.total_anggaran || '-');

                // Informasi Kode Jenis Pengajuan
                if (jenisForm === 'R1' && response.detailData) {
                            $('#reviewModalLabel').text('Review Pengajuan Tanah dan/atau Bangunan Perkantoran');
                            $('#modal-klasifikasi-bangunan').text(response.detailData.klasifikasi_bangunan || '-');

                            $('#modal-klasifikasi-pejabat').text(response.detailData.klasifikasi_pejabat || '-');

                            $('#modal-tujuan').text(response.data.tujuan || '-');
                            // $('#modal-jenis-kantor').text(response.data.jenis_kantor || '-');
                            // $('#modal-jenis-pengadaan').text(response.data.jenis_pengadaan || '-');
                            $('#modal-lokasi').text(response.detailData.lokasi || '-');
                            // Detail Luas Ruang
                            $('#modal-luas-ruang-kerja').text(response.detailData.luas_ruang_kerja || '-');
                            $('#modal-luas-ruang-tamu').text(response.detailData.luas_ruang_tamu || '-');
                            $('#modal-luas-ruang-rapat').text(response.detailData.luas_ruang_rapat || '-');
                            $('#modal-luas-ruang-tunggu').text(response.detailData.luas_ruang_tunggu || '-');
                            $('#modal-luas-ruang-istirahat').text(response.detailData.luas_ruang_istirahat || '-');
                            $('#modal-luas-ruang-sekretaris').text(response.detailData.luas_ruang_sekretaris || '-');
                            $('#modal-luas-ruang-simpan').text(response.detailData.luas_ruang_simpan || '-');
                            $('#modal-luas-ruang-toilet').text(response.detailData.luas_ruang_toilet || '-');
                            $('#modal-luas-ruang-rapat-utama').text(response.detailData.luas_ruang_rapat_utama || '-');
                            $('#modal-r1-info').show();

                            $('#modal-r1-luas-ruang').show();
                            $('#modal-r3-info').hide();
                            $('#modal-r3-luas-ruang').hide();
                            $('#modal-r4-info').hide();
                            $('#modal-r5-info').hide();
                        // } else if (jenisForm === 'R2' && response.detailData) {

                            // Data masih kosong
                        } else if (jenisForm === 'R3' && response.detailData) {

                            $('#reviewModalLabel').text('Review Pengajuan Rumah Negara');

                            $('#modal-r3-peruntukan-pejabat').text(response.detailData.peruntukan_pejabat || '-');
                            $('#modal-r3-klasifikasi-pejabat').text(response.detailData.klasifikasi_pejabat || '-');
                            $('#modal-r3-lokasi').text(response.detailData.lokasi || '-');
                            $('#modal-r3-tujuan-rumah').text(response.data.tujuan_rumah || '-');
                            $('#modal-r3-jenis-rumah').text(response.detailData.jenis_rumah || '-');
                            $('#modal-r3-jenis-pengadaan-rumah').text(response.detailData.jenis_pengadaan_rumah || '-');


                            // Detail jumlah ruang R3
                            $('#modal-r3-ruang-kerja').text(response.detailData.jumlah_ruang_kerja || '-');
                            $('#modal-r3-ruang-duduk').text(response.detailData.jumlah_ruang_duduk || '-');
                            $('#modal-r3-ruang-fungsional').text(response.detailData.jumlah_ruang_fungsional || '-');
                            $('#modal-r3-ruang-makan').text(response.detailData.jumlah_ruang_makan || '-');
                            $('#modal-r3-ruang-tidur').text(response.detailData.jumlah_ruang_tidur || '-');
                            $('#modal-r3-ruang-mandi').text(response.detailData.jumlah_ruang_wc || '-');
                            $('#modal-r3-ruang-dapur').text(response.detailData.jumlah_dapur || '-');
                            $('#modal-r3-ruang-gudang').text(response.detailData.jumlah_gudang || '-');
                            $('#modal-r3-ruang-garasi').text(response.detailData.jumlah_garasi || '-');
                            $('#modal-r3-ruang-tidur-pramuwisma').text(response.detailData.jumlah_ruang_tidur_pramuwisma || '-');
                            $('#modal-r3-ruang-cuci').text(response.detailData.jumlah_ruang_cuci || '-');
                            $('#modal-r3-ruang-mandi-pramuwisma').text(response.detailData.jumlah_kamar_mandi_pramuwisma || '-');


                            $('#modal-r1-info').hide();
                            $('#modal-r1-luas-ruang').hide();
                            $('#modal-r3-info').show();
                            $('#modal-r3-luas-ruang').show();
                            $('#modal-r4-info').hide();
                            $('#modal-r5-info').hide();

                        } else if (jenisForm === 'R4' && response.detailData) {
                            $('#reviewModalLabel').text('Review Pengajuan Kendaraan Jabatan');

                            $('#modal-r4-pejabat-pemakai').text(response.detailData.pejabat_pemakai || '-');
                            $('#modal-r4-spesifikasi-kendaraan').text(response.detailData.spesifikasi || '-');

                            $('#modal-r1-info').hide();
                            $('#modal-r1-luas-ruang').hide();
                            $('#modal-r3-info').hide();
                            $('#modal-r3-luas-ruang').hide();
                            $('#modal-r4-info').show();
                            $('#modal-r5-info').hide();

                        }  else if (jenisForm === 'R5' && response.detailData) {
                            $('#reviewModalLabel').text('Review Pengajuan Kendaraan Operasional');

                            $('#modal-r5-jenis-satker').text(response.detailData.jenis_satker || '-');
                            $('#modal-r5-jenis-kendaraan').text(response.detailData.jenis_kendaraan || '-');
                            $('#modal-r5-tipe-kendaraan').text(response.detailData.tipe_kendaraan_jabatan_alih_fungsi || '-');

                            $('#modal-r1-info').hide();
                            $('#modal-r1-luas-ruang').hide();
                            $('#modal-r3-info').hide();
                            $('#modal-r3-luas-ruang').hide();
                            $('#modal-r4-info').hide();
                            $('#modal-r5-info').show();
                        // } else if (jenisForm === 'R6' && response.detailData) {

                        } else {
                            $('#reviewModalLabel').text('Review Pengajuan');
                            $('#modal-r1-info').hide();
                            $('#modal-r1-luas-ruang').hide();
                            $('#modal-r3-info').hide();
                            $('#modal-r3-luas-ruang').hide();
                            $('#modal-r4-info').hide();
                            $('#modal-r5-info').hide();
                        }


                        if (response.status == "Diajukan Ke Unit Pelaksana") {
                            $('#approval-container').show();
                            $('#save-approval').show();
                        } else {
                            $('#approval-container').hide();
                            $('#save-approval').hide();
                        }

                $('#reviewModal').modal('show');
            },
            error: function(xhr, status, error) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    // Jika ada error validasi
                    var errorsArr = [];
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        errorsArr.push(value);
                    });
                    alert("Error Validasi:\n" + errorsArr.join("\n"));
                } else {
                    // Jika error lain
                    var jsonValue = xhr.responseJSON ? xhr.responseJSON : jQuery.parseJSON(xhr.responseText);
                    var errorMessage = jsonValue && jsonValue.message ? jsonValue.message : 'Terjadi kesalahan: ' + error;
                      alert(errorMessage);
                }
                 console.error(xhr.responseText);
            }
        });
    });

    // Event handler untuk menampilkan field alasan jika opsi "tolak" dipilih
    $(document).on('change', 'input[name="approval"]', function() {
        if ($(this).val() === 'tolak') {
            $('#alasan-tolak-container').show();
        } else {
            $('#alasan-tolak-container').hide();
            $('#alasan-tolak').val('');
        }
    });

    $('#reviewModal').on('click', '#save-approval', function() {
        var id = $(this).data('id');
        var approval = $('input[name="approval"]:checked').val();
        var alasan = (approval === 'tolak') ? $('#alasan-tolak').val() : '';
        // Jika perlu, ambil nilai tahun pelaksanaan dari input terkait
        var ajukanUrl = kirimUrl + "/" + id;

        $.ajax({
            // Arahkan ke route baru: /persetujuanpelaksanabagian/{id}
            url: ajukanUrl,
            type: 'GET',
            dataType: 'json',
            data: {
                approval: approval,
                alasan: alasan
            },
            success: function(data) {
                if (data.status === 'berhasil') {
                    Swal.fire({
                        title: 'Sukses',
                        text: 'Data berhasil disimpan',
                        icon: 'success'
                    });
                    $('#reviewModal').modal('hide');
                    $('#tabelbagian').DataTable().ajax.reload();
                }
            },
            error: function(xhr, status, error) {
                var errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Terjadi kesalahan: ' + error;
                Swal.fire({
                    title: 'Error!',
                    text: errorMessage,
                    icon: 'error'
                });
            }
        });
    });



});

