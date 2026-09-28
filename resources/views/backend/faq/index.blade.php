@extends('backend.layouts.master')

@section('main-content')
    <div class="card shadow mb-4">
        <div class="row">
            <div class="col-md-12">
                @include('backend.layouts.notification')
            </div>
        </div>
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">FAQ</h6>
            <a href="{{ route('faq.create') }}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
                data-placement="bottom" title="Tambah FAQ"><i class="fas fa-plus"></i> Tambahkan FAQ
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="faq-dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pertanyaan</th>
                            <th>Jawaban</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Pertanyaan</th>
                            <th>Jawaban</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        @foreach ($faqs as $faq)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $faq->title }}</td>
                                <td>{{ $faq->content }}</td>
                                <td>
                                    @if ($faq->status == 'active')
                                        <span class="badge badge-success">{{ $faq->status }}</span>
                                    @else
                                        <span class="badge badge-warning">{{ $faq->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('faq.edit', $faq->id) }}"
                                        class="btn btn-primary btn-sm float-left mr-1"
                                        style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                        title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{ route('faq.destroy', [$faq->id]) }}">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm dltBtn" data-id={{ $faq->id }}
                                            style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                            data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <span style="float:right">
                    {{ $faqs->links() }}
                </span>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="{{ asset('backend/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" /> --}}
    <style>
        div.dataTables_wrapper div.dataTables_paginate {
            display: none;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('backend/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <script src="{{ asset('backend/js/demo/datatables-demo.js') }}"></script>
    <script>
        $('#faq-dataTable').DataTable({
            "columnDefs": [{
                "orderable": false,
                "targets": [3, 4] // Kolom Status dan Aksi tidak bisa disorting
            }]
        });
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('body').on('click', '.dltBtn', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var dataID = $(this).data('id');

                swal({
                        title: "Apakah Anda yakin?",
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            form.submit();
                        } else {
                            swal("Data aman!");
                        }
                    });
            });
        });
    </script>
@endpush
