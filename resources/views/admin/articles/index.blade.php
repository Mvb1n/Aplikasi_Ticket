<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">Kelola Artikel</h2>
                <a href="{{ route('admin.articles.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border rounded-md font-semibold text-xs text-white uppercase hover:bg-gray-700">
                    Tambah Artikel Baru
                </a>
            </div>

            @if (session('success'))
               <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg">
                   {{ session('success') }}
               </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
               <div class="p-6 text-gray-900">
                   <div class="overflow-x-auto">
                       <table class="min-w-full divide-y divide-gray-200">
                           <thead class="bg-gray-50">
                               <tr>
                                   <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                                   <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penulis</th>
                                   <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                   <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                   <th class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                               </tr>
                           </thead>
                           <tbody class="bg-white divide-y divide-gray-200">
                               @forelse ($articles as $article)
                                   <tr>
                                       <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $article->title }}</td>
                                       <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $article->user->name }}</td>
                                       <td class="px-6 py-4 whitespace-nowrap">
                                           <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $article->status == 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                               {{ ucfirst($article->status) }}
                                           </span>
                                       </td>
                                       <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $article->created_at->format('d M Y') }}</td>
                                       <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                           <a href="{{ route('admin.articles.edit', $article->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                           <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus artikel ini?');">
                                               @csrf
                                               @method('DELETE')
                                               <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                           </form>
                                       </td>
                                   </tr>
                               @empty
                                   <tr>
                                       <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada artikel yang dibuat.</td>
                                   </tr>
                               @endforelse
                           </tbody>
                       </table>
                   </div>
                   <div class="mt-4">
                       {{ $articles->links() }}
                   </div>
               </div>
            </div>
        </div>
    </div>
</x-app-layout>