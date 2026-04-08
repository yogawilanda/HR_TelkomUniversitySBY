@props([
    'name',
    'value' => '',
    'idx' => 0,
    'caption_special_button' => '',
    'onclick' => '',
    'has_Special_Button' => 'false',
    'editable' => 'true',
    'isNeed' => true,
    'IsEcTrue' => null,
])

@php
    $key = $name . '.' . $idx;
    $hasError = $errors->has($key);
    $isEmpty = $value === '' || $value === null;

    $display = $isEmpty ? 'PERLU DIISI' : $value;
@endphp

<x-tb-cl-fill cls="items{{ $idx }}">

    @if ($isNeed == 'True')
        <div class="@if ($editable == 'true') editable-cell @endif px-2 py-1 rounded cursor-pointer transition hover:bg-gray-100 hover:shadow-sm hover:ring-1 hover:ring-gray-300
        {{ $hasError ? 'bg-red-50 ring-1 ring-red-400' : '' }}"
            data-name="{{ $name }}" data-value="{{ $value }}">

            <span class="{{ $isEmpty ? 'text-red-500' : '' }} warning_fill">
                @if ($IsEcTrue != null)
                    @if ($IsEcTrue == 'true')
                        {{ $display }}
                    @else
                        
                    @endif
                @else
                    {{ $display }}
                @endif
            </span>

            <input type="hidden" name="{{ $name }}[]" value="{{ $value }}">

            {{-- KETERANGAN ERROR (PER CELL) --}}
            @if ($hasError)
                <div class="mt-1 text-xs text-red-600 leading-snug eror_caution">
                    {{ $errors->first($key) }}
                </div>
            @endif
        </div>
    @endif

    @if ($has_Special_Button == 'true')
        <button type="button"
            class="btn-close !bg-none min-h-fit py-2 border-0 focus:outline-none focus:ring-0 hover:bg-gray-300 hover:border-gray-800 w-[100px] text-[10px]"
            onclick="{{ $onclick }}">{{ $caption_special_button }}</button>
    @endif
</x-tb-cl-fill>
