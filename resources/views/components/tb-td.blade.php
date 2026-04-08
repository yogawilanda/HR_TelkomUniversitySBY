@props(['type' => null, 'nama' => null, 'sorting' => false])

<th data-field="{{ $nama }}" data-filter-control="{{ $type }}"
    @if ($sorting) data-sortable="true" @endif
    
    class="px-4 py-3 text-xs font-semibold text-blue-600 uppercase tracking-wider @if ($sorting) sortable @endif">
    {{ $slot }}

    @if ($sorting)
        <i class="bi bi-filter sort-icon"></i>
    @endif
</th>
