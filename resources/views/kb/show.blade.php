<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h1 class="text-3xl font-bold mb-2">{{ $article->title }}</h1>
                    <p class="text-sm text-gray-500 mb-6">Oleh {{ $article->user->name }} &bull; {{ $article->created_at->format('d F Y') }}</p>
                    <div class="prose max-w-none">
                        {!! nl2br(e($article->content)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>