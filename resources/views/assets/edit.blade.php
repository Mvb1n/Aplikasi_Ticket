<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Edit Aset: {{ $asset->name }}</h3>

                    {{-- TAMBAHKAN BLOK INI UNTUK MENAMPILKAN ERROR --}}
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Oops! Terjadi kesalahan.</strong>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('assets.update', $asset->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Nama Aset -->
                        <div>
                            <x-input-label for="name" :value="__('Nama Aset')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $asset->name)" required />
                        </div>

                        <!-- Nomor Seri (Tidak bisa diubah) -->
                        <div class="mt-4">
                            <x-input-label for="serial_number" :value="__('Nomor Seri (Tidak bisa diubah)')" />
                            <x-text-input type="text" name="serial_number" id="serial_number" required class="mt-1 block w-full bg-gray-100" :value="$asset->serial_number" readonly />
                        </div>

                        <div>
                            <x-input-label for="category" :value="__('Kategori')" />
                            <x-text-input type="text" name="category" id="category" required class="mt-1 block w-full" :value="old('category', $asset->category ?? 'Perangkat IT')" />
                        </div>

                        <!-- Pilih Site (Bisa diubah) -->
                        <div class="mt-4">
                            <x-input-label for="site_id" :value="__('Pilih Site Penempatan')" />
                            <select id="site_id" name="site_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}" @selected(old('site_id', $asset->site_id) == $site->id)>
                                        {{ $site->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="mt-4">
                            <x-input-label for="status" :value="__('Status Aset')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="In Use" @selected(old('status', $asset->status) == 'In Use')>In Use</option>
                                <option value="In Repair" @selected(old('status', $asset->status) == 'In Repair')>In Repair</option>
                                <option value="Stolen/Lost" @selected(old('status', $asset->status) == 'Stolen/Lost')>Stolen/Lost</option>
                                <option value="Decommissioned" @selected(old('status', $asset->status) == 'Decommissioned')>Decommissioned</option>
                            </select>
                        </div>

                        {{-- Anda bisa menambahkan field lain seperti description dan purchase_date di sini --}}

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ url()->previous() }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <x-primary-button>Simpan Perubahan</x-primary-button>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>