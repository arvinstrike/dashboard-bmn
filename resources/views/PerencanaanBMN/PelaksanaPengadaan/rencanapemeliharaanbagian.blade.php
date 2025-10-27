@extends('layouts.app')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <div class="col-sm-6">
                            @if(session('status'))
                                <div class="alert alert-success">
                                    {{session('status')}}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">{{$judul}}</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <div class="container">

                <div class="card">
                    <div class="card-header">
                        <div class="btn-group float-sm-right" role="group">
                            <a class="btn btn-success float-sm-right" href="javascript:void(0)" id="tambahdata"> Tambah Data</a>
                        </div>
                        <h3 class="card-title">{{$judul}}</h3>
                    </div>
                    <div class="card-body">
                        <table id="tabelbagian" class="table table-bordered table-striped tabelbagian">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Tanggal Ke BMN</th>
                                <th>Tanggal Ke Perencanaan</th>
                                <th>Tanggal Final</th>
                                <th>Kode Barang</th>
                                <th>Uraian Barang</th>
                                <th>NUP</th>
                                <th>Tgl Perolehan</th>
                                <th>Merek/Tipe</th>
                                <th>Luas</th>
                                <th>Kondisi Barang</th>
                                <th>Foto Barang</th>
                                <th>Keterangan</th>
                                <th>Status Usulan</th>
                                <th>Dokumen Pendukung</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Tanggal Ke BMN</th>
                                <th>Tanggal Ke Perencanaan</th>
                                <th>Tanggal Final</th>
                                <th>Kode Barang</th>
                                <th>Uraian Barang</th>
                                <th>NUP</th>
                                <th>Tgl Perolehan</th>
                                <th>Merek/Tipe</th>
                                <th>Luas</th>
                                <th>Kondisi Barang</th>
                                <th>Foto Barang</th>
                                <th>Keterangan</th>
                                <th>Status Usulan</th>
                                <th>Dokumen Pendukung</th>
                                <th>Aksi</th>
                            </tr>
                            </tfoot>
                        </table>
                        <div class="modal fade" id="ajaxModel" aria-hidden="true" data-focus="false">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="modelHeading"></h4>
                                    </div>
                                    <div class="modal-body">
                                        <form id="formbagian" name="formbagian" class="form-horizontal">
                                            <input type="hidden" name="idbagianawal" id="idbagianawal">
                                            <input type="hidden" name="outputawal" id="outputawal">
                                            <input type="hidden" name="id" id="id">
                                            <input type="hidden" name="dokumenpendukungawal" id="dokumenpendukungawal">
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label for="deputi" class="control-label">Jenis Tabel</label>
                                                    <select class="form-control jenistabel" name="jenistabel" id="jenistabel" style="width: 100%;">
                                                        <option value="">Pilih Jenis Tabel</option>
                                                        <option value="R1">Pengadaan Tanah</option>
                                                        <option value="R2">Pengadaan Gedung dan Bangunan</option>
                                                        <option value="R3">Pengadaan Kendaraan Dinas Jabatan</option>
                                                        <option value="R4">Pengadaan Kendaraan Operasional</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <!--
                                            form khusus untuk tanah
                                            -->
                                            <fieldset id="tanahFieldset">
                                                <legend>Informasi Pengadaan Tanah/Gedung</legend>
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <label for="deputi" class="control-label">Tujuan</label>
                                                        <select class="form-control tujuanrencana" name="tujuanrencana" id="tujuanrencana" style="width: 100%;">
                                                            <option value="">Pilih Tujuan Rencana</option>
                                                            <option value="Perluasan">Perluasan</option>
                                                            <option value="Tambah Unit">Tambah Unit</option>
                                                            <option value="Khusus Lainnya">Khusus Lainnya</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-6" id="atrNonAtrDiv">
                                                        <label for="deputi" class="control-label">ATR/Non ATR</label>
                                                        <select class="form-control atrnonatr" name="atrnonatr" id="atrnonatr" style="width: 100%;">
                                                            <option value="">Pilih ATR/Non ATR</option>
                                                            <option value="ATR">ATR</option>
                                                            <option value="Non ATR">Non ATR</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <label for="deputi" class="control-label">Jenis Kantor</label>
                                                        <select class="form-control jeniskantor" name="jeniskantor" id="jeniskantor" style="width: 100%;">
                                                            <option value="">Pilih Jenis Kantor</option>
                                                            <option value="Kementerian dan Lembaga">Kementerian dan Lembaga</option>
                                                            <option value="Kantor Eselon I">Kantor Eselon I</option>
                                                            <option value="Kantor Eselon II">Kantor Eselon II</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label for="deputi" class="control-label">Jenis Pengadaan</label>
                                                        <select class="form-control jenispengadaan" name="jenispengadaan" id="jenispengadaan" style="width: 100%;">
                                                            <option value="">Pilih Jenis Pengadaan</option>
                                                            <option value="001 - Bangunan">Bangunan</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <label for="nilaibukti" class="control-label">Lokasi</label>
                                                        <div class="input-group mb-3">
                                                            <textarea class="form-control" id="lokasi" name="lokasi" placeholder="Lokasi" value="" required=""></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label for="nilaibukti" class="control-label">Luas</label>
                                                        <div class="input-group mb-3">
                                                            <input type="text" class="form-control luas" id="luas" name="luas" placeholder="Luas" value="" required="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                            <fieldset id="kendaraanJabatanFieldset">
                                                <legend>Informasi Pengadaan Kendaraan Dinas Jabatan</legend>
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <label for="deputi" class="control-label">Pejabat Pemakai</label>
                                                        <select class="form-control pejabatpemakai" name="pejabatpemakai" id="pejabatpemakai" style="width: 100%;">
                                                            <option value="">Pilih Pejabat Pemakai</option>
                                                            <option value="Eselon I A Kepala Kantor">Eselon I A Kepala Kantor</option>
                                                            <option value="Eselon I A Non Kepala Kantor">Eselon I A Non Kepala Kantor</option>
                                                            <option value="Eselon I B Non Kepala Kantor">Eselon I B Non Kepala Kantor</option>
                                                            <option value="Eselon II A Non Kepala Kantor">Eselon II A Non Kepala Kantor</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label for="deputi" class="control-label">Spesifikasi</label>
                                                        <select class="form-control spesifikasi" name="spesifikasi" id="spesifikasi" style="width: 100%;">
                                                            <option value="">Pilih Spesifikasi</option>
                                                            <option value="A - Sedan - 3500 cc, 6 Silinder">A - Sedan - 3500 cc 6 Silinder</option>
                                                            <option value="A - SUV - 3500 cc, 6 Silinder">A - SUV - 3500 cc 6 Silinder</option>
                                                            <option value="A - MPV - 3500 cc, 6 Silinder">A - MPV - 3500 cc 6 Silinder</option>
                                                            <option value="B - Sedan - 2500 cc, 4 Silinder">B - Sedan - 2500 cc, 4 Silinder</option>
                                                            <option value="B - SUV - 3000 cc, 6 Silinder">B - SUV - 3000 cc, 6 Silinder</option>
                                                            <option value="C - Sedan - 2000 cc, 4 Silinder">C - Sedan - 2000 cc, 4 Silinder</option>
                                                            <option value="C - SUV - 2500 cc, 4 Silinder">C - SUV - 2500 cc, 4 Silinder</option>
                                                            <option value="D - SUV - 2500 cc, 4 Silinder">D - SUV - 2500 cc, 4 Silinder</option>
                                                            <option value="E - SUV - 2000 cc, 4 Silinder">E - SUV - 2000 cc, 4 Silinder</option>
                                                            <option value="F - MPV - 2000 cc Bensin, 4 Silinder">F - MPV - 2000 cc Bensin, 4 Silinder</option>
                                                            <option value="F - MPV - 2000 cc Diesel, 4 Silinder">F - MPV - 2000 cc Diesel, 4 Silinder</option>
                                                            <option value="G - MPV - 1500 cc, 4 Silinder">G - MPV - 1500 cc, 4 Silinder</option>
                                                            <option value="G - Sepeda Motor - 225 cc, 1 Silinder">G - Sepeda Motor - 225 cc, 1 Silinder</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </fieldset>
                                            <fieldset id="kendaraanOperasionalFieldset">
                                                <legend>Informasi Pengadaan Kendaraan Dinas Operasional</legend>
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <label for="deputi" class="control-label">Jenis Satker</label>
                                                        <select class="form-control jenissatker" name="jenissatker" id="jenissatker" style="width: 100%;">
                                                            <option value="">Pilih Jenis Satker</option>
                                                            <option value="Eselon I">Eselon I</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label for="deputi" class="control-label">Jenis Kendaraan</label>
                                                        <select class="form-control jeniskendaraan" name="jeniskendaraan" id="jeniskendaraan" style="width: 100%;">
                                                            <option value="">Pilih Jenis Kendaraan</option>
                                                            <option value="Roda 2">Roda 2</option>
                                                            <option value="Roda 4">Roda 4</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </fieldset>

                                            <fieldset id="informasiUmum">
                                                <legend>Informasi Umum</legend>
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <label for="deputi" class="control-label">Skema</label>
                                                        <select class="form-control skema" name="skema" id="skema" style="width: 100%;">
                                                            <option value="">Pilih Skema</option>
                                                            <option value="Pembelian">Pembelian</option>
                                                            <option value="Sewa">Sewa</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label for="Program" class="control-label">Program</label>
                                                        <select class="form-control program" name="program" id="program" style="width: 100%;">
                                                            <option value="">Program</option>
                                                            @foreach($dataprogram as $data)
                                                                <option value="{{ $data->kode }}">{{ $data->kode." | ".$data->uraianprogram }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <label for="deputi" class="control-label">Kegiatan</label>
                                                        <select class="form-control kegiatan" name="kegiatan" id="kegiatan" style="width: 100%;">
                                                            <option value="">Pilih Kode Kegiatan</option>
                                                            @foreach($datakegiatan as $data)
                                                                <option value="{{ $data->kode }}">{{ $data->kode." | ".$data->deskripsi }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label for="output" class="control-label">Output</label>
                                                        <select class="form-control output" name="output" id="output" style="width: 100%;">
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <label for="deputi" class="control-label">Kode Barang</label>
                                                        <select class="form-control kodebarang" name="kodebarang" id="kodebarang" style="width: 100%;">
                                                            <option value="Pilih">Pilih Kode Barang</option>
                                                            @foreach($databmnrk as $data)
                                                                <option value="{{ $data->kdbrg }}">{{ $data->deskripsi }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group row">
                                                            <div class="col-sm-6">
                                                                <label for="keterangan" class="control-label">Barang Tersedia DBR</label>
                                                                <div class="input-group mb-3">
                                                                    <input type="text" class="form-control" id="barangtersediadbr" name="barangtersediadbr" placeholder="Barang Tersedia DBR" value="" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <label for="keterangan" class="control-label">Barang Tersedia Total</label>
                                                                <div class="input-group mb-3">
                                                                    <input type="text"  class="form-control" id="barangtersediatotal" name="barangtersediatotal" placeholder="Barang Tersedia Total" value="" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <label for="keterangan" class="control-label">Uraian/Spesifikasi Barang</label>
                                                        <div class="input-group mb-3">
                                                            <textarea class="form-control" id="uraianbarang" name="uraianbarang" placeholder="Uraian/Spesifikasi" value="" required=""></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label for="keterangan" class="control-label">Keterangan</label>
                                                        <div class="input-group mb-3">
                                                            <textarea class="form-control" id="keterangan" name="keterangan" placeholder="Keterangan" value="" required=""></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <label for="deputi" class="control-label">Bagian Pelaksana</label>
                                                        <select class="form-control idbagianpelaksana" name="idbagianpelaksana" id="idbagianpelaksana" style="width: 100%;">
                                                            <option value="">Pilih Bagian Pelaksana</option>
                                                            @foreach($databagianrk as $data)
                                                                <option value="{{ $data->idbagian }}">{{ $data->uraianbagian }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label for="deputi" class="control-label">Usulan Tahun Pengadaan</label>
                                                        <select class="form-control tahunpengadaan" name="tahunpengadaan" id="tahunpengadaan" style="width: 100%;">
                                                            <option value="">Pilih Tahun Pengadaan</option>
                                                            @foreach($datatahunanggaran as $data)
                                                                <option value="{{ $data }}">{{ $data }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <label for="nilaibukti" class="control-label">Kuantitas</label>
                                                        <div class="input-group mb-3">
                                                            <input type="number" class="form-control" id="quantitas" name="quantitas" placeholder="Kuantitas Pengajuan" value="" required="">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label for="nilaibukti" class="control-label">Harga Barang</label>
                                                        <div class="input-group mb-3">
                                                            <input type="text" class="form-control" id="hargabarang" name="hargabarang" placeholder="Harga Barang" value="" required="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <label for="nilaibukti" class="control-label">Total Anggaran</label>
                                                        <div class="input-group mb-3">
                                                            <input type="text" class="form-control" id="totalanggaran" name="totalanggaran" placeholder="Total Harga Barang" value="" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="input-group">
                                                            <label for="file" class="control-label">Dokumen Pendukung</label>
                                                            <div class="input-group mb-3">
                                                                <div class="custom-file">
                                                                    <input type="file" class="custom-file-input" id="file" name="file">
                                                                    <label class="custom-file-label" for="exampleInputFile">Pilih File</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group" id="linkbukti" aria-hidden="true">
                                                            <a href="#" id="aktuallinkbukti">Lihat Bukti</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                            <div class="col-sm-offset-2 col-sm-10">
                                                <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Simpan Data</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content -->
    <script src="{{env('APP_URL')."/".asset('AdminLTE/plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // Sembunyikan semua fieldset pada awalnya
            $('#tanahFieldset').hide();
            $('#kendaraanJabatanFieldset').hide();

            // Tangani perubahan pada dropdown 'jenistabel'
            $('#jenistabel').change(function() {
                var selectedValue = $(this).val();

                // Sembunyikan semua fieldset terlebih dahulu
                $('#tanahFieldset').hide();
                $('#kendaraanJabatanFieldset').hide();
                $('#kendaraanOperasionalFieldset').hide();

                // Tampilkan fieldset yang sesuai dengan pilihan pengguna
                if (selectedValue === 'R1' || selectedValue === 'R2') {
                    $('#tanahFieldset').show();
                } else if (selectedValue === 'R3') {
                    $('#kendaraanJabatanFieldset').show();
                }else if (selectedValue === 'R4') {
                    $('#kendaraanOperasionalFieldset').show();
                }
            });
        });
        function toggleSecondComboBox() {
            var tujuanValue = document.getElementById("tujuanrencana").value;
            var atrNonAtrDiv = document.getElementById("atrNonAtrDiv");

            if (tujuanValue === "Perluasan") {
                atrNonAtrDiv.style.display = "block";  // Tampilkan combobox kedua
            } else {
                atrNonAtrDiv.style.display = "none";  // Sembunyikan combobox kedua
            }
        }

        // Pasang event listener pada combobox pertama
        document.getElementById("tujuanrencana").addEventListener("change", toggleSecondComboBox);

        // Panggil fungsi saat halaman dimuat agar kondisi awal sesuai
        toggleSecondComboBox();

        $(function () {
            bsCustomFileInput.init();
            $('.jenistabel').select2({
                width: '100%',
                theme: 'bootstrap4',
                dropdownParent: $('#ajaxModel')
            })
            $('.pejabatpemakai').select2({
                width: '100%',
                theme: 'bootstrap4',
                dropdownParent: $('#ajaxModel')
            })
            $('.spesifikasi').select2({
                width: '100%',
                theme: 'bootstrap4',
                dropdownParent: $('#ajaxModel')

            })

            $('.idbagianpelaksana').select2({
                width: '100%',
                theme: 'bootstrap4',
                dropdownParent: $('#ajaxModel')
            })

            $('.program').select2({
                width: '100%',
                theme: 'bootstrap4',
                dropdownParent: $('#ajaxModel')
            })
            $('.jenissatker').select2({
                width: '100%',
                theme: 'bootstrap4',
                dropdownParent: $('#ajaxModel')

            })
            $('.skema').select2({
                width: '100%',
                theme: 'bootstrap4',
                dropdownParent: $('#ajaxModel')
            })
            $('.jeniskendaraan').select2({
                width: '100%',
                theme: 'bootstrap4',
                dropdownParent: $('#ajaxModel')

            })

            $('.kegiatan').select2({
                width: '100%',
                theme: 'bootstrap4',
                dropdownParent: $('#ajaxModel')
            })

            $('.output').select2({
                width: '100%',
                theme: 'bootstrap4',
                dropdownParent: $('#ajaxModel')
            })

            $('.kodebarang').select2({
                width: '100%',
                theme: 'bootstrap4',
                dropdownParent: $('#ajaxModel')
            })

            $('.tahunpengadaan').select2({
                width: '100%',
                theme: 'bootstrap4',
                dropdownParent: $('#ajaxModel')
            })
            $('.tujuanrencana').select2({
                width: '100%',
                theme: 'bootstrap4',
                dropdownParent: $('#ajaxModel')
            })
            $('.atrnonatr').select2({
                width: '100%',
                theme: 'bootstrap4',
                dropdownParent: $('#ajaxModel')
            })
            $('.jeniskantor').select2({
                width: '100%',
                theme: 'bootstrap4',
                dropdownParent: $('#ajaxModel')
            })
            $('.jenispengadaan').select2({
                width: '100%',
                theme: 'bootstrap4',
                dropdownParent: $('#ajaxModel')
            })

            $("input[data-bootstrap-switch]").each(function(){
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            })
            /*------------------------------------------
            --------------------------------------------
            Render DataTable
            --------------------------------------------
            --------------------------------------------*/
            // Setup - add a text input to each footer cell
            $('#tabelbagian tfoot th').each( function (i) {
                var title = $('#tabelbagian thead th').eq( $(this).index() ).text();
                $(this).html( '<input type="text" placeholder="'+title+'" data-index="'+i+'" />' ).css(
                    {"width":"5%"},
                );
            });
            var table = $('.tabelbagian').DataTable({
                fixedColumn:true,
                scrollX:"100%",
                autoWidth:true,
                processing: true,
                serverSide: true,
                dom: 'Bfrtip',
                buttons: ['copy','excel','pdf','csv','print'],
                ajax:"{{route('pengajuanrkbmn.index')}}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'idbagianpelaksana', name: 'idbagianpelaksana'},
                    {data: 'biropelaksana', name: 'biropelaksana'},
                    {data: 'tanggalpengajuan', name: 'tanggalpengajuan'},
                    {data: 'tanggalkebmn', name: 'tanggalkebmn'},
                    {data: 'tanggalkeperencanaan', name: 'tanggalkeperencanaan'},
                    {data: 'tanggalfinal', name: 'tanggalfinal'},
                    {data: 'kodebarang', name: 'kodebarang'},
                    {data: 'barangtersediadbr', name: 'barangtersediadbr'},
                    {data: 'quantitas', name: 'quantitas'},
                    {data: 'hargabarang', name: 'hargabarang'},
                    {data: 'totalanggaran', name: 'totalanggaran'},
                    {data: 'uraianbarang', name: 'uraianbarang'},
                    {data: 'status', name: 'status'},
                    {data: 'dokumenpendukung', name: 'dokumenpendukung'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: true,
                        searchable: true
                    },
                ],
                columnDefs: [
                    {
                        targets: 10,
                        render: $.fn.dataTable.render.number('.', ',', 0, '')
                    },
                    {
                        targets: 11,
                        render: $.fn.dataTable.render.number('.', ',', 0, '')
                    },
                ],
            });
            table.buttons().container()
                .appendTo( $('.col-sm-6:eq(0)', table.table().container() ) );
            // Filter event handler
            $( table.table().container() ).on( 'keyup', 'tfoot input', function () {
                table
                    .column( $(this).data('index') )
                    .search( this.value )
                    .draw();
            } );


            $('#kegiatan').on('change', function () {
                var  kegiatan = this.value;
                timeout = setTimeout(function () {
                    if(kegiatan){
                        $.ajax({
                            url: "{{url('ambildataoutput')}}",
                            type: "POST",
                            data: {
                                kodekegiatan: kegiatan,
                                _token: '{{csrf_token()}}'
                            },
                            dataType: 'json',
                            success: function (result) {
                                var output = document.getElementById('outputawal').value;
                                $('#output').html('<option value="">Pilih Output</option>');
                                $.each(result.output, function (key, value) {
                                    if (output === value.kodeoutput) {
                                        $('select[name="output"]').append('<option value="'+value.kodeoutput+'" selected>'+value.kodeoutput+" | "+value.deskripsi+'</option>').trigger('change')
                                    }else{
                                        $("#output").append('<option value="' + value.kodeoutput + '">' +value.kodeoutput+" | "+value.deskripsi+ '</option>');
                                    }

                                });
                            }

                        });
                    }else{
                        $('#output').val('');
                    }

                },500);
            });


            $('#tambahdata').click(function () {
                $('#saveBtn').val("tambah");
                $('#id').val('');
                $('#idbagianawal').val('');
                $('#idbagian').val('');
                $('#outputawal').val('');
                $('#dokumenpendukungawal').val('');
                $('#jenistabel').val("").trigger('change');
                $('#tujuanrencana').val("").trigger('change');
                $('#atrnonatr').val("").trigger('change');
                $('#jeniskantor').val("").trigger('change');
                $('#jenispengadaan').val("").trigger('change');
                $('#lokasi').val("");
                $('#luas').val("");
                $('#pejabatpemakai').val('').trigger('change');
                $('#spesifikasi').val('').trigger('change');
                $('#jenissatker').val("").trigger('change');
                $('#jeniskendaraan').val("").trigger('change');
                $('#skema').val("").trigger('change');
                $('#program').val("").trigger('change');
                $('#kegiatan').val("").trigger('change');
                $('#output').val("").trigger('change');
                $('#kodebarang').val("Pilih").trigger('change');
                $('#barangtersediadbr').val("");
                $('#barangtersediatotal').val("");
                $('#uraianbarang').val("");
                $('#keterangan').val("");
                $('#idbagianpelaksana').val('').trigger('change');
                $('#tahunanggaranpengusulan').val('').trigger('change');
                $('#quantitas').val("");
                $('#hargabarang').val("");
                $('#totalanggaran').val("");
                $('#formbagian').trigger("reset");
                $('#modelHeading').html("Tambah Pengajuan");
                $('#ajaxModel').modal('show');
            });

            $('body').on('click', '.kirimkepelaksana', function () {
                var id = $(this).data("id");
                if(confirm("Apakah Anda Yakin AKan Kirim Data Ini Ke Unit Kerja Pelaksana Pengadaan?")){
                    $.ajax({
                        type: "GET",
                        url: "{{ url('kirimkepelaksanapengadaan','') }}"+"/"+id,
                        success: function (data) {
                            if (data.status == "berhasil"){
                                Swal.fire({
                                    title: 'Sukses',
                                    text: 'Data Berhasil Dikirim Ke Unit Kerja Pelaksana Pengadaan',
                                    icon: 'success'
                                })
                            } else{
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Kirim Data Gagal, Data Tidak Ditemukan atau Sudah Tidak Berstatus Draft',
                                    icon: 'error'
                                })
                            }
                            $('#tabelbagian').DataTable().ajax.reload();
                        },
                        error: function (xhr, textStatus, errorThrown) {
                            if(xhr.responseJSON.errors){
                                var errorsArr = [];
                                $.each(xhr.responseJSON.errors, function(key,value) {
                                    errorsArr.push(value);
                                });
                                Swal.fire({
                                    title: 'Error!',
                                    text: errorsArr,
                                    icon: 'error'
                                })
                            }else{
                                var jsonValue = jQuery.parseJSON(xhr.responseText);
                                Swal.fire({
                                    title: 'Error!',
                                    text: jsonValue.message,
                                    icon: 'error'
                                })
                            }

                            $('#saveBtn').html('Simpan Data');
                        },
                    });
                }
            });

            /*------------------------------------------
            --------------------------------------------
            Click to Edit Button
            --------------------------------------------
            --------------------------------------------*/
            $('body').on('click', '.editpengajuan', function () {
                var id = $(this).data('id');
                $.get("{{ route('pengajuanrkbmn.index') }}" +'/' + id +'/edit', function (data) {
                    $('#modelHeading').html("Edit Referensi Bagian RK");
                    $('#saveBtn').val("edit");
                    $('#ajaxModel').modal('show');
                    $('#id').val(data.id);
                    $('#idbagianawal').val(data.idbagianpelaksana);
                    $('#outputawal').val(data.output);
                    $('#dokumenpendukungawal').val(data.dokumenpendukung);
                    $('#jenistabel').val(data.jenistabel).trigger('change');
                    $('#tujuanrencana').val(data.tujuan).trigger('change');
                    $('#atrnonatr').val(data.atrnonatr).trigger('change');
                    $('#jeniskantor').val(data.jeniskantor).trigger('change');
                    $('#jenispengadaan').val(data.jenispengadaan).trigger('change');
                    $('#lokasi').val(data.lokasi);
                    $('#luas').val(data.luas);
                    $('#pejabatpemakai').val(data.pejabatpemakai).trigger('change');
                    $('#spesifikasi').val(data.spesifikasi).trigger('change');
                    $('#jenissatker').val(data.jenissatker).trigger('change');
                    $('#jeniskendaraan').val(data.jeniskendaraan).trigger('change');
                    $('#skema').val(data.skema).trigger('change');
                    $('#program').val(data.program).trigger('change');
                    $('#kegiatan').val(data.kegiatan).trigger('change');
                    $('#output').val(data.output).trigger('change');
                    $('#kodebarang').val(data.kodebarang).trigger('change');
                    $('#barangtersediadbr').val(data.barangtersediadbr);
                    $('#barangtersediatotal').val(data.barangtersediatotal);
                    $('#uraianbarang').val(data.uraianbarang);
                    $('#keterangan').val(data.keterangan);
                    $('#idbagianpelaksana').val(data.idbagianpelaksana).trigger('change');
                    $('#tahunpengadaan').val(data.tahunanggaranpengusulan).trigger('change');
                    $('#quantitas').val(data.quantitas);
                    $('#hargabarang').val(data.hargabarang);
                    $('#totalanggaran').val(data.totalanggaran);
                    document.getElementById('aktuallinkbukti').href = "{{env('APP_URL')."/".asset('storage')}}"+"/"+data.dokumenpendukung
                    hitungtotalanggaran();
                })
            });

            /*------------------------------------------
            --------------------------------------------
            Create Product Code
            --------------------------------------------
            --------------------------------------------*/
            $('#saveBtn').click(function (e) {
                e.preventDefault();
                $(this).html('Sending..');
                let form = document.getElementById('formbagian');
                let fd = new FormData(form);
                let saveBtn = document.getElementById('saveBtn').value;
                var id = document.getElementById('id').value;
                fd.append('saveBtn',saveBtn)
                if(saveBtn == "edit"){
                    fd.append('_method','PUT')
                }
                for (var pair of fd.entries()) {
                    console.log(pair[0]+ ', ' + pair[1]);
                }

                $.ajax({
                    data: fd,
                    url: saveBtn === "tambah" ? "{{route('pengajuanrkbmn.store')}}":"{{route('pengajuanrkbmn.update','')}}"+'/'+id,
                    type: "POST",
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        if (data.status == "berhasil"){
                            Swal.fire({
                                title: 'Sukses',
                                text: 'Simpan Data Berhasil',
                                icon: 'success'
                            })
                        }else{
                            Swal.fire({
                                title: 'Error!',
                                text: 'Simpan Data Gagal',
                                icon: 'error'
                            })
                        }
                        $('#formbagian').trigger("reset");
                        $('#ajaxModel').modal('hide');
                        $('#saveBtn').html('Simpan Data');
                        table.draw();
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        if(xhr.responseJSON.errors){
                            var errorsArr = [];
                            $.each(xhr.responseJSON.errors, function(key,value) {
                                errorsArr.push(value);
                            });
                            Swal.fire({
                                title: 'Error!',
                                text: errorsArr,
                                icon: 'error'
                            })
                        }else{
                            var jsonValue = jQuery.parseJSON(xhr.responseText);
                            Swal.fire({
                                title: 'Error!',
                                text: jsonValue.message,
                                icon: 'error'
                            })
                        }

                        $('#saveBtn').html('Simpan Data');
                    },
                });
            });

            $('body').on('click', '.deletepengajuan', function () {

                var id = $(this).data("id");
                if(confirm("Apakah Anda Yakin AKan Hapus Data Ini!")){
                    $.ajax({
                        type: "DELETE",
                        url: "{{ route('pengajuanrkbmn.destroy','') }}"+'/'+id,
                        success: function (data) {
                            if (data.status == "berhasil"){
                                Swal.fire({
                                    title: 'Sukses',
                                    text: 'Data Berhasil Dihapus',
                                    icon: 'success'
                                })
                            }else{
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Hapus Data Gagal',
                                    icon: 'error'
                                })
                            }
                            table.draw();
                        },
                        error: function (xhr, textStatus, errorThrown) {
                            if(xhr.responseJSON.errors){
                                var errorsArr = [];
                                $.each(xhr.responseJSON.errors, function(key,value) {
                                    errorsArr.push(value);
                                });
                                Swal.fire({
                                    title: 'Error!',
                                    text: errorsArr,
                                    icon: 'error'
                                })
                            }else{
                                var jsonValue = jQuery.parseJSON(xhr.responseText);
                                Swal.fire({
                                    title: 'Error!',
                                    text: jsonValue.message,
                                    icon: 'error'
                                })
                            }

                            $('#saveBtn').html('Simpan Data');
                        },
                    });
                }
            });

            $('#kodebarang').on('change', function () {
                var kodebarang = this.value;
                // Jika kode barang dipilih, lakukan AJAX request

                timeout = setTimeout(function () {
                    if (kodebarang) {
                        $.ajax({
                            url: "{{url('ambildatabarangdalamdbr')}}",
                            type: "POST",
                            data: {
                                kodebarang: kodebarang,
                                _token: '{{csrf_token()}}'
                            },
                            dataType: 'json',
                            success: function (result) {
                                let barangtersediadbr = result[0].totalbarangdbr;
                                let barangtersediatotal = result[1].totalbarang;
                                $('#barangtersediadbr').val(barangtersediadbr);
                                $('#barangtersediatotal').val(barangtersediatotal);
                            }
                        });
                    } else {
                        $('#barangtersedia-container').hide();
                        $('#barangtersediadbr').val('');
                        $('#barangtersediatotal').val('');
                    }
                }, 500); // Delay 500ms
            });

            $('#hargabarang').on('input', function() {
                var hargabarang = $(this).val();

                // Hapus koma ribuan sebelum melakukan perhitungan
                hargabarang = removeThousandSeparator(hargabarang);
                hitungtotalanggaran();

                // Tambahkan koma ribuan setelah perhitungan
                $(this).val(addThousandSeparator(hargabarang));
            });

            $('#quantitas').on('input', function() {
                var quantitas = $(this).val();

                // Hapus koma ribuan sebelum melakukan perhitungan
                quantitas = removeThousandSeparator(quantitas);
                hitungtotalanggaran();

                // Tambahkan koma ribuan setelah perhitungan
                $(this).val(addThousandSeparator(quantitas));

            });

            function hitungtotalanggaran(){
                let quantitas = document.getElementById('quantitas').value;
                let hargabarang = document.getElementById('hargabarang').value;

                quantitas = removeThousandSeparator(quantitas);
                hargabarang = removeThousandSeparator(hargabarang);

                let totalanggaran = quantitas * hargabarang;
                document.getElementById('totalanggaran').value = addThousandSeparator(totalanggaran);
                document.getElementById('quantitas').value = addThousandSeparator(quantitas);
                document.getElementById('hargabarang').value = addThousandSeparator(hargabarang);
            }

            // Fungsi untuk menambahkan separator ribuan dengan koma
            function addThousandSeparator(number) {
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            // Fungsi untuk menghapus separator ribuan
            function removeThousandSeparator(number) {
                return number.replace(/,/g, '');
            }
        });

    </script>
@endsection
