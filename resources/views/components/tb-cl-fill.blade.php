@props(['id' => null, 'cls' => null, 'idTargetModal' => null, 'click' => null])

<td {{-- class="x-tb-cl-fill fill-table-row px-4 py-3 whitespace-nowrap align-middle {{ $cls }}"  --}}
    class="x-tb-cl-fill fill-table-row px-4 py-3 whitespace-nowrap align-middle break-words text-wrap {{ $cls }}"
    id="{{ $id }}" @if ($idTargetModal) data-bs-target="#{{ $idTargetModal }}" @endif
    @if ($click != null) onclick="if (
            !event.target.closest(a, button, [data-no-modal])) { {{ $click }} }" @endif>
    {{ $slot }}
</td>
