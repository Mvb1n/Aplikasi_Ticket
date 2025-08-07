@if ($errors->any())
    {{-- ... (kode untuk menampilkan error) ... --}}
@endif

<!-- Judul -->
<div>
    <x-input-label for="title" value="Judul Artikel" />
    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $article->title ?? '')" required />
</div>

<!-- Konten -->
<div class="mt-4">
    <x-input-label for="content" value="Isi Konten" />
    <textarea id="content" name="content" rows="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('content', $article->content ?? '') }}</textarea>
</div>

<!-- Status -->
<div class="mt-4">
    <x-input-label for="status" value="Status" />
    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        <option value="draft" @selected(old('status', $article->status ?? '') == 'draft')>Draft</option>
        <option value="published" @selected(old('status', $article->status ?? '') == 'published')>Published</option>
    </select>
</div>

<div class="flex justify-end mt-4">
    <x-primary-button>
        {{ isset($article) ? 'Update Artikel' : 'Simpan Artikel' }}
    </x-primary-button>
</div>
