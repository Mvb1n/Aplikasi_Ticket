@php
    // $filePath di sini DIJAMIN berupa string
    $displayName = basename($filePath);
    if (strpos($displayName, '_') !== false) {
        $displayName = substr($displayName, strpos($displayName, '_') + 1);
    }
    
    $extension = strtolower(pathinfo($displayName, PATHINFO_EXTENSION));
    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp']);
    $isPdf = $extension == 'pdf';
    $isDoc = in_array($extension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']);
@endphp

{{-- Setiap item file --}}
<div class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
    <div class="w-0 flex-1 flex items-center">
        
        {{-- Bagian Ikon --}}
        <div class="flex-shrink-0 h-5 w-5 text-gray-400">
            @if($isImage)
                {{-- Ikon Gambar --}}
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M1 5.25A2.25 2.25 0 013.25 3h13.5A2.25 2.25 0 0119 5.25v9.5A2.25 2.25 0 0116.75 17H3.25A2.25 2.25 0 011 14.75v-9.5zm1.5 0v7.625c.307-.1.63-.16.974-.19L6.5 12.5l2.086 2.086a.75.75 0 001.06 0l2.086-2.086 2.562.854c.345.115.703.18 1.07.19V5.25H2.5zM3.25 4.5a.75.75 0 00-.75.75v.01c0 .02.004.04.004.06L3.25 14.75l3.25-3.25a2.25 2.25 0 013.182 0l2.086 2.086 2.086-2.086a2.25 2.25 0 013.182 0l3.25 3.25v-9.5a.75.75 0 00-.75-.75H3.25zM14 8a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd" /></svg>
            @elseif($isPdf)
                {{-- Ikon PDF --}}
                <svg class="text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.22 14.78a.75.75 0 001.06 0l7.22-7.22V12a.75.75 0 001.5 0V7.5a.75.75 0 00-.75-.75h-4.5a.75.75 0 000 1.5h2.69l-7.22 7.22a.75.75 0 000 1.06z" clip-rule="evenodd" /></svg>
            @elseif($isDoc)
                {{-- Ikon Dokumen --}}
                <svg class="text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M3.5 3.75a.25.25 0 01.25-.25h12.5c.138 0 .25.112.25.25v12.5a.25.25 0 01-.25.25H3.75a.25.25 0 01-.25-.25V3.75zM5 6.25a.75.75 0 01.75-.75h8.5a.75.75 0 010 1.5h-8.5a.75.75 0 01-.75-.75zM5 10a.75.75 0 01.75-.75h8.5a.75.75 0 010 1.5h-8.5A.75.75 0 015 10zm0 3.75a.75.75 0 01.75-.75h5.5a.75.75 0 010 1.5h-5.5a.75.75 0 01-.75-.75z" /></svg>
            @else
                {{-- Ikon File Umum --}}
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 000 1.5h11.5a.75.75 0 000-1.5H4.25zM4.25 9.5a.75.75 0 000 1.5h11.5a.75.75 0 000-1.5H4.25zM4.25 13.5a.75.75 0 000 1.5h11.5a.75.75 0 000-1.5H4.25z" clip-rule="evenodd" /></svg>
            @endif
        </div>
        
        <span class="ml-2 flex-1 w-0 truncate">
            {{ $displayName }}
        </span>
    </div>

    <div class="ml-4 flex-shrink-0">
        <a href="{{ Storage::url($filePath) }}" target="_blank" class="font-medium text-indigo-600 hover:text-indigo-500">
            Lihat
        </a>
        <a href="{{ Storage::url($filePath) }}" download="{{ $displayName }}" class="font-medium text-gray-600 hover:text-gray-500 ml-4">
            Download
        </a>
    </div>
</div>