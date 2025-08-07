<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if($query)
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                    Hasil Pencarian untuk: <span class="text-indigo-600">"{{ $query }}"</span>
                </h2>
            @else
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                    Silakan masukkan kata kunci di bar pencarian di atas.
                </h2>
            @endif

            <!-- Hasil Pencarian Insiden -->
            @if($query)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">Ditemukan {{ $incidents->total() }} laporan insiden yang cocok</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Site</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($incidents as $incident)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $incident->title }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $incident->site?->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $incident->status }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('incidents.show', $incident->id) }}" class="text-indigo-600 hover:text-indigo-900">Lihat Detail</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada laporan insiden yang cocok.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{-- Menambahkan query string ke link paginasi --}}
                            {{ $incidents->appends(request()->input())->links() }}
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
