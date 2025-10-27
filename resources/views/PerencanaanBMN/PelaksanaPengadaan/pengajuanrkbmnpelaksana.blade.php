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

                        </div>
                        <h3 class="card-title">{{$judul}}</h3>
                    </div>
                    <div class="card-body">
                        <table id="tabelbagian" class="table table-bordered table-striped tabelbagian">
                            <thead>
                                <tr>
                                    <th>Aksi</th>
                                    <th>Kode Pengajuan</th>
                                    <th>Status</th>
                                    <th>Tahun Anggaran</th>

                                    <th>Program</th>
                                    <th>Kegiatan</th>
                                    <th>Output</th>

                                    <th>Pelaksana Bagian</th>
                                    <th>Pelaksana Biro</th>
                                    <th>Pengusul Bagian</th>
                                    <th>Pengusul Biro</th>

                                    <th>Harga Barang</th>


                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Aksi</th>
                                    <th>Kode Pengajuan</th>
                                    <th>Status</th>
                                    <th>Tahun Anggaran</th>

                                    <th>Program</th>
                                    <th>Kegiatan</th>
                                    <th>Output</th>

                                    <th>Pelaksana Bagian</th>
                                    <th>Pelaksana Biro</th>
                                    <th>Pengusul Bagian</th>
                                    <th>Pengusul Biro</th>

                                    <th>Harga Barang</th>

                                </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>


    @include('PerencanaanBMN.PelaksanaPengadaan.ReviewModal')

    <!-- /.content -->
    <script src="{{env('APP_URL')."/".asset('AdminLTE/plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
    <script type="text/javascript">

        $(function () {

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
            const mapJenisPengajuan = data => {
                if (!data) return data;
                if (data.startsWith('R1')) return "Tanah dan/atau Bangunan Perkantoran";
                if (data.startsWith('R2')) return "Tanah dan/atau Bangunan Pendidikan";
                if (data.startsWith('R3')) return "Tanah dan/atau Bangunan Rumah Negara";
                if (data.startsWith('R4')) return "Kendaraan Jabatan";
                if (data.startsWith('R5')) return "Kendaraan Operasional";
                if (data.startsWith('R6')) return "Kendaraan Fungsional";
                return data;
            };

            const table = $('.tabelbagian').DataTable({
                fixedColumn:true,
                scrollX:"100%",
                autoWidth:true,
                processing: true,
                serverSide: true,
                dom: 'Bfrtip',
                buttons: ['copy','excel','pdf','csv','print'],
                ajax:"{{route('persetujuanusulanrkpelaksana')}}",
                columns: [
                    {
                        data: 'action',
                        name: 'action',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'kode_jenis_pengajuan',
                        name: 'kodejenispengajuan',
                        // render: (data) => mapJenisPengajuan(data)
                    },
                    {data: 'status', name: 'staus'},
                    {data: 'tahun_anggaran', name: 'tahunanggaran'},

                    {data: 'program', name: 'program'},
                    {data: 'kegiatan', name: 'kegiatan'},
                    {data: 'output', name: 'output'},

                    { data: 'id_bagian_pelaksana', name: 'idbagianpelaksana' },
                    { data: 'id_biro_pelaksana', name: 'idbiropelaksana' },
                    { data: 'id_bagian_pengusul', name: 'idbagianpelaksana' },
                    { data: 'id_biro_pengusul', name: 'idbiropelaksana' },

                    {data: 'harga_barang', name: 'hargabarang'},
                ],
                columnDefs: [
            {
                // Format harga barang menjadi Rupiah
                targets: 11,
                render: function(data, type, row, meta) {
                    if (type === 'display' || type === 'filter') {
                        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(data);
                    }
                    return data;
                }
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


    <script>
        var kirimUrl ="{{ url('kirimkekoordinatorpengadaan') }}"
        var baseUrl = "{{ url('pengajuanrkbmn') }}";  // Untuk Review dan Delete
    </script>



    <script src="resources\views\PerencanaanBMN\PelaksanaPengadaan\script\table.js"></script>
@endsection
