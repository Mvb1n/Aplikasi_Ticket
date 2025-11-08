<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-semibold text-gray-800">Laporan Insiden</h3>
                <a href="{{ route('incidents.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Lapor Insiden Baru
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Site</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelapor</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    {{-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ditugaskan</th> --}}
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Aksi</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($incidents as $incident)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $incident->title }}</td>
                                        {{-- KOLOM BARU UNTUK MENAMPILKAN SITE --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $incident->site?->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $incident->user?->name ?? 'User Dihapus' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($incident->status == 'Open') bg-red-100 text-red-800 @endif
                                                @if($incident->status == 'In Progress') bg-yellow-100 text-yellow-800 @endif
                                                @if($incident->status == 'Resolved') bg-blue-100 text-blue-800 @endif
                                                @if($incident->status == 'Closed') bg-green-100 text-green-800 @endif
                                                @if($incident->status == 'Cancelled') bg-gray-100 text-gray-800-800 @endif
                                            ">
                                                {{ $incident->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $incident->created_at->format('d M Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-4">
                                            <a href="{{ route('incidents.show', $incident->id) }}" class="text-indigo-600 hover:text-indigo-900">Lihat Detail</a>
                                            {{-- Tombol Edit & Hapus hanya untuk Admin --}}
                                            @can('edit', $incident)
                                                <a href="{{ route('incidents.edit', $incident->id) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                            @endcan
                                            @can('delete', $incident)
                                            <form action="{{ route('incidents.cancel', $incident->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin membatalkan laporan ini? Status aset terkait akan dikembalikan.');" style="display:inline;">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Batalkan</button>
                                            </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Belum ada laporan insiden.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $incidents->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
