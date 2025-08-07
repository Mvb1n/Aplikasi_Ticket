<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Hanya tampilkan tombol ini untuk Admin --}}
            @if(auth()->user()->hasRole('admin'))
                {{-- <div class="flex justify-end mb-4"> --}}
                    <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-semibold text-gray-800">Manajemen Aset</h3>
                    <a href="{{ route('assets.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border rounded-md font-semibold text-xs text-white uppercase hover:bg-gray-700">
                        Tambah Aset Baru
                    </a>
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Daftar Master Aset</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Aset</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Seri</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="relative px-6 py-3"><span class="sr-only">Edit</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($assets as $asset)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $asset->serial_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($asset->status == 'In Use') bg-green-100 text-green-800 @endif
                                                @if($asset->status == 'Stolen/Lost') bg-red-100 text-red-800 @endif
                                                @if($asset->status == 'In Repair') bg-yellow-100 text-yellow-800 @endif
                                                @if($asset->status == 'Decommissioned') bg-gray-100 text-gray-800 @endif
                                            ">
                                                {{ $asset->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if(auth()->user()->hasRole('admin'))
                                                <a href="{{ route('assets.edit', $asset->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                <form action="{{ route('assets.destroy', $asset->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus aset ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $assets->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>