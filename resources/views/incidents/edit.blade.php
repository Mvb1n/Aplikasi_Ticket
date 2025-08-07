<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Edit Laporan Insiden #{{ $incident->id }}</h3>

                    <form method="POST" action="{{ route('incidents.update', $incident->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Judul Laporan -->
                        <div>
                            <x-input-label for="title" :value="__('Judul Laporan')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $incident->title)" required />
                        </div>

                        <!-- Pilih Site -->
                        <div class="mt-4">
                            <x-input-label for="site_id" :value="__('Lokasi Site Kejadian')" />
                            <select id="site_id" name="site_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}" @selected(old('site_id', $incident->site_id) == $site->id)>
                                        {{ $site->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Lokasi Spesifik -->
                        <div class="mt-4">
                            <x-input-label for="location" :value="__('Lokasi Spesifik di Site')" />
                            <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location', $incident->location)" required />
                        </div>

                        <!-- Daftar Aset Dinamis -->
                        <div class="mt-6 border-t pt-6">
                            <h4 class="text-md font-medium text-gray-800">Pilih Ulang Aset yang Terlibat</h4>
                            <div id="asset-list" class="mt-2 max-h-60 overflow-y-auto border border-gray-300 rounded-md p-4 space-y-2">
                                <p class="text-sm text-gray-500">Memuat aset...</p>
                            </div>
                        </div>

                        {{-- Kita sembunyikan field lain yang tidak boleh diedit di sini --}}
                        <input type="hidden" name="status" value="{{ $incident->status }}">

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('incidents.show', $incident->id) }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <x-primary-button>Simpan Perubahan</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const siteSelect = document.getElementById('site_id');
            const assetListDiv = document.getElementById('asset-list');
            // Simpan ID aset yang saat ini terhubung dengan insiden
            const currentAssetIds = @json($incident->assets->pluck('id'));

            function fetchAssets(siteId) {
                assetListDiv.innerHTML = '<p class="text-sm text-gray-500">Memuat aset...</p>';

                fetch(`/get-assets-by-site/${siteId}`)
                    .then(response => response.json())
                    .then(data => {
                        assetListDiv.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(asset => {
                                const isChecked = currentAssetIds.includes(asset.id) ? 'checked' : '';
                                const label = document.createElement('label');
                                label.className = 'flex items-center';
                                label.innerHTML = `
                                    <input type="checkbox" name="asset_ids[]" value="${asset.id}" ${isChecked} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-600">${asset.name} (SN: ${asset.serial_number})</span>
                                `;
                                assetListDiv.appendChild(label);
                            });
                        } else {
                            assetListDiv.innerHTML = '<p class="text-sm text-gray-500">Tidak ada aset yang tersedia di site ini.</p>';
                        }
                    });
            }

            // Panggil fungsi saat halaman pertama kali dimuat
            fetchAssets(siteSelect.value);

            // Panggil fungsi setiap kali site diubah
            siteSelect.addEventListener('change', function() {
                fetchAssets(this.value);
            });
        });
    </script>
    @endpush
</x-app-layout>
