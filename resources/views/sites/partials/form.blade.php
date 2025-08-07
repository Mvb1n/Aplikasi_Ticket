{{-- Menampilkan error validasi --}}
@if ($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Nama Site -->
<div>
    <x-input-label for="name" :value="__('Nama Site')" />
    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $site->name ?? '')" required />
</div>

<!-- Kode Lokasi -->
<div class="mt-4">
    <x-input-label for="location_code" :value="__('Kode Lokasi (Contoh: PLM, JKT)')" />
    <x-text-input id="location_code" class="block mt-1 w-full" type="text" name="location_code" :value="old('location_code', $site->location_code ?? '')" required />
</div>

<!-- Alamat -->
<div class="mt-4">
    <x-input-label for="address" :value="__('Alamat Lengkap')" />
    <textarea id="address" name="address" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('address', $site->address ?? '') }}</textarea>
</div>

<div class="flex items-center justify-end mt-4">
    <a href="{{ route('sites.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</a>
    <x-primary-button>
        {{ isset($site) ? 'Simpan Perubahan' : 'Tambah Site' }}
    </x-primary-button>
</div>