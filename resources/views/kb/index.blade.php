<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Pusat Bantuan</h2>
            <div class="space-y-4">
                @forelse($articles as $article)
                    <a href="{{ route('kb.show', $article->slug) }}" class="block p-6 bg-white border rounded-lg shadow hover:bg-gray-50">
                        <h5 class="text-xl font-bold text-gray-900">{{ $article->title }}</h5>
                        <p class="text-sm text-gray-500">Dipublikasikan oleh {{ $article->user->name }} pada {{ $article->created_at->format('d M Y') }}</p>
                    </a>
                @empty
                    <p class="text-gray-500">Belum ada artikel yang dipublikasikan.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>