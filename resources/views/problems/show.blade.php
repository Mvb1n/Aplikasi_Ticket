<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Problem #') }}{{ $problem->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Kolom Detail Problem -->
            <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $problem->title }}</h3>
                    <p class="text-sm text-gray-600 mb-4">{{ $problem->description }}</p>

                    <div class="border-t border-gray-200 pt-4">
                        <h4 class="text-md font-medium text-gray-900 mb-2">Insiden Terkait</h4>
                        <ul class="list-disc list-inside">
                            @foreach($problem->incidents as $incident)
                                <li class="text-sm text-gray-700">
                                    <a href="{{ route('incidents.show', $incident->id) }}" class="text-indigo-600 hover:underline">
                                        #{{ $incident->id }} - {{ $incident->title }} (dilaporkan oleh {{ $incident->user->name }})
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

                {{-- Hanya tampilkan kolom ini untuk Admin atau Security --}}
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('security'))
                    <!-- Kolom Analisis & Aksi -->
                    <div class="md:col-span-1 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Analisis & Solusi</h3>

                    <form method="POST" action="{{ route('problems.update', $problem->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Analisis Akar Masalah -->
                        <div>
                            <x-input-label for="root_cause_analysis" :value="__('Analisis Akar Masalah (RCA)')" />
                            <textarea id="root_cause_analysis" name="root_cause_analysis" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('root_cause_analysis', $problem->root_cause_analysis) }}</textarea>
                        </div>

                        <!-- Solusi Permanen -->
                        <div class="mt-4">
                            <x-input-label for="permanent_solution" :value="__('Solusi Permanen')" />
                            <textarea id="permanent_solution" name="permanent_solution" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('permanent_solution', $problem->permanent_solution) }}</textarea>
                        </div>

                        <!-- Status -->
                        <div class="mt-4">
                            <x-input-label for="status" :value="__('Ubah Status')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="Analysis" @selected($problem->status == 'Analysis')>Analysis</option>
                                <option value="Solution Implemented" @selected($problem->status == 'Solution Implemented')>Solution Implemented</option>
                                <option value="Closed" @selected($problem->status == 'Closed')>Closed</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Update Problem') }}
                            </x-primary-button>
                        </div>
                    </form>
                        </div>
                    </div>
                @endif
        </div>

        </div>
    </div>
</x-app-layout>