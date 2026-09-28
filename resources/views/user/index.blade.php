@extends('user.layouts.master')

@section('main-content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        </div>

        <div class="row">

            <div class="col-xl-4 col-md-6 mb-4">
                <a href="{{ route('addresses.index') }}" style="text-decoration: none;">
                    <div class="card border-left-primary shadow h-100 py-2" style="transition: transform .2s;">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Alamat Saya
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ \App\Models\Address::where('user_id', auth()->id())->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </div>
    <div class="card shadow mb-4">
        {{-- ... Notifikasi ... --}}
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Daftar Pesanan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if (count($orders) > 0)
                    {{-- Helper Format Rupiah --}}
                    @php
                        $formatRp = fn($n) => 'Rp' . number_format((float) $n, 0, ',', '.');
                    @endphp

                    <table class="table table-bordered" id="order-dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Order No.</th>
                                <th>tanggal</th>
                                <th style="min-width: 250px;">Produk Dipesan</th>
                                <th>Quantity</th>
                                <th>Total Berat</th>
                                <th>Ongkir</th>
                                <th>Total harga</th>
                                <th>Status Pesanan</th>
                                <th>Status Pembayaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $orders->firstItem() + $loop->index }}</td>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</td>

                                    {{-- KOLOM PRODUK DIPESAN --}}
                                    <td>
                                        @if ($order->trashed())
                                            {{-- Pesan jika Order dihapus (Soft Deleted) --}}
                                            <span class="text-danger" style="font-size: 0.9em; font-style: italic;">
                                                <i class="fas fa-exclamation-triangle"></i> Pesanan tidak tersedia. Silakan
                                                hubungi
                                                <a href="https://wa.me/628988199366?text=Halo%20Jirifarm,%20saya%20ingin%20bertanya%20mengenai%20pesanan%20saya%20dengan%20No%20Order:%20{{ $order->order_number }}%20karena%20berstatus%20tidak%20tersedia."
                                                    target="_blank" class="text-danger font-weight-bold"
                                                    style="text-decoration: underline;">
                                                    Admin
                                                </a>.
                                            </span>
                                        @else
                                            @if ($order->cart)
                                                <ul class="list-unstyled mb-0" style="font-size: 0.9em; padding-left: 0;">
                                                    @forelse ($order->cart as $item)
                                                        <li class="mb-1">
                                                            @if (($item->product && $item->product->trashed()) || ($item->variant && $item->variant->trashed()))
                                                                <div class="mb-2 mt-2">
                                                                    <span class="text-danger"
                                                                        style="font-size: 0.9em; font-style: italic;">
                                                                        <i class="fas fa-exclamation-triangle"></i> Produk
                                                                        tidak tersedia.
                                                                    </span>
                                                                </div>
                                                            @else
                                                                <div style="font-weight: 500;">
                                                                    {{ $item->product->title ?? 'N/A' }}
                                                                </div>
                                                                <div
                                                                    style="padding-left: 1em; text-indent: -0.7em; color: #666;">
                                                                    <span style="color: #888;">›&nbsp;</span>
                                                                    @if ($item->variant)
                                                                        {{ $item->variant->variant_name ?? '-' }}
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </li>
                                                    @empty
                                                        <li>-</li>
                                                    @endforelse
                                                </ul>
                                            @else
                                                -
                                            @endif
                                        @endif
                                    </td>

                                    <td>{{ $order->quantity }}x</td>

                                    <td>
                                        @php $weight = $order->total_weight_gram ?? 0; @endphp
                                        @if ($weight < 1000)
                                            <span style="white-space: nowrap;">{{ $weight }} gr</span>
                                        @else
                                            <span
                                                style="white-space: nowrap;">{{ number_format($weight / 1000, 2, ',', '.') }}
                                                kg</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($order->delivery_charge > 0)
                                            {{ $formatRp($order->delivery_charge) }}
                                        @elseif(strtolower($order->payment_method) == 'cod')
                                            Ambil di Tempat
                                        @else
                                            {{ $formatRp(0) }}
                                        @endif
                                    </td>

                                    <td>{{ $formatRp($order->total_amount) }}</td>

                                    {{-- KOLOM STATUS PESANAN --}}
                                    <td>
                                        @if ($order->trashed())
                                            <span class="badge badge-dark">Dihapus Admin</span>
                                        @elseif ($order->status == 'new')
                                            <span class="badge badge-info">Baru</span>
                                        @elseif($order->status == 'process')
                                            <span class="badge badge-warning">Diproses</span>
                                        @elseif($order->status == 'delivered')
                                            <span class="badge badge-success">Terkirim</span>
                                        @elseif($order->status == 'cancel')
                                            <span class="badge badge-danger">Dibatalkan</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($order->status) }}</span>
                                        @endif
                                    </td>

                                    {{-- KOLOM STATUS PEMBAYARAN (Tetap tampil normal sebagai bukti) --}}
                                    <td>
                                        @if ($order->payment_status == 'paid')
                                            <span class="badge badge-success">Lunas</span>
                                        @elseif($order->payment_status == 'unpaid')
                                            <span class="badge badge-danger">Belum Lunas</span>
                                        @else
                                            <span
                                                class="badge badge-secondary">{{ ucfirst($order->payment_status) }}</span>
                                        @endif
                                    </td>

                                    {{-- KOLOM AKSI --}}
                                    <td>
                                        @if ($order->trashed())
                                            <button type="button" class="btn btn-danger btn-sm btn-deleted-info"
                                                data-toggle="tooltip" title="Pesanan Tidak Tersedia" data-placement="bottom"
                                                data-order="{{ $order->order_number }}">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        @else
                                            @php
                                                $hasDeletedProduct = false;
                                                if ($order->cart) {
                                                    foreach ($order->cart as $item) {
                                                        if (
                                                            ($item->product && $item->product->trashed()) ||
                                                            ($item->variant && $item->variant->trashed())
                                                        ) {
                                                            $hasDeletedProduct = true;
                                                            break;
                                                        }
                                                    }
                                                }
                                            @endphp

                                            @if ($hasDeletedProduct)
                                                <button type="button" class="btn btn-danger btn-sm btn-deleted-info"
                                                    data-toggle="tooltip" title="Produk Tidak Tersedia"
                                                    data-placement="bottom" data-order="{{ $order->order_number }}">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                            @else
                                                <a href="{{ route('user.order.show', $order->id) }}"
                                                    class="btn btn-warning btn-sm" data-toggle="tooltip"
                                                    title="Lihat Detail" data-placement="bottom">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if ($order->payment_method == 'manual_transfer' && $order->payment_status == 'unpaid')
                                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                                        data-target="#bankTransferModal" title="Info Pembayaran"
                                                        data-placement="bottom"
                                                        data-total-amount="{{ $order->total_amount }}"
                                                        data-order-number="{{ $order->order_number }}">
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>
                                                @endif

                                                @if ($order->payment_method == 'cod' && $order->status != 'cancel')
                                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                                        data-target="#codModal" title="Info Pengambilan"
                                                        data-placement="bottom"
                                                        data-total-amount="{{ $order->total_amount }}"
                                                        data-order-number="{{ $order->order_number }}">
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <span style="float:right">{{ $orders->links() }}</span>
                @else
                    <h6 class="text-center">Belum ada pesanan. Yuk, mulai belanja!</h6>
                @endif
            </div>
        </div>
    </div>
    @include('frontend.layouts.bank_transfer_modal')
    @include('frontend.layouts.cod_modal')
@endsection

@push('styles')
    <link href="{{ asset('backend/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
    <style>
        div.dataTables_wrapper div.dataTables_paginate {
            display: none;
        }
    </style>
@endpush

@push('scripts')
    <!-- Page level plugins -->
    <script src="{{ asset('backend/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="{{ asset('backend/js/demo/datatables-demo.js') }}"></script>
    <script>
        $('#order-dataTable').DataTable({
            "columnDefs": [{
                "orderable": false,
                "targets": [8]
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
                var dataID = $(this).data('id');
                // alert(dataID);
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
            $('#bankTransferModal').on('show.bs.modal', function(event) {
                // 1. Dapatkan tombol yang di-klik
                var button = $(event.relatedTarget);

                // 2. Ekstrak data dari atribut data-*
                var totalAmount = button.data('total-amount');
                var orderNumber = button.data('order-number');

                // 3. Buat fungsi format Rupiah
                var formatRp = (n) => 'Rp' + new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(n);

                // 4. Dapatkan elemen modal
                var modal = $(this);
                var totalAmountEl = modal.find('#modal-total-amount');
                var waLinkEl = modal.find('#modal-user-wa-link'); // <-- Targetkan ID baru

                // 5. Update konten modal
                totalAmountEl.text(formatRp(totalAmount));

                // 6. Update link WA (ganti nomor WA jika perlu)
                var waNumber = "628988199366"; // Nomor dari modal
                var waText =
                    `Halo, saya ingin konfirmasi pembayaran untuk pesanan ${orderNumber} dengan total ${formatRp(totalAmount)}.`;
                var waLink =
                    `https://wa.me/${waNumber}?text=${encodeURIComponent(waText)}`;

                waLinkEl.attr('href', waLink);
            });
            $('#codModal').on('show.bs.modal', function(event) {
                // 1. Dapatkan tombol yang di-klik
                var button = $(event.relatedTarget);

                // 2. Ekstrak data dari atribut data-*
                // Jika dibuka dari tombol "Place Order" (checkout), data mungkin null disini,
                // tapi di checkout kita isi manual via JS.
                // Script ini khusus untuk tombol di Index.
                var totalAmount = button.data('total-amount');
                var orderNumber = button.data('order-number');

                // Cek jika data ada (artinya dibuka dari Index, bukan sisa data checkout)
                if (totalAmount && orderNumber) {
                    // 3. Format Rupiah
                    var formatRp = (n) => 'Rp' + new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(n);

                    // 4. Update konten modal
                    $(this).find('#cod-total-amount').text(formatRp(totalAmount));
                    $(this).find('#cod-order-number').text(orderNumber);

                    // 5. Update Link WA
                    var waNumber = "628988199366";
                    var waText =
                        `Halo Jirifarm, saya ingin konfirmasi pesanan COD dengan No Order: ${orderNumber}. Mohon info pengambilan.`;
                    var waLink = `https://wa.me/${waNumber}?text=${encodeURIComponent(waText)}`;

                    $(this).find('#cod-wa-link').attr('href', waLink);
                }
            });
            $('.btn-deleted-info').click(function(e) {
                e.preventDefault();

                // Ambil nomor order dari atribut data-order
                var orderNumber = $(this).data('order');

                // Siapkan link WhatsApp
                var waNumber = "628988199366";
                var waText =
                    `Halo Jirifarm, saya ingin bertanya mengenai pesanan saya dengan No Order: ${orderNumber} karena ada produk yang tidak tersedia.`;
                var waLink = `https://wa.me/${waNumber}?text=${encodeURIComponent(waText)}`;

                // Tampilkan SweetAlert
                swal({
                    title: "Produk Tidak Tersedia",
                    text: "Mohon maaf, ada produk dalam pesanan ini yang sudah tidak tersedia/dihapus. Silakan hubungi admin kami untuk informasi lebih lanjut.",
                    icon: "warning",
                    buttons: ["Tutup", "Hubungi WhatsApp"],
                    dangerMode: true,
                }).then((willContact) => {
                    // Jika user klik tombol "Hubungi WhatsApp"
                    if (willContact) {
                        window.open(waLink, '_blank');
                    }
                });
            });
        })
    </script>
@endpush
