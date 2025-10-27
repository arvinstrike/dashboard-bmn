<div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">Review Pengajuan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <style>
                    /* Styling untuk modal review (bisa dipindahkan ke file CSS terpisah) */
                    .modal-row {
                        margin-bottom: 10px; /* Jarak antar baris */
                    }
                    .modal-label {
                        display: block; /* Agar label di atas */
                        font-weight: bold;
                        margin-bottom: 0px; /* Jarak antara label dan nilai */
                    }
                    .modal-value {
                        display: block;
                        padding: 5px;
                        font-size: 1rem;
                        line-height: 1.5;
                        color: #495057;
                        background-color: #fff;
                        background-clip: padding-box;
                        border-bottom: 1px solid #ced4da; /*Garis Bawah*/
                    }

                    .modal-luas-ruang .form-group {
                      margin-bottom: 1rem;
                    }

                    .modal-luas-ruang label {
                      font-weight: bold;
                      display: block; /* Agar label berada di atas */
                      margin-bottom: .2rem;
                    }
                    .modal-luas-ruang .input-group-text{
                      width: 50px;
                      justify-content: center;
                    }
                    .modal-luas-ruang .form-control{
                      border: 1px solid #ced4da;
                      border-right: none;
                    }
                </style>
                {{-- <input type="file" name="dokumen" id="dokumen-upload" style="display: none;"> --}}

                <div class="row">
                    <div class="col-md-4"> {{-- Kolom Kiri --}}

                        <div class="modal-row">
                            <label class="modal-label">Bagian Pelaksana:</label>
                            <span class="modal-value" id="modal-bagian-pelaksana"></span>
                        </div>
                        <div class="modal-row">
                            <label class="modal-label">Biro Pelaksana:</label>
                            <span class="modal-value" id="modal-biro-pelaksana"></span>
                        </div>
                        <div class="modal-row">
                            <label class="modal-label">Tanggal Pengajuan:</label>
                            <span class="modal-value" id="modal-tanggal-pengajuan"></span>
                        </div>
                        <div class="modal-row">
                            <label class="modal-label">Skema:</label>
                            <span class="modal-value" id="modal-skema"></span>
                        </div>
                        <div class="modal-row">
                            <label class="modal-label">Program:</label>
                            <span class="modal-value" id="modal-program"></span>
                        </div>
                        <div class="modal-row">
                          <label class="modal-label">Kegiatan:</label>
                          <span class="modal-value" id="modal-kegiatan"></span>
                        </div>
                         <div class="modal-row">
                          <label class="modal-label">Output:</label>
                          <span class="modal-value" id="modal-output"></span>
                        </div>


                    </div>

                    <div class="col-md-4"> {{-- Kolom Tengah --}}
                       {{-- Informasi Spesifik R1 (akan ditampilkan/disembunyikan dengan JavaScript) --}}
                        <div id="modal-r1-info" style="display: none;">
                            <div class="modal-row">
                                <label class="modal-label">Klasifikasi Bangunan:</label>
                                <span class="modal-value" id="modal-klasifikasi-bangunan"></span>
                            </div>
                            <div class="modal-row">
                                <label class="modal-label">Klasifikasi Pejabat:</label>
                                <span class="modal-value" id="modal-klasifikasi-pejabat"></span>
                            </div>
                            <div class="modal-row">
                                <label class="modal-label">Tujuan:</label>
                                <span class="modal-value" id="modal-tujuan"></span>
                            </div>
                            <div class="modal-row">
                                <label class="modal-label">Jenis Kantor:</label>
                                <span class="modal-value" id="modal-jenis-kantor"></span>
                            </div>
                            <div class="modal-row">
                                <label class="modal-label">Jenis Pengadaan:</label>
                                <span class="modal-value" id="modal-jenis-pengadaan"></span>
                            </div>
                            <div class="modal-row">
                                <label class="modal-label">Lokasi:</label>
                                <span class="modal-value" id="modal-lokasi"></span>
                            </div>
                        </div>
                        <div id="modal-r3-info" style="display: none;">
                            <div class="modal-row">
                                <label class="modal-label">Peruntukan Pejabat:</label>
                                <span class="modal-value form-control-plaintext" id="modal-r3-peruntukan-pejabat"></span>
                            </div>
                            <div class="modal-row">
                                <label class="modal-label">Klasifikasi Pejabat:</label>
                                <span class="modal-value form-control-plaintext" id="modal-r3-klasifikasi-pejabat"></span>
                            </div>
                            <div class="modal-row">
                                <label class="modal-label">Lokasi:</label>
                                <span class="modal-value form-control-plaintext" id="modal-r3-lokasi"></span>
                            </div>
                            <div class="modal-row">
                                <label class="modal-label">Tujuan Rumah:</label>
                                <span class="modal-value form-control-plaintext" id="modal-r3-tujuan-rumah"></span>
                            </div>
                            <div class="modal-row">
                                <label class="modal-label">Jenis Rumah:</label>
                                <span class="modal-value form-control-plaintext" id="modal-r3-jenis-rumah"></span>
                            </div>
                            <div class="modal-row">
                                <label class="modal-label">Jenis Pengadaan Rumah:</label>
                                <span class="modal-value form-control-plaintext" id="modal-r3-jenis-pengadaan-rumah"></span>
                            </div>
                        </div>
                        {{-- INFORMASI SPESIFIK R4 (Kendaraan Jabatan) --}}
                        <div id="modal-r4-info" style="display: none;">
                            <div class="modal-row">
                                <label class="modal-label">Pejabat Pemakai:</label>
                                <span class="modal-value form-control-plaintext" id="modal-r4-pejabat-pemakai"></span>
                            </div>
                            <div class="modal-row">
                                <label class="modal-label">Spesifikasi Kendaraan:</label>
                                <span class="modal-value form-control-plaintext" id="modal-r4-spesifikasi-kendaraan"></span>
                            </div>
                        </div>
                        {{-- INFORMASI SPESIFIK R5 (Kendaraan Operasional) --}}
                        <div id="modal-r5-info" style="display: none;">
                            <div class="modal-row">
                                <label class="modal-label">Jenis Satker:</label>
                                <span class="modal-value form-control-plaintext" id="modal-r5-jenis-satker"></span>
                            </div>
                            <div class="modal-row">
                                <label class="modal-label">Jenis Kendaraan:</label>
                                <span class="modal-value form-control-plaintext" id="modal-r5-jenis-kendaraan"></span>
                            </div>
                            <div class="modal-row">
                                <label class="modal-label">Tipe Kendaraan Jabatan Alih Fungsi:</label>
                                <span class="modal-value form-control-plaintext" id="modal-r5-tipe-kendaraan"></span>
                            </div>
                        </div>
                        <div class="modal-row">
                            <label class="modal-label">Dokumen Pendukung:</label>
                            <span class="modal-value" id="modal-dokumen-pendukung"></span>
                        </div>
                    </div>



                    <div class="col-md-4"> {{-- Kolom Kanan --}}
                         <div class="modal-row">
                            <label class="modal-label">Status Usulan:</label>
                            <span class="modal-value" id="modal-status-usulan"></span>
                        </div>
                        <div class="modal-row">
                            <label class="modal-label">Kode Barang:</label>
                            <span class="modal-value form-control-plaintext" id="modal-kode-barang"></span>
                        </div>
                        <div class="modal-row">
                            <label class="modal-label">Uraian Barang:</label>
                            <span class="modal-value form-control-plaintext" id="modal-uraian-barang"></span>
                        </div>
                        <div class="modal-row">
                            <label class="modal-label">Harga Satuan</label>
                            <span class="modal-value" id="modal-harga"></span>
                        </div>
                        <div class="modal-row">
                            <label class="modal-label">Kuantitas</label>
                            <span class="modal-value" id="modal-jumlah"></span>
                        </div>

                         <div class="modal-row">
                            <label class="modal-label">Total Anggaran:</label>
                            <span class="modal-value" id="modal-total-anggaran"></span>
                        </div>

                        <div class="modal-row">
                          <label class="modal-label">Keterangan</label>
                          <span class="modal-value" id="modal-keterangan"></span>
                        </div>

                    </div>
                </div>

                {{-- Detail Luas Ruang (akan ditampilkan/disembunyikan dengan JavaScript) --}}
                <div id="modal-r1-luas-ruang" style="display: none;">
                    <hr>
                    <h5>Detail Luas Ruang</h5>
                    <div class="form-group row modal-luas-ruang">
                      {{--Karena sudah tidak menggunakan table, maka untuk memunculkannya, perlu bantuan javascript--}}
                        <div class="col-sm-4">
                            <label for="modal-luas-ruang-kerja" class="control-label">Ruang Kerja</label>
                            <div class="input-group mb-3">
                                <span  class="form-control luas" id="modal-luas-ruang-kerja"></span>
                                <div class="input-group-append">
                                    <span class="input-group-text">m²</span>
                                </div>
                            </div>
                        </div>
                        {{-- ... (ulangi untuk ruang tamu, ruang rapat, dll.) ... --}}
                        <div class="col-sm-4">
                            <label for="modal-luas-ruang-tamu" class="control-label">Ruang Tamu</label>
                            <div class="input-group mb-3">
                                <span  class="form-control luas" id="modal-luas-ruang-tamu"></span>
                                <div class="input-group-append">
                                    <span class="input-group-text">m²</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label for="modal-luas-ruang-rapat" class="control-label">Ruang Rapat</label>
                            <div class="input-group mb-3">
                                <span  class="form-control luas" id="modal-luas-ruang-rapat"></span>
                                <div class="input-group-append">
                                    <span class="input-group-text">m²</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row modal-luas-ruang">

                        <div class="col-sm-4">
                            <label for="modal-luas-ruang-tunggu" class="control-label">Ruang Tunggu</label>
                            <div class="input-group mb-3">
                                <span  class="form-control luas" id="modal-luas-ruang-tunggu"></span>
                                <div class="input-group-append">
                                    <span class="input-group-text">m²</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                        <label for="modal-luas-ruang-istirahat" class="control-label">Ruang Istirahat</label>
                        <div class="input-group mb-3">
                            <span  class="form-control luas" id="modal-luas-ruang-istirahat"></span>
                            <div class="input-group-append">
                                <span class="input-group-text">m²</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4">
                      <label for="modal-luas-ruang-sekretaris" class="control-label">Ruang Sekretaris</label>
                        <div class="input-group mb-3">
                            <span  class="form-control luas" id="modal-luas-ruang-sekretaris"></span>
                            <div class="input-group-append">
                                <span class="input-group-text">m²</span>
                            </div>
                        </div>
                    </div>
                    </div>

                  <div class="form-group row modal-luas-ruang">
                    <div class="col-sm-4">
                       <label for="modal-luas-ruang-simpan" class="control-label">Ruang Simpan</label>
                        <div class="input-group mb-3">
                            <span  class="form-control luas" id="modal-luas-ruang-simpan"></span>
                            <div class="input-group-append">
                                <span class="input-group-text">m²</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label for="modal-luas-ruang-toilet" class="control-label">Ruang Toilet</label>
                        <div class="input-group mb-3">
                            <span  class="form-control luas" id="modal-luas-ruang-toilet"></span>
                            <div class="input-group-append">
                                <span class="input-group-text">m²</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label for="modal-luas-ruang-rapat-utama" class="control-label">Ruang Rapat Utama</label>
                        <div class="input-group mb-3">
                         <span  class="form-control luas" id="modal-luas-ruang-rapat-utama"></span>
                            <div class="input-group-append">
                                <span class="input-group-text">m²</span>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>

                {{-- Detail Luas Ruang R3 (akan ditampilkan/disembunyikan dengan JavaScript) --}}
                <div id="modal-r3-luas-ruang" style="display: none;">
                    <hr>
                    <h5>Detail Jumlah Ruang (Rumah Negara)</h5>

                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label class="control-label">Ruang Kerja</label>
                            <div class="input-group mb-3">
                                <span class="form-control" id="modal-r3-ruang-kerja"></span>
                                <div class="input-group-append">
                                    <span class="input-group-text">ruang</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="control-label">Ruang Duduk</label>
                            <div class="input-group mb-3">
                                <span class="form-control" id="modal-r3-ruang-duduk"></span>
                                <div class="input-group-append">
                                    <span class="input-group-text">ruang</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="control-label">Ruang Fungsional</label>
                            <div class="input-group mb-3">
                               <span class="form-control" id="modal-r3-ruang-fungsional"></span>
                                <div class="input-group-append">
                                    <span class="input-group-text">ruang</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="control-label">Ruang Makan</label>
                            <div class="input-group mb-3">
                                 <span class="form-control" id="modal-r3-ruang-makan"></span>
                                <div class="input-group-append">
                                    <span class="input-group-text">ruang</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label class="control-label">Ruang Tidur</label>
                            <div class="input-group mb-3">
                                 <span class="form-control" id="modal-r3-ruang-tidur"></span>
                                <div class="input-group-append">
                                    <span class="input-group-text">ruang</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="control-label">Kamar Mandi/WC</label>
                            <div class="input-group mb-3">
                                <span class="form-control" id="modal-r3-ruang-mandi"></span>
                                <div class="input-group-append">
                                    <span class="input-group-text">ruang</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="control-label">Dapur</label>
                            <div class="input-group mb-3">
                               <span class="form-control" id="modal-r3-ruang-dapur"></span>
                                <div class="input-group-append">
                                    <span class="input-group-text">ruang</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="control-label">Gudang</label>
                            <div class="input-group mb-3">
                               <span class="form-control" id="modal-r3-ruang-gudang"></span>
                                <div class="input-group-append">
                                    <span class="input-group-text">ruang</span>
                                </div>
                            </div>
                        </div>
                    </div>

                      <div class="form-group row">
                        <div class="col-sm-3">
                            <label class="control-label">Garasi</label>
                            <div class="input-group mb-3">
                               <span  class="form-control" id="modal-r3-ruang-garasi"></span>
                                <div class="input-group-append">
                                    <span class="input-group-text">ruang</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="control-label">Ruang Tidur Pramuwisma</label>
                            <div class="input-group mb-3">
                                 <span  class="form-control" id="modal-r3-ruang-tidur-pramuwisma"></span>
                                <div class="input-group-append">
                                    <span class="input-group-text">ruang</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="control-label">Ruang Cuci</label>
                            <div class="input-group mb-3">
                                <span class="form-control" id="modal-r3-ruang-cuci"></span>
                                <div class="input-group-append">
                                    <span class="input-group-text">ruang</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="control-label">Kamar Mandi Pramuwisma</label>
                            <div class="input-group mb-3">
                               <span class="form-control" id="modal-r3-ruang-mandi-pramuwisma"></span>
                                <div class="input-group-append">
                                    <span class="input-group-text">ruang</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bagian dalam modal-body setelah detail informasi -->
            <div id="approval-container" class="card mt-3" style="display: none;">
                <div class="card-header">
                  <h5 class="mb-0">Approval</h5>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <label class="form-label">Pilih Approval:</label>
                    <div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="approval" id="approvalSetuju" value="setuju" checked>
                        <label class="form-check-label" for="approvalSetuju">Setujui</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="approval" id="approvalTolak" value="tolak">
                        <label class="form-check-label" for="approvalTolak">Tolak</label>
                      </div>
                    </div>
                  </div>
                  <div class="mb-3" id="alasan-tolak-container" style="display: none;">
                    <label for="alasan-tolak" class="form-label">Alasan Penolakan:</label>
                    <input type="text" class="form-control" id="alasan-tolak" placeholder="Masukkan alasan penolakan">
                  </div>
                </div>
              </div>



            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <!-- Tombol simpan approval, hanya muncul jika status adalah Diajukan ke Unit Pelaksana -->
                <button type="button" class="btn btn-primary" id="save-approval" style="display: none;">Simpan</button>
            </div>



        </div>
    </div>
</div>


