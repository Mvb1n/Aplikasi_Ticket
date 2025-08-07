<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Edit Peran untuk: {{ $user->name }}</h3>
                    <p class="text-sm text-gray-500 mb-2">Email: {{ $user->email }}</p>

                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mt-4">
                            <x-input-label :value="__('Peran (Role)')" />
                            <div class="mt-2 space-y-2">
                                @foreach ($roles as $role)
                                    <label class="flex items-center">
                                        {{-- DIUBAH MENJADI RADIO BUTTON --}}
                                        <input type="radio" name="role" value="{{ $role->id }}"
                                            class="border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            {{-- Periksa peran pertama yang dimiliki user --}}
                                            @if($user->roles->first() && $user->roles->first()->id == $role->id) checked @endif
                                        >
                                        <span class="ml-2 text-sm text-gray-600">{{ ucfirst($role->name) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                Batal
                            </a>
                            <x-primary-button>
                                {{ __('Simpan Perubahan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>