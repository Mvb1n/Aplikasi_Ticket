<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Form untuk membuat token baru --}}
            <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
                <h3 class="text-lg font-medium">Buat Token API Baru</h3>
                <form method="POST" action="{{ route('api-tokens.store') }}">
                    @csrf
                    <div class="mt-4">
                        <x-input-label for="name" value="Nama Token (Contoh: 'Token untuk Aplikasi 2')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                    </div>
                    <div class="flex justify-end mt-4">
                        <x-primary-button>Buat Token</x-primary-button>
                    </div>
                </form>
            </div>

            {{-- Menampilkan token yang baru dibuat (hanya sekali) --}}
            @if (session('token'))
                <div class="p-4 mb-4 text-sm text-yellow-700 bg-yellow-100 rounded-lg">
                    <p class="font-bold">Token baru Anda telah dibuat. Harap salin sekarang. Anda tidak akan bisa melihatnya lagi.</p>
                    <input type="text" class="w-full mt-2 bg-gray-100 border-gray-300 rounded" value="{{ session('token') }}" readonly>
                </div>
            @endif

            {{-- Daftar token yang sudah ada --}}
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-medium">Token yang Sudah Ada</h3>
                <ul class="mt-4 space-y-2">
                    @forelse ($tokens as $token)
                        <li class="text-sm text-gray-700">{{ $token->name }} - Dibuat pada: {{ $token->created_at->format('d M Y') }}</li>
                    @empty
                        <li class="text-sm text-gray-500">Belum ada token yang dibuat.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
