{{-- resources/views/vendor/filament/components/layouts/app.blade.php --}}
@php use Filament\Support\Facades\FilamentView; @endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
<head>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- Sesuaikan jika pakai vite --}}
    @stack('head')
</head>


<body 
    class="filament-body min-h-screen bg-cover bg-center bg-no-repeat text-gray-900 antialiased"
    style="background-image: url('{{ asset('images/keruk.jpg') }}');"
>
    {{ $slot }}

    @livewireScripts
    @stack('scripts')
</body>
</html>
