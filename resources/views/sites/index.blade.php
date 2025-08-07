<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-semibold text-gray-800">Manajemen Site</h3>
                @can('create', App\Models\Site::class)
                    <a href="{{ route('sites.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border rounded-md font-semibold text-xs text-white uppercase hover:bg-gray-700">
                        Tambah Site Baru
                    </a>
                @endcan
            </div>

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($sites as $site)
                    <div class="flex flex-col p-6 bg-white border border-gray-200 rounded-lg shadow">
                        <div class="flex-grow">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <a href="{{ route('sites.show', $site->id) }}" class="hover:underline">
                                        <h5 class="text-xl font-bold tracking-tight text-gray-900">{{ $site->name }}</h5>
                                    </a>
                                    <p class="font-normal text-gray-500 text-sm mb-1">Kode: {{ $site->location_code }}</p>
                                </div>
                                <!-- Tombol Aksi (Edit & Hapus) -->
                                <div class="flex space-x-2 flex-shrink-0">
                                    @can('update', $site)
                                        <a href="{{ route('sites.edit', $site->id) }}" class="text-blue-600 hover:text-blue-900 text-sm">Edit</a>
                                    @endcan
                                    @can('delete', $site)
                                        <form action="{{ route('sites.destroy', $site->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus site ini? Semua aset di dalamnya juga akan terhapus.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Hapus</button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                            <p class="font-normal text-gray-600 text-sm">{{ $site->address }}</p>
                        </div>
                        <div class="mt-4 pt-4 border-t">
                            <h6 class="text-sm font-semibold text-gray-600 mb-2">Ringkasan Aset</h6>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500">Total Aset</span>
                                    <span class="font-bold text-gray-800 px-2 py-1 bg-gray-100 rounded">{{ $site->assets_count }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-green-700">Digunakan</span>
                                    <span class="font-semibold text-green-700 px-2 py-1 bg-green-100 rounded">{{ $site->assets_in_use_count }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-red-700">Hilang/Dicuri</span>
                                    <span class="font-semibold text-red-700 px-2 py-1 bg-red-100 rounded">{{ $site->assets_stolen_lost_count }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-yellow-700">Dalam Perbaikan</span>
                                    <span class="font-semibold text-yellow-700 px-2 py-1 bg-yellow-100 rounded">{{ $site->assets_in_repair_count }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>