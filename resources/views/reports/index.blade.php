<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Laporan & Statistik Insiden</h2>

            <!-- Form Filter Tanggal -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.index') }}">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <div>
                                <x-input-label for="start_date" :value="__('Tanggal Mulai')" />
                                <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="$startDate" />
                            </div>
                            <div>
                                <x-input-label for="end_date" :value="__('Tanggal Selesai')" />
                                <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="$endDate" />
                            </div>
                            <div>
                                <x-primary-button>Terapkan Filter</x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Grafik Tren Insiden -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Tren Insiden Harian</h3>
                    <canvas id="incidentTrendChart"></canvas>
                </div>
            </div>

            <!-- Ringkasan per Site -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Ringkasan Insiden per Site</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Site</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Insiden</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($incidentsBySite as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->site?->name ?? 'Site Dihapus' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data untuk rentang tanggal ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- Script untuk Grafik (Chart.js) --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('incidentTrendChart').getContext('2d');
                const incidentData = @json($incidentTrend);

                const labels = incidentData.map(item => item.date);
                const data = incidentData.map(item => item.count);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Jumlah Insiden per Hari',
                            data: data,
                            borderColor: 'rgba(79, 70, 229, 1)',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            fill: true,
                            tension: 0.1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    // 3. Cara lebih modern & efisien untuk memastikan
                                    //    label sumbu Y hanya bilangan bulat (integer)
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>