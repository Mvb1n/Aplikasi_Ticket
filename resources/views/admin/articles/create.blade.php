<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-medium mb-4">Buat Artikel Baru</h3>
                <form method="POST" action="{{ route('admin.articles.store') }}">
                    @csrf
                    @include('admin.articles.partials.form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
