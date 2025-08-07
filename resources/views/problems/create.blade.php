<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Tiket Problem Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('problems.store') }}">
                        @csrf

                        <!-- Judul Problem -->
                        <div>
                            <x-input-label for="title" :value="__('Judul Problem')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                        </div>

                        <!-- Deskripsi Problem -->
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Deskripsi Singkat Problem')" />
                            <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('description') }}</textarea>
                        </div>

                        <!-- Pilih Insiden Terkait -->
                        <div class="mt-4">
                            <x-input-label :value="__('Pilih Insiden Terkait')" />
                            <div class="mt-2 max-h-60 overflow-y-auto border border-gray-300 rounded-md p-2">
                                @forelse ($incidents as $incident)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="incident_ids[]" value="{{ $incident->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-600">#{{ $incident->id }} - {{ $incident->title }}</span>
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-500">Tidak ada insiden aktif untuk ditautkan.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Buat Tiket') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
