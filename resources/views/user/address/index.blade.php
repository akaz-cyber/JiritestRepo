@extends('user.layouts.master')
@section('title', 'Jirifarm || Address Page')
@section('main-content')

    <div class="card shadow mb-4">

        <div class="row">
            <div class="col-md-12">
                @include('backend.layouts.notification')
            </div>
        </div>

        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Daftar Alamat</h6>
            <a href="{{ route('addresses.create') }}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
                data-placement="bottom" title="Tambah Alamat">
                <i class="fas fa-plus"></i> Tambah Alamat
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-bordered" id="order-dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width:50px;">No</th>
                            <th>Label</th>
                            <th>Penerima & Kontak</th>
                            <th>Provinsi</th>
                            <th>Kota/Kabupaten</th>
                            <th>Kecamatan/Desa</th>
                            <th>Kode Pos</th>
                            <th>Alamat Lengkap</th>
                            <th>Status</th>
                            <th style="width:220px;">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($addresses as $key => $address)
                            <tr>
                                <td>{{ $addresses->firstItem() + $key }}</td>

                                <td>
                                    <b>{{ $address->label ?: 'Tanpa Label' }}</b>
                                    @if ($address->is_default)
                                        <span class="badge badge-success">Default</span>
                                    @endif
                                </td>

                                <td>
                                    {{ $address->first_name }} {{ $address->last_name }}<br>
                                    <small class="d-block">{{ $address->phone }} | {{ $address->email }}</small>
                                </td>

                                <td>{{ $address->province_name }}</td>
                                <td>{{ $address->city_name }}</td>
                                <td>{{ $address->district_name }} / {{ $address->sub_district_name }}</td>
                                <td>{{ $address->post_code }}</td>
                                <td>{{ $address->address1 }}</td>

                                <td>
                                    @if ($address->is_default)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">Tidak Aktif</span>
                                    @endif
                                </td>

                                {{-- 🔥 ACTION HANYA SATU KOLUM (DIPERBAIKI) --}}
                                <td>

                                    {{-- EDIT --}}
                                    <a href="{{ route('addresses.edit', Crypt::encryptString($address->id)) }}"
                                        class="btn btn-primary btn-sm">
                                        Edit
                                    </a>

                                    {{-- DELETE --}}
                                    <form method="POST"
                                        action="{{ route('addresses.destroy', Crypt::encryptString($address->id)) }}"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-danger btn-sm dltBtn">
                                            Hapus
                                        </button>
                                    </form>

                                    {{-- SET DEFAULT --}}
                                    @if (!$address->is_default)
                                        <form method="POST"
                                            action="{{ route('addresses.setDefault', Crypt::encryptString($address->id)) }}"
                                            class="set-default-form d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn btn-warning btn-sm">
                                                Jadikan Default
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge badge-success">Default</span>
                                    @endif

                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="10" class="text-center">
                                    <h6 class="text-center">Tidak Ada Alamat Yang Terdaftar !!!</h6>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>

                <span style="float:right">{{ $addresses->links() }}</span>

            </div>
        </div>
    </div>

@endsection


{{-- =========================== --}}
{{--           STYLE             --}}
{{-- =========================== --}}
@push('styles')
    <link href="{{ asset('backend/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />

    <style>
        #order-dataTable_filter,
        #order-dataTable_info {
            display: none;
        }
    </style>
@endpush


{{-- =========================== --}}
{{--           SCRIPT            --}}
{{-- =========================== --}}
@push('scripts')
    <script src="{{ asset('backend/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <script>
        // Init datatable
        $('#order-dataTable').DataTable({
            columnDefs: [{
                orderable: false,
                targets: [0, 9]
            }],
            paging: false,
            info: false,
            searching: false,
        });


        // DELETE SWEET ALERT
        $(document).on('click', '.dltBtn', function(e) {
            e.preventDefault();

            const form = $(this).closest('form');
            const url = form.attr('action');

            swal({
                title: "Yakin ingin menghapus?",
                text: "Setelah dihapus, data tidak dapat kembali!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {
                        $.post(url, {
                            _method: 'DELETE',
                            _token: $('meta[name="csrf-token"]').attr('content')
                        })
                        .done(() => location.reload())
                        .fail(() => swal("Gagal menghapus!", "", "error"));
                    }
                });
        });


        // SET DEFAULT SWEET ALERT (TANPA AJAX)
        $('.set-default-form').on('submit', function(e) {
            e.preventDefault();

            const form = this;

            swal({
                title: "Jadikan alamat ini Default?",
                text: "Alamat default sebelumnya akan dinonaktifkan.",
                icon: "info",
                buttons: true,
            })
                .then((confirm) => {
                    if (confirm) form.submit();
                });
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
@endpush
