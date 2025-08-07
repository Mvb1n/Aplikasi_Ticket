<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Header Halaman -->
            <div class="mb-6">
                <a href="{{ route('sites.show', $site->id) }}" class="text-sm text-indigo-600 hover:text-indigo-900 mb-2 inline-block">&larr; Kembali ke Detail {{ $site->name }}</a>
                <h2 class="text-3xl font-bold text-gray-800">Daftar {{ $category }}</h2>
                <p class="text-gray-600">Menampilkan aset dengan status: <span class="font-semibold">{{ str_replace('-', ' ', $status) }}</span></p>
            </div>

            <!-- Tabel Aset -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <th class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Aset</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Seri</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terakhir Diperbarui</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($assets as $asset)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->serial_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->updated_at->format('d M Y, H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @can('update', $asset)
                                                <a href="{{ route('assets.edit', $asset->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit Status</a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data aset yang cocok.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>