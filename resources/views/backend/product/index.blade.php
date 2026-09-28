@extends('backend.layouts.master')
@section('title', 'jirifarm || Product Page')

@section('main-content')
    <div class="card shadow mb-4">
        <div class="row">
            <div class="col-md-12">
                @include('backend.layouts.notification')
            </div>
        </div>

        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Product List</h6>
            <a href="{{ route('product.create') }}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
                data-placement="bottom" title="Add Product">
                <i class="fas fa-plus"></i> Add Product
            </a>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('product.index') }}" class="d-flex justify-content-between mb-3">

                {{-- Bagian Kiri: Dropdown Per Page --}}
                <div class="form-inline">
                    <label class="mr-2">Show</label>
                    <select name="per_page" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <label>entries</label>
                </div>

                {{-- Bagian Kanan: Kolom Pencarian --}}
                <div class="form-inline">
                    <label class="mr-2">Search:</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="q" value="{{ $q }}" class="form-control"
                            placeholder="Cari produk atau SKU...">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                @if ($products->count())
                    <table class="table table-bordered" id="product-dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>S.N.</th>
                                <th>SKU</th>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Varian & Stok</th> {{-- Jumlah Item + daftar varian = stok --}}
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>S.N.</th>
                                <th>SKU</th>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Varian & Stok</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach ($products as $idx => $p)
                                @php
                                    $variants = $p->variants ?? collect();
                                    $skuList = $variants->pluck('sku')->filter()->values();
                                    $skuPreview = $skuList->take(2)->implode(', ');
                                    $skuMore = max(0, $skuList->count() - 2);

                                    $totalStock = (int) $variants->sum('stock');

                                    // Foto pertama
                                    $photo = '';
                                    if (!empty($p->photo)) {
                                        // 1. Ganti explode dari ',' menjadi '|'
                                        $parts = array_values(array_filter(array_map('trim', explode('|', $p->photo))));
                                        $photo = $parts[0] ?? '';
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $idx + 1 }}</td>

                                    {{-- SKU ringkas seperti di contoh Brand (gaya badge) --}}
                                    <td>
                                        @if ($variants->count() == 0)
                                            <span class="text-muted">-</span>
                                        @elseif($variants->count() == 1)
                                            <span class="badge badge-info">{{ $skuList->first() ?? '—' }}</span>
                                        @else
                                            @if ($skuPreview)
                                                <span class="badge badge-info">{{ $skuPreview }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                            @if ($skuMore > 0)
                                                <span class="badge badge-light">+{{ $skuMore }}</span>
                                            @endif
                                        @endif
                                    </td>

                                    {{-- Produk: gambar + nama + slug kecil --}}
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if ($photo)
                                                {{-- 2. Tambahkan asset('storage/') pada src --}}
                                                <img src="{{ asset('storage/' . $photo) }}" alt="thumb" class="zoom"
                                                    loading="lazy"
                                                    style="width:42px;height:42px;object-fit:cover;border-radius:6px;margin-right:10px;">
                                            @endif

                                            <div>
                                                <div class="font-weight-bold">{{ $p->title }}</div>
                                                <small class="text-muted">{{ $p->slug }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">{{ $p->cat_info->title ?? '-' }}</div>
                                        @if ($p->sub_cat_info)
                                            <small class="text-muted">
                                                <i class="fas fa-caret-right mr-1"></i>{{ $p->sub_cat_info->title }}
                                            </small>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($p->status == 'active')
                                            <span class="badge badge-success">{{ $p->status }}</span>
                                        @else
                                            <span class="badge badge-warning">{{ $p->status }}</span>
                                        @endif
                                    </td>

                                    {{-- Varian & Stok: Jumlah Item + list varian = stok --}}
                                    <td>
                                        <div class="mb-1">
                                            <span class="badge badge-primary">Jumlah Item: {{ $totalStock }}</span>
                                        </div>
                                        @if ($variants->count())
                                            <div class="border rounded p-2" style="max-height: 160px; overflow:auto;">
                                                <ul class="list-unstyled mb-0 small">
                                                    @foreach ($variants as $v)
                                                        <li class="d-flex justify-content-between">
                                                            <span>{{ $v->variant_name ?? '-' }}</span>
                                                            <strong class="ml-2">{{ (int) ($v->stock ?? 0) }}</strong>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @else
                                            <small class="text-muted">Belum ada varian</small>
                                        @endif
                                    </td>

                                    {{-- Action ala halaman Brand: Edit (bulat), Delete (bulat) --}}
                                    <td>
                                        <a href="{{ route('product.edit', $p->id) }}"
                                            class="btn btn-primary btn-sm float-left mr-1"
                                            style="height:30px; width:30px; border-radius:50%" data-toggle="tooltip"
                                            title="Edit" data-placement="bottom">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form method="POST" action="{{ route('product.destroy', [$p->id]) }}"
                                            class="float-left">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-danger btn-sm dltBtn" data-id={{ $p->id }}
                                                style="height:30px; width:30px; border-radius:50%" data-toggle="tooltip"
                                                data-placement="bottom" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end mt-3">
                        {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                    {{-- Ikuti gaya Brand: pakai DataTables, hide Laravel links --}}
                    {{-- <span style="float:right">{{ $products->links() }}

                    </span> --}}
                @else
                    <h6 class="text-center">No products found! Please create product</h6>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="{{ asset('backend/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
    <style>
        .zoom {
            transition: transform .2s;
        }

        .zoom:hover {
            transform: scale(3.2);
        }
    </style>
@endpush

@push('scripts')
    <!-- DataTables -->
    <script src="{{ asset('backend/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <!-- Inisialisasi DataTables (search & filter built-in) -->
    {{-- <script>
        $('#product-dataTable').DataTable({
            "columnDefs": [{
                    "orderable": false,
                    "targets": [5, 6]
                }
            ]
        });
    </script> --}}

    <!-- SweetAlert hapus (seperti di Brand) -->
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
                        title: "Are you sure?",
                        text: "Once deleted, you will not be able to recover this data!",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            form.submit();
                        } else {
                            swal("Your data is safe!");
                        }
                    });
            })
        });
    </script>
@endpush
