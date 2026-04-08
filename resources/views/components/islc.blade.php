@props(['lbl', 'nm' => null, 'req' => true, 'fill' => '', 'full' => 'true', 'is_disabled' => 'false'])

<div class="flex flex-col @if ($full === 'true') flex-grow @endif gap-1 {{ $fill }}">
    <label class="text-sm text-gray-600 font-medium">
        {{ $lbl }} @if ($req)
            *
        @endif
    </label>

    <select name="{{ $nm }}" @if ($req) required @endif
        @if ($is_disabled === 'true') disabled @endif
        class="h-10 border bg-gray-100 border-gray-300 rounded-md px-3 text-gray-700 focus:outline-none focus:ring-1 focus:ring-gray-400 {{ $is_disabled === 'true' ? 'bg-gray-200 cursor-not-allowed' : '' }}">
        {{ $slot }}
    </select>

    {{-- Trick: Jika disabled, buat input hidden agar value tetap terkirim ke server --}}
    @if ($is_disabled === 'true')
        <input type="hidden" name="{{ $nm }}" value="{{ request($nm) }}">
    @endif
    <p class="flex items-start gap-1 text-xs text-yellow-600 italic mt-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-[2px] text-yellow-600" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M12 20h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
        </svg>
        <span>Tips: klik select, ketik nama, maka pilihan akan langsung menuju nama yang dicari.</span>
    </p>
</div>
