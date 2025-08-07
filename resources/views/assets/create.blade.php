<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Tambah Aset Baru</h3>
                    <form method="POST" action="{{ route('assets.store') }}">
                        @csrf

                        <!-- Nama Aset -->
                        <div>
                            <x-input-label for="name" :value="__('Nama Aset')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                        </div>

                        <!-- Nomor Seri -->
                        <div class="mt-4">
                            <x-input-label for="serial_number" :value="__('Nomor Seri')" />
                            <x-text-input id="serial_number" class="block mt-1 w-full" type="text" name="serial_number" :value="old('serial_number')" required />
                        </div>

                        <!-- Kategori -->
                        <div class="mt-4">
                            <x-input-label for="category" :value="__('Kategori Aset')" />
                            <x-text-input id="category" class="block mt-1 w-full" type="text" name="category" :value="old('category')" required placeholder="Contoh: Baterai, Perangkat IT" />
                        </div>

                        <!-- Pilih Site -->
                        <div class="mt-4">
                            <x-input-label for="site_id" :value="__('Site Penempatan')" />
                            <select id="site_id" name="site_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" {{ $site_id ? 'disabled' : '' }}>
                                <option value="">-- Pilih Site --</option>
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}" @selected(old('site_id', $site_id) == $site->id)>
                                        {{ $site->name }}
                                    </option>
                                @endforeach
                            </select>
                            {{-- Jika site_id sudah ada, buat input hidden agar nilainya tetap terkirim --}}
                            @if($site_id)
                                <input type="hidden" name="site_id" value="{{ $site_id }}">
                            @endif
                        </div>

                        <!-- Status Awal -->
                        <div class="mt-4">
                            <x-input-label for="status" :value="__('Status Awal')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="In Use" @selected(old('status') == 'In Use')>In Use</option>
                                <option value="In Repair" @selected(old('status') == 'In Repair')>In Repair</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ url()->previous() }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <x-primary-button>
                                Tambah Aset
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
