@extends('backend.layouts.master')

@section('main-content')
    <div class="container-fluid">
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
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-2 text-gray-800">Aturan Asosiasi Apriori</h1>
            {{-- <p class="mb-4">Data di bawah ini merupakan hasil generate algoritma Apriori untuk menentukan rekomendasi produk di
        Jirifarm.</p> --}}
            <a href="{{ route('apriori.set') }}" class="btn btn-sm btn-info shadow-sm text-white mx-2">
                SET DATASET <i class="fas fa-database fa-sm text-white"></i>
            </a>
        </div>


        <div class="card shadow mb-4">

            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Tabel Association Rules</h6>
                <form action="{{ route('apriori.generate') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success shadow-sm text-white"
                        onclick="return confirm('Proses algoritma Apriori akan memakan waktu beberapa saat. Pastikan kamu sudah melakukan Clean Dataset. Lanjutkan?')">
                        <i class="fas fa-sync-alt fa-sm text-white"></i> SET APRIORI
                    </button>
                </form>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <form action="{{ route('apriori.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="view" class="font-weight-bold text-primary">Sortir Berdasarkan:</label>
                                <select name="view" id="view" class="form-control" onchange="this.form.submit()">
                                    <option value="variant" {{ $viewType == 'variant' ? 'selected' : '' }}>-- Tampilkan
                                        Semua Varian --</option>
                                    <option value="product" {{ $viewType == 'product' ? 'selected' : '' }}>-- Tampilkan
                                        Semua Produk --</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Antecedent</th>
                                <th>Consequent</th>
                                <th>Support</th>
                                <th>Confidence</th>
                                <th>Lift</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Jika Beli (Antecedent)</th>
                                <th>Maka Beli (Consequent)</th>
                                <th>Support</th>
                                <th>Confidence</th>
                                <th>Lift</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach ($rules as $key => $rule)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <strong>{{ $rule->antecedent_product }}</strong>
                                        @if ($viewType === 'variant' && $rule->antecedent_variant)
                                            <br><span class="badge badge-info">{{ $rule->antecedent_variant }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $rule->consequent_product }}</strong>
                                        @if ($viewType === 'variant' && $rule->consequent_variant)
                                            <br><span class="badge badge-success">{{ $rule->consequent_variant }}</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($rule->support, 4) }}</td>
                                    <td>{{ number_format($rule->confidence, 4) }}</td>
                                    <td>{{ number_format($rule->lift, 4) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
            $('#dataTable').DataTable();
        });
    </script>


    <script>
        $(document).ready(function() {
            // Memanggil tabel Apriori kamu
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
