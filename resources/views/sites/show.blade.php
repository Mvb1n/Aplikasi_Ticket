<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header Halaman -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <a href="{{ route('sites.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 mb-2 inline-block">&larr; Kembali ke Daftar Site</a>
                    <h2 class="text-3xl font-bold text-gray-800">{{ $site->name }}</h2>
                    <p class="text-gray-600">{{ $site->address }}</p>
                </div>
                <div>
                    {{-- Tombol untuk menambah aset baru ke site ini --}}
                    <a href="{{ route('assets.create', ['site_id' => $site->id]) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border rounded-md font-semibold text-xs text-white uppercase hover:bg-gray-700">
                        Tambah Aset ke Site Ini
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Daftar Aset per Kategori -->
            <div class="space-y-8">
                @forelse ($assetsByCategory as $category => $assets)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-700 mb-4">{{ $category }}</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Aset</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Seri</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            <th class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($assets as $asset)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->serial_number }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->status }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                                    <a href="{{ route('assets.edit', $asset->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                    <form action="{{ route('assets.destroy', $asset->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus aset ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                        Tidak ada data aset di site ini.
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
