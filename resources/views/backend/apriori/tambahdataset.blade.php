@extends('backend.layouts.master')

@section('main-content')
    <div class="container-fluid">

        <h1 class="h3 mb-2 text-gray-800">Tambah Dataset apriori</h1>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Tabel dataset</h6>
                <button type="button" class="btn btn-sm btn-success shadow-sm text-white" data-toggle="modal"
                    data-target="#uploadModal">
                    <i class="fas fa-plus fa-sm text-white"></i> UPLOAD DATASET
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>OrderID</th>
                                <th>IndexID</th>
                                <th>ItemDescription</th>
                                <th>HargaSatuan</th>
                                <th>Qty</th>
                                <th>HargaTotal</th>
                                <th>Berat</th>
                                <th>BeratTotal</th>
                                <th>Lokasi</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>OrderID</th>
                                <th>IndexID</th>
                                <th>ItemDescription</th>
                                <th>HargaSatuan</th>
                                <th>Qty</th>
                                <th>HargaTotal</th>
                                <th>Berat</th>
                                <th>BeratTotal</th>
                                <th>Lokasi</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach ($datasets as $key => $data)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $data->order_id }}</td>
                                    <td>{{ $data->index_id }}</td>
                                    <td>{{ $data->item_description }}</td>
                                    <td>{{ $data->harga_satuan }}</td>
                                    <td>{{ $data->qty }}</td>
                                    <td>{{ $data->harga_total }}</td>
                                    <td>{{ $data->berat }}</td>
                                    <td>{{ $data->berat_total }}</td>
                                    <td>{{ $data->lokasi }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Upload File CSV Apriori</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('apriori.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Pilih File CSV</label>
                                <input type="file" name="file_csv" class="form-control" accept=".csv" required>
                                <small class="text-muted">Format yang didukung hanya .csv. Pastikan urutan kolom sesuai
                                    standar tabel.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="{{ asset('backend/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('backend/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables cukup sekali saja
            var table = $('#dataTable').DataTable();

            // Script filter produk dan varian (pastikan elemen HTML-nya ada jika ingin ini berfungsi)
            $('#filter-produk').on('change', function() {
                var searchVal = $(this).val();
                $('#filter-varian').val('');
                table.search(searchVal).draw();
            });

            $('#filter-varian').on('change', function() {
                var searchVal = $(this).val();
                $('#filter-produk').val('');
                table.search(searchVal).draw();
            });
        });
    </script>
@endpush
