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
        @stack('scripts')
        @stack('styles')
    </head>
    <body class="font-sans antialiased">

        {{-- Inisialisasi Alpine.js dengan isSidebarOpen diatur ke true (terbuka penuh) --}}
        <div x-data="{ isSidebarOpen: true }" class="flex min-h-screen bg-gray-100">

            <!-- Sidebar -->
            <aside 
                class="bg-gray-800 text-gray-200 flex flex-col fixed inset-y-0 left-0 z-30 transition-all duration-300"
                :class="isSidebarOpen ? 'w-64' : 'w-20'"
            >
                <!-- Logo di Sidebar -->
                <div class="flex items-center h-16 flex-shrink-0 border-b border-gray-700" :class="isSidebarOpen ? 'justify-start px-4' : 'justify-center'">
                    <a href="{{ route('dashboard') }}">
                        <div class="flex items-center">
                            <img src="https://unsri.ac.id/images/lambang/0925253d-49e2-4a72-a0c7-ae70d33d24be.jpg" alt="Logo Aplikasi" class="h-10 w-10 rounded-full flex-shrink-0">

                            <span class="ml-3 font-semibold text-lg whitespace-nowrap overflow-hidden transition-all duration-200"
                                  :class="isSidebarOpen ? 'w-32 opacity-100' : 'w-0 opacity-0'">
                                Aplikasi Tiket
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Menu Navigasi di Sidebar -->
                {{-- Semua user bisa melihat laporan insiden --}}
                <nav class="flex-1 py-4 overflow-y-auto">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" :sidebar="true" x-bind:is-open="isSidebarOpen">
                        <x-slot name="icon">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        </x-slot>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('incidents.index')" :active="request()->routeIs('incidents.*')" :sidebar="true" x-bind:is-open="isSidebarOpen">
                         <x-slot name="icon">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path></svg>
                        </x-slot>
                        {{ __('Laporan Insiden') }}
                    </x-nav-link>
                    <x-nav-link :href="route('kb.index')" :active="request()->routeIs('kb.*')" :sidebar="true" x-bind:is-open="isSidebarOpen">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" /></svg>
                        </x-slot>
                        {{ __('Pusat Bantuan') }}
                    </x-nav-link>

                    {{-- Hanya admin dan security yang bisa melihat tiket problem --}}
                    @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('security'))
                    <x-nav-link :href="route('problems.index')" :active="request()->routeIs('problems.*')" :sidebar="true" x-bind:is-open="isSidebarOpen">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" /></svg>
                        </x-slot>
                            {{ __('Tiket Problem') }}
                    </x-nav-link>
                    <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')" :sidebar="true" x-bind:is-open="isSidebarOpen">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0 1 16.5 7.605" /></svg>
                        </x-slot>
                        {{ __('Laporan') }}
                    </x-nav-link>
                    @endif

                    {{-- Hanya admin yang bisa melihat manajemen aset --}}
                    @if(auth()->user()->hasRole('admin'))
                    <x-nav-link :href="route('sites.index')" :active="request()->routeIs('sites.*')" :sidebar="true" x-bind:is-open="isSidebarOpen">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" /></svg>
                        </x-slot>
                        {{ __('Manajemen Site') }}
                    </x-nav-link>
                    <x-nav-link :href="route('assets.index')" :active="request()->routeIs('assets.*')" :sidebar="true" x-bind:is-open="isSidebarOpen">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" /></svg>
                        </x-slot>
                        {{ __('Manajemen Asset') }}
                    </x-nav-link>
                    <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" :sidebar="true" x-bind:is-open="isSidebarOpen">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
                        </x-slot>
                        {{ __('Manajemen Pengguna') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.articles.index')" :active="request()->routeIs('admin.articles.*')" :sidebar="true" x-bind:is-open="isSidebarOpen">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                        </x-slot>
                        {{ __('Kelola Artikel') }}
                    </x-nav-link>
                    <x-nav-link :href="route('api-tokens.index')" :active="request()->routeIs('api-tokens.*')" :sidebar="true" x-bind:is-open="isSidebarOpen">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                        </x-slot>
                        {{ __('Kelola Token') }}
                    </x-nav-link>
                    @endif
                </nav>
            </aside>

            <!-- Konten Utama -->
            <div class="flex-1 flex flex-col transition-all duration-300" :class="isSidebarOpen ? 'lg:ml-64' : 'lg:ml-20'">
                <!-- Top Bar -->
                @include('layouts.navigation')

                <!-- Page Heading -->
                @if (isset($header))
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <!-- Page Content -->
                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>