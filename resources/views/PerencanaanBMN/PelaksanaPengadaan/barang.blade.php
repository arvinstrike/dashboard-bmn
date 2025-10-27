@extends('layouts.app')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        @if(session('status'))
                            <div class="alert alert-success">
                                {{session('status')}}
                            </div>
                        @endif
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
                <div class="row">
                </div>
                <div class="row">
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{$judul}}</h3>
                    </div>
                    <div class="card-body">
                        <table id="tabelbarang" class="table table-bordered table-striped tabelbarang">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>UAKPB</th>
                                <th>KD BRG</th>
                                <th>Uraian BRG</th>
                                <th>NUP</th>
                                <th>Merek/Type</th>
                                <th>Tgl Oleh</th>
                                <th>Tgl Catat</th>
                                <th>Kondisi</th>
                                <th>Intra/Ekstra</th>
                                <th>Nilai Aset</th>
                                <th>Status DBR</th>
                                <th>Foto</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>No</th>
                                <th>UAKPB</th>
                                <th>KD BRG</th>
                                <th>Uraian BRG</th>
                                <th>NUP</th>
                                <th>Merek/Type</th>
                                <th>Tgl Oleh</th>
                                <th>Tgl Catat</th>
                                <th>Kondisi</th>
                                <th>Intra/Ekstra</th>
                                <th>Nilai Aset</th>
                                <th>Status DBR</th>
                                <th>Foto</th>
                                <th>Aksi</th>
                            </tr>
                            </tfoot>
                        </table>
                        <div class="modal fade" id="ajaxModelRencanaPemeliharaan" aria-hidden="true" data-focus="false">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="modelHeading"></h4>
                                    </div>
                                    <div class="modal-body">
                                        <form id="formrencanapemeliharaan" name="formrencanapemeliharaan" class="form-horizontal">
                                            <input type="hidden" name="idbagianawal" id="idbagianawal">
                                            <input type="hidden" name="id" id="id">
                                            <input type="hidden" name="dokumenpendukungawal" id="dokumenpendukungawal">
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <label for="deputi" class="control-label">Jenis Tabel</label>
                                                    <select class="form-control jenistabel" name="jenistabel" id="jenistabel" style="width: 100%;">
                                                        <option value="">Pilih Jenis Tabel</option>
                                                        <option value="R5">Pemeliharaan Gedung dan Bangunan</option>
                                                        <option value="R6">Pemeliharaan Kendaraan</option>
                                                        <option value="R7">Pemeliharaan PM TIK dan Non TIK</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <fieldset id="informasiUmum">
                                                <legend>Informasi Umum</legend>
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <label for="deputi" class="control-label">Kode Barang</label>
                                                        <input type="text" class="form-control kodebarang" name="kodebarang" id="kodebarang" style="width: 100%;" readonly>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label for="nilaibukti" class="control-label">Uraian Barang</label>
                                                        <div class="input-group mb-3">
                                                            <input type="text" class="form-control uraianbarang" id="uraianbarang" name="uraianbarang" placeholder="Uraian Barang" value="" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <label for="nilaibukti" class="control-label">NUP</label>
                                                        <div class="input-group mb-3">
                                                            <input type="text" class="form-control noaset" id="noaset" name="noaset" placeholder="Uraian Barang" value="" required="" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label for="keterangan" class="control-label">Tanggal Perolehan</label>
                                                        <div class="input-group mb-3">
                                                            <input type="text" class="form-control" id="tanggalperolehan" name="tanggalperolehan" placeholder="Tanggal Perolehan" value="" required="" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <label for="keterangan" class="control-label">Merek/Tipe</label>
                                                        <div class="input-group mb-3">
                                                            <input type="text" class="form-control" id="merktype" name="merktype" placeholder="Merk/Type" value="" required="" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label for="keterangan" class="control-label">Kondisi</label>
                                                        <div class="input-group mb-3">
                                                            <textarea class="form-control" id="kondisi" name="kondisi" placeholder="Kondisi" value="" required="" readonly></textarea>
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
                                                        <label for="deputi" class="control-label">Usulan Tahun Pemeliharaan</label>
                                                        <select class="form-control tahunpemeliharaan" name="tahunpemeliharaan" id="tahunpemeliharaan" style="width: 100%;">
                                                            <option value="">Pilih Tahun Pemeliharaan</option>
                                                            @foreach($datatahunanggaran as $data)
                                                                <option value="{{ $data }}">{{ $data }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
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
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <label for="keterangan" class="control-label">Daftar Hasil Pemeliharaan</label>
                                                        <div class="input-group mb-3">
                                                            <input type="text" class="form-control" id="daftarhasilpemeliharaan" name="daftarhasilpemeliharaan" placeholder="Daftar Hasil Pemeliharaan" value="" required="">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label for="keterangan" class="control-label">Keterangan</label>
                                                        <div class="input-group mb-3">
                                                            <input  type="text" class="form-control" id="keterangan" name="keterangan" placeholder="Keterangan" value="" required="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                            <!--
                                            form khusus untuk tanah
                                            -->
                                            <fieldset id="tanahFieldset">
                                                <legend>Informasi Pengadaan Tanah/Gedung</legend>
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <label for="nilaibukti" class="control-label">Luas</label>
                                                        <div class="input-group mb-3">
                                                            <input type="text" class="form-control luas" id="luas" name="luas" placeholder="Luas" value="" required="">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label for="nilaibukti" class="control-label">Luas Tapak</label>
                                                        <div class="input-group mb-3">
                                                            <input type="text" class="form-control luastapak" id="luastapak" name="luastapak" placeholder="Luas Tapak" value="" required="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <label for="nilaibukti" class="control-label">Luas Pemanfaatan</label>
                                                        <div class="input-group mb-3">
                                                            <input type="text" class="form-control luaspemanfaatan" id="luaspemanfaatan" name="luaspemanfaatan" placeholder="Luas Pemanfaatan" value="" required="">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label for="nilaibukti" class="control-label">Luas Riil</label>
                                                        <div class="input-group mb-3">
                                                            <input type="text" class="form-control luasriil" id="luasriil" name="luasriil" placeholder="Luas Riil" value="" required="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <label for="nilaibukti" class="control-label">Luas Pemeliharaan Satker Lain</label>
                                                        <div class="input-group mb-3">
                                                            <input type="text" class="form-control luaspemeliharaansatkerlain" id="luaspemeliharaansatkerlain" name="luaspemeliharaansatkerlain" placeholder="Luas Pemeliharaan Satker Lain" value="" required="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                            <div class="col-sm-offset-2 col-sm-10">
                                                <div class="btn-group" role="group">
                                                    <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Simpan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="ajaxModel" aria-hidden="true" tabindex="-1" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="modelHeading">Upload Foto BMN</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('uploadfotobarang') }}" method="POST" id="formuploadfotobmn" name="formuploadfotobmn" class="form-horizontal" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="idbarang" id="idbarang">
                                            <div class="form-group">
                                                <label for="fotobmn" class="col-form-label">Upload Foto BMN</label>
                                                <div class="custom-file">
                                                    <input type="file" accept=".jpg,.jpeg,.png,.webp" class="custom-file-input" id="fotobmn" name="fotobmn[]" multiple>
                                                    <label class="custom-file-label" for="fotobmn">Choose file</label>
                                                </div>
                                            </div>
                                            <div class="form-group">
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
    <script type="text/javascript">
        $(document).ready(function() {
            // Sembunyikan semua fieldset pada awalnya
            $('#tanahFieldset').hide();

            // Tangani perubahan pada dropdown 'jenistabel'
            $('#jenistabel').change(function() {
                var selectedValue = $(this).val();

                // Sembunyikan semua fieldset terlebih dahulu
                $('#tanahFieldset').hide();

                // Tampilkan fieldset yang sesuai dengan pilihan pengguna
                if (selectedValue === 'R5') {
                    $('#tanahFieldset').show();
                }
            });
        });

        $(function () {
            $('.jenistabel').select2({
                width: '100%',
                theme: 'bootstrap4',
                dropdownParent: $('#ajaxModelRencanaPemeliharaan')
            })
            $('.idbagianpelaksana').select2({
                width: '100%',
                theme: 'bootstrap4',
                dropdownParent: $('#ajaxModelRencanaPemeliharaan')
            })
            $('.tahunpemeliharaan').select2({
                width: '100%',
                theme: 'bootstrap4',
                dropdownParent: $('#ajaxModelRencanaPemeliharaan')
            })
            $('.custom-file-input').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });

            $('#tabelbarang tfoot th').each( function (i) {
                var title = $('#tabelbarang thead th').eq( $(this).index() ).text();
                $(this).html( '<input type="text" placeholder="'+title+'" data-index="'+i+'" />' ).css(
                    {"width":"5%"},
                );
            });
            var table = $('.tabelbarang').DataTable({
                lengthAdjust: true,
                fixedColumn:true,
                scrollX:"100%",
                autoWidth:true,
                processing: true,
                serverSide: true,
                dom: 'lf<"floatright"B>rtip',
                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5'
                ],
                ajax:"{{route('getdatabarangpelaksana')}}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'kd_lokasi', name: 'kd_lokasi'},
                    {data: 'kd_brg', name: 'kd_brg'},
                    {data: 'ur_sskel', name: 'kodebarangrelation.ur_sskel'},
                    {data: 'no_aset', name: 'no_aset'},
                    {data: 'merk_type', name: 'merk_type'},
                    {data: 'tgl_perlh', name: 'tgl_perlh'},
                    {data: 'tgl_buku', name: 'tgl_buku'},
                    {data: 'kondisi', name: 'kondisi'},
                    {data: 'flag_sap', name: 'flag_sap'},
                    {data: 'rph_aset', name: 'rph_aset'},
                    {data: 'statusdbr', name: 'statusdbr'},
                    {data: 'foto', name: 'foto'},
                    {data: 'action', name: 'action'},
                ],
            });
            table.buttons().container()
                .appendTo( $('.col-sm-6:eq(0)', table.table().container() ) );
            // Filter event handler
            $( table.table().container() ).on( 'keypress', 'tfoot input', function (e) {
                if (e.key === "Enter" || e.key === "Unidentified" || e.keycode === 229){
                    table
                        .column( $(this).data('index') )
                        .search( this.value )
                        .draw();
                }
            } );

            $('body').on('click', '.uploadfoto', function () {
                var idbarang = $(this).data("id");
                $('#idbarang').val(idbarang);
                $('#modelHeading').html("Upload Foto BMN");
                $('#ajaxModel').modal('show');
            });

            $('body').on('click', '.ajukanpemeliharaan', function () {
                var id = $(this).data('id');
                $.get("{{ route('dapatkandatabarang','')}}" +'/' + id , function (data) {
                    $('#modelHeading').html("Pengajuan Rencana Pemeliharaan");
                    $('#ajaxModelRencanaPemeliharaan').modal('show');
                    $('#saveBtn').val("edit");
                    $('#idbarang').val(data.id);
                    $('#idbagianawal').val('');
                    $('#dokumenpendukungawal').val('');
                    $('#jenistabel').val('').trigger('change');
                    $('#luas').val('');
                    $('#kodebarang').val(data.kd_brg);
                    $('#uraianbarang').val(data.kodebarangrelation['ur_sskel']);
                    $('#noaset').val(data.no_aset);
                    $('#tanggalperolehan').val(data.tgl_perlh);
                    $('#merktype').val(data.merk_type);
                    $('#kondisi').val(data.kondisi);
                    $('#idbagianpelaksana').val('').trigger('change');
                    $('#tahunpemeliharaan').val('').trigger('change');
                    $('#daftarhasilpemeliharaan').val('');
                    $('#keterangan').val('');
                })
            });

            $('#formuploadfotobmn').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                $.ajax({
                    url: '{{ route("uploadfotobarang") }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if(response.success) {
                            $('#ajaxModel').modal('hide');
                            alert('Foto berhasil diunggah!');
                            table.ajax.reload(); // Reload DataTable setelah upload berhasil
                        } else {
                            alert('Gagal mengunggah foto.');
                        }
                    },
                    error: function(response) {
                        alert('Terjadi kesalahan saat mengunggah foto.');
                    }
                });
            });

            $('body').on('click', '.download-photo', function (e) {
                e.preventDefault();
                var path = $(this).data('path');
                var idbarang = $(this).data('idbarang');

                $.ajax({
                    url: '{{ route("downloadfotobarang") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        path: path,
                        idbarang: idbarang
                    },
                    xhrFields: {
                        responseType: 'blob' // Agar respons diterima sebagai Blob untuk file download
                    },
                    success: function(data) {
                        var a = document.createElement('a');
                        var url = window.URL.createObjectURL(data);
                        a.href = url;
                        a.download = path.split('/').pop(); // Mengambil nama file dari path
                        document.body.append(a);
                        a.click();
                        a.remove();
                        window.URL.revokeObjectURL(url);
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat mengunduh foto.');
                    }
                });
            });

            $('body').on('click', '.delete-photo', function (e) {
                e.preventDefault();
                var path = $(this).data('path');
                var idbarang = $(this).data('idbarang');
                var button = $(this);
                console.log("ID Barang: ", idbarang); // Menampilkan idbarang di console
                console.log("Path: ", path); // Menampilkan path di console

                if (confirm('Apakah Anda yakin ingin menghapus foto ini?')) {
                    $.ajax({
                        url: '{{ route("hapusfotobarang") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            path: path,
                            idbarang: idbarang
                        },
                        success: function(response) {
                            if (response.success) {
                                button.closest('.img-wrapper').remove();
                                alert('Foto berhasil dihapus!');
                                table.ajax.reload();
                            } else {
                                alert('Gagal menghapus foto.');
                            }
                        },
                        error: function() {
                            alert('Terjadi kesalahan saat menghapus foto.');
                        }
                    });
                }
            });
        });

    </script>
@endsection
