<x-app-layout>
    {{-- Tambahkan CSS untuk Peta Leaflet --}}
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Dashboard</h2>

            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('security'))
                {{-- DASHBOARD UNTUK ADMIN & SECURITY --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Kolom Kiri (Peta & Ringkasan) -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Peta Site -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium mb-4">Peta Lokasi Site</h3>
                                <div id="map" class="h-96 rounded-lg"></div>
                            </div>
                        </div>
                        <!-- Grafik Tren Bulanan -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium mb-4">Tren Insiden 6 Bulan Terakhir</h3>
                                <canvas id="monthlyIncidentChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan (Statistik & Papan Peringkat) -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Kartu Statistik -->
                        <div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-4">
                            <h3 class="text-lg font-medium">Ringkasan Sistem</h3>
                            <div class="flex justify-between items-center"><span class="text-gray-600">Insiden Terbuka</span><span class="font-bold text-red-600">{{ $stats['open_incidents'] }}</span></div>
                            <div class="flex justify-between items-center"><span class="text-gray-600">Problem Dianalisis</span><span class="font-bold text-yellow-600">{{ $stats['analysis_problems'] }}</span></div>
                            <div class="flex justify-between items-center"><span class="text-gray-600">Total Aset</span><span class="font-bold text-green-600">{{ $stats['total_assets'] }}</span></div>
                            <div class="flex justify-between items-center"><span class="text-gray-600">Total Site</span><span class="font-bold text-blue-600">{{ $stats['total_sites'] }}</span></div>
                        </div>
                        <!-- Papan Peringkat Site -->
                        <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                            <h3 class="text-lg font-medium mb-4">Top 5 Site Bermasalah</h3>
                            <ul class="space-y-3">
                                @foreach($topProblematicSites as $site)
                                <li class="flex items-center justify-between">
                                    <a href="{{ route('sites.show', $site->id) }}" class="text-sm font-medium text-indigo-600 hover:underline">{{ $site->name }}</a>
                                    <span class="text-sm font-bold text-gray-700">{{ $site->incidents_count }} insiden</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                </div>
            @else
                {{-- TAMPILAN DASHBOARD UNTUK STAFF BIASA --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium">Selamat datang, {{ Auth::user()->name }}!</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Gunakan sistem ini untuk melaporkan setiap insiden kehilangan aset yang Anda temukan.
                        </p>
                    </div>
                </div>

                <div class="mt-6">
                     <a href="{{ route('incidents.create') }}" class="block w-full text-center p-6 bg-indigo-600 border border-transparent rounded-lg shadow text-xl font-bold text-white hover:bg-indigo-700">
                        Lapor Insiden Baru
                    </a>
                </div>

            @endif

        </div>
    </div>

    {{-- Script untuk Peta & Grafik --}}
    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Inisialisasi Peta
                const sites = @json($sitesForMap);
                if (sites.length > 0) {
                    const map = L.map('map').setView([sites[0].latitude, sites[1].longitude], 9);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);
                    sites.forEach(site => {
                        L.marker([site.latitude, site.longitude]).addTo(map)
                            .bindPopup(`<b>${site.name}</b><br>${site.address}`);
                    });
                }

                // Inisialisasi Grafik
                const chartCtx = document.getElementById('monthlyIncidentChart').getContext('2d');
                const trendData = @json($monthlyIncidentTrend);
                const monthNames = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"];
                const labels = trendData.map(item => `${monthNames[item.month - 1]} ${item.year}`);
                const data = trendData.map(item => item.count);

                new Chart(chartCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Jumlah Insiden',
                            data: data,
                            backgroundColor: 'rgba(79, 70, 229, 0.8)',
                        }]
                    },
                    options: { scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
                });
            });
        </script>
    @endpush
</x-app-layout>