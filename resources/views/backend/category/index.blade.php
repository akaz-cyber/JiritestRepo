@extends('backend.layouts.master')

@section('main-content')
    <div class="card shadow mb-4">
        <div class="row">
            <div class="col-md-12">
                @include('backend.layouts.notification')
            </div>
        </div>

        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col-sm-6 col-12 mb-2 mb-sm-0">
                    <h6 class="m-0 font-weight-bold text-primary text-center text-sm-left">Category Lists</h6>
                </div>
                <div class="col-sm-6 col-12">
                    <div class="d-flex flex-column flex-sm-row justify-content-sm-end align-items-center">
                        <a href="#" class="btn btn-info btn-sm mb-2 mb-sm-0 mr-sm-2 w-100 w-sm-auto"
                            data-toggle="modal" data-target="#orderModal">
                            <i class="fas fa-list"></i> Atur List
                        </a>
                        <a href="{{ route('category.create') }}" class="btn btn-primary btn-sm w-100 w-sm-auto"
                            data-toggle="tooltip" data-placement="bottom" title="Add Category">
                            <i class="fas fa-plus"></i> Add Category
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                @if (count($categories) > 0)
                    <table class="table table-bordered" id="banner-dataTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Title</th>
                                <th class="d-none d-md-table-cell">Slug</th>
                                <th>Is Parent</th>
                                <th class="d-none d-sm-table-cell">Parent Category</th>
                                <th>Photo</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $categories->firstItem() + $loop->index }}</td>
                                    <td>{{ $category->title }}</td>
                                    <td class="d-none d-md-table-cell">{{ $category->slug }}</td>
                                    <td>{{ $category->is_parent == 1 ? 'Yes' : 'No' }}</td>
                                    <td class="d-none d-sm-table-cell">{{ $category->parent_info->title ?? '' }}</td>
                                    <td>
                                        @if ($category->photo)
                                            <img src="{{ asset('storage/' . $category->photo) }}" class="img-fluid rounded"
                                                style="max-width:50px" alt="{{ $category->title }}">
                                        @else
                                            <img src="{{ asset('backend/img/thumbnail-default.jpg') }}"
                                                class="img-fluid rounded" style="max-width:50px" alt="default">
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="badge {{ $category->status == 'active' ? 'badge-success' : 'badge-warning' }}">
                                            {{ $category->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <a href="{{ route('category.edit', $category->id) }}"
                                                class="btn btn-primary btn-sm mr-1" style="border-radius:50%"
                                                data-toggle="tooltip" title="edit"><i class="fas fa-edit"></i></a>
                                            <form method="POST" action="{{ route('category.destroy', [$category->id]) }}">
                                                @csrf
                                                @method('delete')
                                                <button class="btn btn-danger btn-sm dltBtn" data-id={{ $category->id }}
                                                    style="border-radius:50%" data-toggle="tooltip" title="Delete"><i
                                                        class="fas fa-trash-alt"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3">
                        <span class="float-right">{{ $categories->links() }}</span>
                    </div>
                @else
                    <h6 class="text-center">No Categories found!!!</h6>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('category.updateOrder') }}" method="POST" id="sortable-form">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="orderModalLabel">Atur Urutan Home</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="py-2 small">
                            <i class="fas fa-info-circle mr-1"></i> Tarik item untuk mengatur urutan tampilan di Home Page.
                        </div>
                        {{-- TAMBAHAN: Logika Kelipatan 3 --}}
                        <div class="form-group">
                            <label for="display_limit" class="font-weight-bold">Jumlah Tampil di Home:</label>
                            <select name="home_display_limit" id="display_limit" class="form-control">
                                @php
                                    // Hitung total kategori aktif & parent
                                    $totalActive = App\Models\Category::where('is_parent', 1)
                                        ->where('status', 'active')
                                        ->count();

                                    // Ambil settingan yang tersimpan (jika ada), default ke 6
                                    $currentLimit = \Illuminate\Support\Facades\Cache::get('home_category_limit', 6);
                                @endphp

                                {{-- Loop mulai dari 3, tambah 3 terus (3, 6, 9...), selama <= total kategori --}}
                                @for ($i = 3; $i <= $totalActive; $i += 3)
                                    <option value="{{ $i }}" {{ $currentLimit == $i ? 'selected' : '' }}>
                                        Tampilkan {{ $i }} Kategori
                                    </option>
                                @endfor

                                {{-- Opsi fallback jika total kurang dari 3 (jarang terjadi tapi buat jaga-jaga) --}}
                                @if ($totalActive < 3)
                                    <option value="3" selected>Minimal 3 (Data kurang)</option>
                                @endif
                            </select>
                            <small class="text-muted">Hanya menampilkan opsi kelipatan 3 sesuai jumlah kategori.</small>
                        </div>
                        {{-- menampilkan opsi kelipatan 3 --}}
                        <hr>
                        <label class="font-weight-bold">Atur Urutan:</label>
                        <ul class="list-group" id="sortable-list">
                            @foreach (App\Models\Category::where('is_parent', 1)->where('status', 'active')->orderBy('position', 'ASC')->get() as $cat)
                                <li class="list-group-item d-flex justify-content-between align-items-center"
                                    data-id="{{ $cat->id }}" style="cursor: move;">
                                    <span>
                                        <i class="fas fa-grip-vertical mr-2 text-muted"></i> {{ $cat->title }}
                                    </span>
                                    <input type="hidden" name="position[{{ $cat->id }}]" class="pos-input"
                                        value="{{ $cat->position }}">
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="{{ asset('backend/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
    <style>
        div.dataTables_wrapper div.dataTables_paginate {
            display: none;
        }

        #sortable-list .list-group-item {
            transition: background-color 0.2s;
        }

        #sortable-list .list-group-item:hover {
            background-color: #f8f9fa;
        }

        .sortable-ghost {
            opacity: 0.4;
            border: 2px dashed #4e73df !important;
        }
    </style>
@endpush

@push('scripts')
    <!-- Page level plugins -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="{{ asset('backend/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="{{ asset('backend/js/demo/datatables-demo.js') }}"></script>
    <script>
        $(document).ready(function() {
            var el = document.getElementById('sortable-list');

            // Inisialisasi Sortable
            var sortable = Sortable.create(el, {
                animation: 150,
                ghostClass: 'bg-light', // Efek warna saat item ditarik
                onEnd: function() {
                    // Fungsi ini dipicu saat item selesai dilepas
                    updateIndexes();
                }
            });

            // Fungsi untuk mengupdate value input hidden berdasarkan urutan baru
            function updateIndexes() {
                $('#sortable-list li').each(function(index) {
                    // Urutan dimulai dari 1 (index + 1)
                    $(this).find('.pos-input').val(index + 1);
                });
            }
        });
    </script>
    <script>
        $('#banner-dataTable').DataTable({
            "columnDefs": [{
                "orderable": false,
                "targets": [3, 4, 5]
            }]
        });

        // Sweet alert

        function deleteData(id) {

        }
    </script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('.dltBtn').click(function(e) {
                var form = $(this).closest('form');
                e.preventDefault();
                swal({
                        title: "Apakah Anda Yakin?",
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            form.submit();
                        }
                    });
            });
            @if (session('error'))
                swal({
                    title: "Gagal!",
                    text: "{{ session('error') }}",
                    icon: "error",
                    button: "OK Mengerti",
                });
            @endif
            @if (session('success'))
                swal({
                    title: "Berhasil!",
                    text: "{{ session('success') }}",
                    icon: "success",
                    button: "OK",
                });
            @endif
        });
    </script>
@endpush
