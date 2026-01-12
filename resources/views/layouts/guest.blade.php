<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Sistem Informasi SMP') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50/50">
        <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0">
            <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-xl ring-1 ring-gray-900/5 sm:rounded-xl relative overflow-hidden">
                 {{ $slot }}
            </div>
            
            <div class="mt-8 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} Sistem Informasi SMP. All rights reserved.
            </div>
        </div>
    </body>
</html>
