<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Edit Site: {{ $site->name }}</h3>
                    <form method="POST" action="{{ route('sites.update', $site->id) }}">
                        @csrf
                        @method('PUT')
                        @include('sites.partials.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>