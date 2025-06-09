<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-8">
                                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                                    {{ __('Dashboard') }}
                                </x-nav-link>
                                <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                                    {{ __('Products') }}
                                </x-nav-link>
                                <x-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.index')">
                                    {{ __('Cart') }}
                                </x-nav-link>
                                <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
                                    {{ __('Orders') }}
                                </x-nav-link>
                            </div>
                            <div class="flex items-center">
                                <!-- User dropdown or other items -->
                            </div>
                        </div>
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Scripts -->
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            console.log('Test script loaded');
        </script>
        @stack('scripts')
    </body>
</html>
