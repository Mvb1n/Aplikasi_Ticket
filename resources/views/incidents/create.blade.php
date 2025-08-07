<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">
                        Formulir Laporan Insiden Baru
                    </h3>

                    <form method="POST" action="{{ route('incidents.store') }}">
                        @csrf

                        <!-- Judul Laporan -->
                        <div>
                            <x-input-label for="title" :value="__('Judul Laporan')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                        </div>

                        <!-- Pilih Site -->
                        <div class="mt-4">
                            <x-input-label for="site_id" :value="__('Lokasi Site Kejadian')" />
                            <select id="site_id" name="site_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">-- Pilih Site --</option>
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}" @selected(old('site_id') == $site->id)>
                                        {{ $site->name }} ({{ $site->location_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Lokasi Spesifik -->
                        <div class="mt-4">
                            <x-input-label for="location" :value="__('Lokasi Spesifik di Site')" />
                            <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location')" required placeholder="Contoh: Ruang Server, Lantai 2" />
                        </div>

                        <div class="mt-6 border-t pt-6">
                            <h4 class="text-md font-medium text-gray-800">Pilih Aset yang Hilang (Bisa lebih dari satu)</h4>
                            <!-- Daftar Aset akan muncul di sini secara dinamis -->
                            <div id="asset-list" class="mt-2 max-h-60 overflow-y-auto border border-gray-300 rounded-md p-4 space-y-2">
                                <p class="text-sm text-gray-500">Silakan pilih site terlebih dahulu untuk menampilkan daftar aset.</p>
                            </div>
                        </div>

                        <!-- Kronologi -->
                        <div class="mt-6 border-t pt-6">
                            <x-input-label for="chronology" :value="__('Kronologi Kejadian')" />
                            <textarea id="chronology" name="chronology" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('chronology') }}</textarea>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Kirim Laporan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JAVASCRIPT UNTUK DROPDOWN DINAMIS --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const siteSelect = document.getElementById('site_id');
            const assetListDiv = document.getElementById('asset-list');

            siteSelect.addEventListener('change', function() {
                const siteId = this.value;
                assetListDiv.innerHTML = '<p class="text-sm text-gray-500">Memuat aset...</p>'; // Tampilkan pesan loading

                if (!siteId) {
                    assetListDiv.innerHTML = '<p class="text-sm text-gray-500">Silakan pilih site terlebih dahulu.</p>';
                    return;
                }

                // Buat AJAX request menggunakan Fetch API
                fetch(`/api/sites/${siteId}/assets`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        assetListDiv.innerHTML = ''; // Kosongkan daftar aset
                        if (data.length > 0) {
                            data.forEach(asset => {
                                const label = document.createElement('label');
                                label.className = 'flex items-center';
                                label.innerHTML = `
                                    <input type="checkbox" name="asset_ids[]" value="${asset.id}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-600">${asset.name} (SN: ${asset.serial_number})</span>
                                `;
                                assetListDiv.appendChild(label);
                            });
                        } else {
                            assetListDiv.innerHTML = '<p class="text-sm text-gray-500">Tidak ada aset yang tersedia di site ini.</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching assets:', error);
                        assetListDiv.innerHTML = '<p class="text-sm text-red-500">Gagal memuat daftar aset. Periksa Console untuk detail.</p>';
                    });
            });
        });
    </script>
    @endpush
</x-app-layout>