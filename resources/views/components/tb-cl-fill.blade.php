@props(['id' => null, 'cls' => null, 'idTargetModal' => null, 'click' => null , 'clsText' => null])

<td class="x-tb-cl-fill fill-table-row px-6 y-4  whitespace-nowrap align-middle text-[14.5px] leading-relaxed tracking-normal {{ $cls }}"
    id="{{ $id }}"
    @if ($idTargetModal) data-bs-target="#{{ $idTargetModal }}" @endif
    @if ($click != null)
        onclick="if (!event.target.closest('a, button, [data-no-modal]')) { {{ $click }} }"
    @endif>
    <div class="text-wrap items-center justify-center align-items-center {{ $clsText }} font-normal break-words">
        {{ $slot }}
    </div>
</td>
