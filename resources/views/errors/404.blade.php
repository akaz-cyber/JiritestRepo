<!DOCTYPE html>
<html lang="en">

<head>

    @include('backend.layouts.head')
    @section('title', 'Jirifarm || ERROR')

</head>

<body>

    <div class="container-fluid">

        <div class="row" style="margin-top:10%">
            <!-- 404 Error Text -->
            <div class="col-md-12">
                <div class="text-center">
                    <div class="error mx-auto" data-text="404">404</div>
                    <p class="lead text-gray-800 mb-5">Maaf halaman tidak tersedia</p>
                    <a href="{{ route('home') }}">&larr; Kembali</a>

                </div>
            </div>
        </div>

    </div>


    @include('backend.layouts.footer')

</body>

</html>
