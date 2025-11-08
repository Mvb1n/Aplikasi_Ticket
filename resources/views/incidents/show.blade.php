<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Insiden #') }}{{ $incident->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Laporan Awal</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Judul</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $incident->title }}</dd>
                        </div>

                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Site Kejadian</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($incident->site)
                                    <a href="{{ route('sites.show', $incident->site->id) }}" class="text-indigo-600 hover:underline">
                                        {{ $incident->site->name }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </dd>
                        </div>

                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Pelapor</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $incident->user?->name ?? 'User Dihapus' }}</dd>
                        </div>

                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Lokasi Spesifik</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $incident->location }}</dd>
                        </div>

                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Tanggal Lapor</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $incident->created_at->format('d M Y, H:i') }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Kronologi</dt>
                            <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $incident->chronology }}</dd>
                        </div>

                        {{-- BAGIAN UNTUK MENAMPILKAN DAFTAR ASET --}}
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Aset yang Dilaporkan Hilang</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <ul class="list-disc list-inside">
                                    @forelse ($incident->assets as $asset)
                                        <li>{{ $asset->name }} (SN: {{ $asset->serial_number }})</li>
                                    @empty
                                        <li>Tidak ada aset terdaftar yang dilaporkan.</li>
                                    @endforelse
                                </ul>
                            </dd>
                        </div>
                        
                        {{-- ===== BLOK TAMPILAN FILE (SUDAH DIPERBAIKI) ===== --}}
                        <div class="sm:col-span-2">
                            @php
                                // Decode JSON, pastikan hasilnya array
                                $filesData = json_decode($incident->attachment_paths, true) ?? [];
                                
                                // Cek apakah ini struktur LAMA (array sederhana) atau BARU (objek)
                                $isOldStructure = !empty($filesData) && !isset($filesData['incident_files']) && !isset($filesData['asset_files']);
                                
                                if ($isOldStructure) {
                                    // Jika struktur lama, paksa jadi struktur baru
                                    $incidentFiles = $filesData;
                                    $assetFiles = [];
                                } else {
                                    // Jika struktur baru, baca normal
                                    $incidentFiles = $filesData['incident_files'] ?? [];
                                    $assetFiles = $filesData['asset_files'] ?? [];
                                }
                            @endphp

                            {{-- 1. Tampilkan File per Aset (VERSI BARU YANG LEBIH AMAN) --}}
                            <dt class="text-sm font-medium text-gray-500">File per Aset</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <ul class="list-disc list-inside">
                                    
                                    @php
                                        // Variabel untuk melacak apakah kita menemukan setidaknya satu file aset
                                        $foundAssetFiles = false;
                                    @endphp

                                    @foreach($incident->assets as $asset)
                                        @php
                                            // Cek file menggunakan KEDUA key: $asset->id DAN $asset->serial_number
                                            // Ini akan berhasil
                                            $filesForThisAsset = $assetFiles[$asset->id] ?? ($assetFiles[(string)$asset->id] ?? ($assetFiles[$asset->serial_number] ?? null));
                                        @endphp
                                        
                                        {{-- Hanya tampilkan jika kita menemukan file untuk aset ini --}}
                                        @if(!empty($filesForThisAsset))
                                            @php $foundAssetFiles = true; @endphp
                                            <li class="font-semibold mt-2">{{ $asset->name }} (SN: {{ $asset->serial_number }})
                                                {{-- Tampilkan file-file untuk aset ini --}}
                                                <div class="border border-gray-200 rounded-md divide-y divide-gray-200 my-2 ml-4">
                                                    @foreach($filesForThisAsset as $filePath)
                                                        @include('incidents.partials.file-attachment-item', ['filePath' => $filePath])
                                                    @endforeach
                                                </div>
                                            </li>
                                        @endif
                                    @endforeach

                                    {{-- Tampilkan pesan 'tidak ada' HANYA jika loop selesai dan tidak ada file yg ditemukan --}}
                                    @if(!$foundAssetFiles && $incident->assets->isNotEmpty())
                                        <li class="text-gray-500">Tidak ada file yang terlampir untuk aset yang dilaporkan.</li>
                                    @elseif($incident->assets->isEmpty())
                                        <li class="text-gray-500">Tidak ada aset yang terhubung dengan insiden ini.</li>
                                    @endif

                                </ul>
                            </dd>

                            {{-- 2. Tampilkan File Umum --}}
                            <dt class="text-sm font-medium text-gray-500 mt-4">File Pendukung Umum</dt>
                            <dd class="mt-2 text-sm text-gray-900">
                                <div class="border border-gray-200 rounded-md divide-y divide-gray-200">
                                    @forelse ($incidentFiles as $filePath)
                                        {{-- Panggil partial, $filePath di sini DIJAMIN string --}}
                                        @include('incidents.partials.file-attachment-item', ['filePath' => $filePath])
                                    @empty
                                        <div class="pl-3 pr-4 py-3 text-sm text-gray-500">
                                            Tidak ada file pendukung umum.
                                        </div>
                                    @endforelse
                                </div>
                            </dd>
                        </div>
                        {{-- ===== AKHIR BLOK FILE ===== --}}

                    </dl>
                </div>

                {{-- BAGIAN DISKUSI DI SINI --}}
                <div class="p-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Diskusi & Komentar</h3>

                    <form action="{{ route('incidents.comments.store', $incident->id) }}" method="POST">
                        @csrf
                        <textarea name="body" rows="3" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Tulis komentar Anda..."></textarea>
                        <div class="flex justify-end mt-2">
                            <x-primary-button>Kirim</x-primary-button>
                        </div>
                    </form>

                    <div class="mt-6 space-y-6">
                        @forelse ($incident->comments()->latest()->get() as $comment)
                            <div class="flex space-x-3">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-gray-500">
                                        <span class="text-xs font-medium leading-none text-white">{{ strtoupper(substr($comment->user->name, 0, 2)) }}</span>
                                    </span>
                                </div>
                                <div class="flex-1 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-medium">{{ $comment->user->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                                    </div>
                                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $comment->body }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Belum ada komentar.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Hanya tampilkan kolom ini untuk Admin atau Security --}}
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('security'))
                <div class="md:col-span-1 bg-white overflow-hidden shadow-sm sm:rounded-lg h-fit">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Investigasi & Aksi</h3>

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

                        <form method="POST" action="{{ route('incidents.update', $incident->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div>
                                <x-input-label for="investigation_notes" :value="__('Catatan Investigasi (LHI)')" />
                                <textarea id="investigation_notes" name="investigation_notes" rows="6" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('investigation_notes', $incident->investigation_notes) }}</textarea>
                                <x-input-error :messages="$errors->get('investigation_notes')" class="mt-2" />
                            </div>

                            {{-- ===== BLOK FORM UPLOAD FILE (SUDAH DIPERBAIKI) ===== --}}
                            <div class="mt-4 space-y-4">
    
                                {{-- Loop untuk setiap aset yang terlibat --}}
                                @foreach($incident->assets as $asset)
                                    <div>
                                        <x-input-label :for="'asset_files_' . $asset->id" 
                                                       :value="__('File untuk: ' . $asset->name . ' (SN: ' . $asset->serial_number . ')' )" />
                                        <input id="{{ 'asset_files_' . $asset->id }}" 
                                               name="asset_files[{{ $asset->id }}][]" {{-- Ini kuncinya: asset_files[ID_ASET][] --}}
                                               type="file" multiple
                                               class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                        <x-input-error :messages="$errors->get('asset_files.' . $asset->id . '.*')" class="mt-2" />
                                    </div>
                                @endforeach
                            
                                {{-- Input untuk file umum (tidak terkait aset) --}}
                                <div>
                                    <x-input-label for="incident_files" :value="__('File Umum (Tidak terkait aset spesifik)')" />
                                    <input id="incident_files" name="incident_files[]" type="file" multiple
                                           class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    <x-input-error :messages="$errors->get('incident_files.*')" class="mt-2" />
                                </div>
                            </div>
                            {{-- ===== AKHIR BLOK FORM UPLOAD ===== --}}

                            <div class="mt-4">
                                <x-input-label for="status" :value="__('Ubah Status')" />
                                <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="Open" @selected($incident->status == 'Open')>Open</option>
                                    <option value="In Progress" @selected($incident->status == 'In Progress')>In Progress</option>
                                    <option value="Resolved" @selected($incident->status == 'Resolved')>Resolved</option>
                                    <option value="Closed" @selected($incident->status == 'Closed')>Closed</option>
                                    <option value="Cancelled" @selected($incident->status == 'Cancelled')>Cancelled</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <div class="w-full inline-flex items-center justify-center mt-4">
                                <x-primary-button class="w-full justify-center">
                                    {{ __('Update Insiden') }}
                                </x-primary-button>
                            </div>
                            
                        </form>
                
                        <div class="border-t border-gray-200 mt-6 pt-6">
                            <h4 class="text-md font-medium text-gray-900 mb-2">Eskalasi ke Problem</h4>
                            <p class="text-sm text-gray-600 mb-3">Jika insiden ini adalah bagian dari masalah yang lebih besar, buat tiket Problem baru.</p>
                            <a href="{{ route('problems.create') }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-400 focus:bg-yellow-400 active:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Buat Tiket Problem
                            </a>
                        </div>

                    </div>
                </div>
            @endif

            </div>
        </div>
    </div>
</x-app-layout>