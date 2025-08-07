<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Edit Aset: {{ $asset->name }}</h3>
                    <form method="POST" action="{{ route('assets.update', $asset->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Nama Aset (Read-only) -->
                        <div>
                            <x-input-label for="name" :value="__('Nama Aset')" />
                            <x-text-input id="name" class="block mt-1 w-full bg-gray-100" type="text" name="name" :value="$asset->name" readonly />
                        </div>

                        <!-- Nomor Seri (Read-only) -->
                        <div class="mt-4">
                            <x-input-label for="serial_number" :value="__('Nomor Seri')" />
                            <x-text-input id="serial_number" class="block mt-1 w-full bg-gray-100" type="text" name="serial_number" :value="$asset->serial_number" readonly />
                        </div>

                        <!-- Status Aset (Bisa Diedit) -->
                        <div class="mt-4">
                            <x-input-label for="status" :value="__('Status Aset')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="In Use" @selected($asset->status == 'In Use')>In Use</option>
                                <option value="In Repair" @selected($asset->status == 'In Repair')>In Repair</option>
                                <option value="Stolen/Lost" @selected($asset->status == 'Stolen/Lost')>Stolen/Lost</option>
                                <option value="Decommissioned" @selected($asset->status == 'Decommissioned')>Decommissioned</option>
                            </select>
                        </div>

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