@props([
    'lbl',
    'type' => 'text',
    'val' => null,
    'plc' => 'Isi Disini',
    'nm' => '',
    'req' => true,
    'dis' => false,
    'max' => 100,
    'step' => '',
    'fill' => '',
    'id' => '',
    'file' => 'application/pdf, image/*', // <— tambahkan ini
    'rules' => '', // <— tambahkan ini
])

@php
    // pastikan $rules array
    $rules = is_array($rules) ? $rules : [];
    $hasAnyHelp = $max !== 'none' || !empty($rules);
@endphp

<div class="flex flex-col gap-1 {{ $fill }}">
    <label class="text-sm text-gray-600 font-medium">
        {{ $lbl }}
        @if ($req==true)
            *
        @endif
    </label>

    @if ($type !== 'textarea')
        <input type="{{ $type }}" name="{{ $nm }}" placeholder="{{ $plc }}"
            value="{{ old($nm, $val) }}" @if ($req) required @endif
            @if ($dis) disabled @endif
            @if ($step != '') step="{{ $step }}" @endif
            @if ($type == 'number') inputmode="decimal" @endif
            @if ($max !== 'none') maxlength="{{ $max }}" @endif
            @if($max != 'none' && $type == 'date') max = {{ $max }}  @endif
            @if($type=='file') 
            accept="{{ $file }}"
            @endif
            @if($id!=null) id="{{ $id }}" @endif
            class="peer bg-gray-100 h-10 border @if($type=='file') py-1 @endif border-gray-300 rounded-md px-3 text-gray-700 focus:outline-none focus:ring-1 focus:ring-gray-400">
    @else
        <textarea name="{{ $nm }}" rows="4" placeholder="{{ $plc }}"
            @if ($max !== 'none') maxlength="{{ $max }}" @endif
            @if ($req==true) required @endif @if ($dis) disabled @endif
            class="peer border bg-gray-100 flex-grow-1 border-gray-300 rounded-md px-3 py-2 text-gray-700 focus:outline-none focus:ring-1 focus:ring-gray-400">{{ old($nm, $val) }}</textarea>
    @endif

    @if ($rules != 'none')
        @if ($hasAnyHelp)
            <ul
                class="hidden peer-focus:block text-xs text-gray-500 list-disc ml-5 transition-all duration-200 ease-in-out">
                @if ($max !== 'none')
                    <li>Maksimal {{ $max }} @if($type==='text') Karakter @endif</li>
                @endif

                @foreach ($rules as $rule)
                    <li>{{ $rule }}</li>
                @endforeach
            </ul>
        @endif
    @endif

    @error($nm)
        <div class="text-danger text-sm">{{ $message }}</div>
    @enderror

</div>
