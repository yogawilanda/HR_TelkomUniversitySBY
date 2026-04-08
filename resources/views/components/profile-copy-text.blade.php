 @props(['subjek'=>null])
<dd class="truncate font-medium text-gray-900 dark:text-gray-100 cursor-pointer hover:underline"
    onclick="navigator.clipboard.writeText('{{ $slot }}');toast('{{ $subjek }} Berhasil di Salin')"
    title="Klik untuk menyalin">
    {{ $slot }}
</dd>
