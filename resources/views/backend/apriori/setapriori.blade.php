@extends('backend.layouts.master')

@section('main-content')
    <div class="container-fluid">

        <h1 class="h3 mb-2 text-gray-800">Clear dataset apriori</h1>
        {{-- <p class="mb-4">Data di bawah ini merupakan hasil generate algoritma Apriori untuk menentukan rekomendasi produk di Jirifarm.</p> --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Tabel dataset</h6>

                <div class="d-flex align-items-center">
                    <a href="{{ route('apriori.tambah') }}" class="btn btn-sm btn-success shadow-sm text-white mr-2">
                        <i class="fas fa-plus fa-sm text-white"></i> TAMBAH DATASET
                    </a>

                    <form action="{{ route('apriori.clean') }}" method="POST" class="m-0 p-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-info shadow-sm text-white">
                            <i class="fas fa-broom fa-sm text-white"></i> CLEAN DATASET
                        </button>
                    </form>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>OrderID</th>
                                <th>Product Variant ID</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>OrderID</th>
                                <th>Product Variant ID</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach ($clean_datasets as $key => $data)
                                <tr>
                                    {{-- Menampilkan nomor urut yang menyesuaikan dengan halaman pagination --}}
                                    <td>{{ $clean_datasets->firstItem() + $key }}</td>
                                    <td>{{ $data->OrderID }}</td>
                                    <td>{{ $data->product_variant_id }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $clean_datasets->links('pagination::bootstrap-4') }}
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
            // Memanggil tabel DataTables
            var table = $('#dataTable').DataTable();

            // Aksi saat Admin memilih nama Produk
            $('#filter-produk').on('change', function() {
                var searchVal = $(this).val();

                // Kembalikan kotak varian ke "Semua Varian" agar tidak bentrok
                $('#filter-varian').val('');

                // Cari kata tersebut di dalam tabel
                table.search(searchVal).draw();
            });

            // Aksi saat Admin memilih nama Varian
            $('#filter-varian').on('change', function() {
                var searchVal = $(this).val();

                // Kembalikan kotak produk ke "Semua Produk" agar tidak bentrok
                $('#filter-produk').val('');

                // Cari kata tersebut di dalam tabel
                table.search(searchVal).draw();
            });
        });
    </script>
@endpush
