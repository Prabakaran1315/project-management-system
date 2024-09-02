<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title')</title>
        <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="https://cdn.datatables.net/2.1.5/css/dataTables.dataTables.css" rel="stylesheet">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            [type=text]:focus,
            [type=search]:focus,
            input:where(:not([type])):focus,
            [type=email]:focus,
            [type=url]:focus,
            [type=password]:focus,
            [type=number]:focus,
            [type=date]:focus,
            [type=datetime-local]:focus,
            [type=month]:focus,
            [type=search]:focus,
            [type=tel]:focus,
            [type=time]:focus,
            [type=week]:focus,
            [multiple]:focus,
            textarea:focus,
            select:focus {
                border-color: rgb(55 65 81 / var(--tw-bg-opacity)) !important;
            }

            div.dt-container select.dt-input {
                width: 59px !important;
            }

            div.dt-container .dt-length label {
                display: none !important;
            }
            div.dt-container .dt-search input {
                border-color: rgb(55 65 81 / var(--tw-bg-opacity)) !important;
            }

            div.dt-processing > div:last-child > div {
                background: rgb(55 65 81 / var(--tw-bg-opacity)) !important;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
        <script src="https://cdn.datatables.net/2.1.5/js/dataTables.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="{{ asset('js/plugins/loader.js') }}"></script>
        <script src="{{ asset('js/plugins/toast.js') }}"></script>
        @yield('script')
    </body>
</html>
