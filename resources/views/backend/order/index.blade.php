@extends('backend.layouts.master')

@section('main-content')

    {{-- Helper Format Rupiah --}}
    @php
        $formatRp = fn($n) => 'Rp' . number_format((float) $n, 0, ',', '.');
    @endphp
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="row">
            <div class="col-md-12">
                @include('backend.layouts.notification')
            </div>
        </div>
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Order Lists</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if (count($orders) > 0)
                    <table class="table table-bordered" id="order-dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Order No.</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th style="min-width: 150px;">Produk Dipesan</th>
                                <th>Jumlah</th>
                                <th>Ongkir</th>
                                <th>Total Harga</th>
                                <th>Status Barang</th>
                                <th>Status Pembayaran</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Order No.</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th style="min-width: 150px;">Produk Dipesan</th>
                                <th>Jumlah</th>
                                <th>Ongkir</th>
                                <th>Total Harga</th>
                                <th>Status Barang</th>
                                <th>Status Pembayaran</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach ($orders as $order)
                                @php
                                    $shipping_charge = DB::table('shippings')
                                        ->where('id', $order->shipping_id)
                                        ->pluck('price');
                                @endphp
                                <tr>
                                    <td>{{ $orders->firstItem() + $loop->index }}</td>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->first_name }} {{ $order->last_name }}</td>
                                    <td>{{ $order->email }}</td>
                                    <td>
                                        @if ($order->cart)
                                            <ul class="list-unstyled mb-0" style="font-size: 0.9em; padding-left: 0;">
                                                @forelse ($order->cart as $item)
                                                    <li class="mb-1">
                                                        <div style="font-weight: 500;">
                                                            {{ $item->product->title ?? 'N/A' }}

                                                            {{-- Tambahkan indikator jika produk sudah dihapus --}}
                                                            @if ($item->product && $item->product->trashed())
                                                                <span class="badge badge-danger"
                                                                    style="font-size: 0.7em; margin-left: 5px;">Dihapus</span>
                                                            @endif
                                                        </div>

                                                        <div style="padding-left: 1em; text-indent: -0.7em; color: #666;">
                                                            <span style="color: #888;">›&nbsp;</span>
                                                            @if ($item->variant)
                                                                {{ $item->variant->variant_name ?? '-' }}
                                                            @endif
                                                        </div>
                                                    </li>
                                                @empty
                                                    <li>-</li>
                                                @endforelse
                                            </ul>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $order->quantity }}</td>
                                    <td>
                                        @if ($order->delivery_charge > 0)
                                            {{ $formatRp($order->delivery_charge, 2) }}
                                        @else
                                            Ambil di Tempat
                                        @endif
                                    </td>
                                    <td>{{ $formatRp($order->total_amount, 2) }}</td>
                                    <td>
                                        @if ($order->status == 'new')
                                            <span class="badge badge-primary">{{ $order->status }}</span>
                                        @elseif($order->status == 'process')
                                            <span class="badge badge-warning">{{ $order->status }}</span>
                                        @elseif($order->status == 'delivered')
                                            <span class="badge badge-success">{{ $order->status }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ $order->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->payment_status == 'paid')
                                            <span class="badge badge-success">{{ $order->payment_status }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ $order->payment_status }}</span>
                                        @endif
                                    <td>
                                        <a href="{{ route('order.show', $order->id) }}"
                                            class="btn btn-warning btn-sm float-left mr-1"
                                            style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                            title="view" data-placement="bottom"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('order.edit', $order->id) }}"
                                            class="btn btn-primary btn-sm float-left mr-1"
                                            style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                            title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                                        <form method="POST" action="{{ route('order.destroy', [$order->id]) }}">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-danger btn-sm dltBtn" data-id={{ $order->id }}
                                                style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                                data-placement="bottom" title="Delete"><i
                                                    class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <span style="float:right">{{ $orders->links() }}</span>
                @else
                    <h6 class="text-center">No orders found!!! Please order some products</h6>
                @endif
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
        })
    </script>
@endpush
