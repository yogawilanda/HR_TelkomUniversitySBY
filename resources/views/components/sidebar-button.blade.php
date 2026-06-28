@props([
    'href' => null,
    'icon' => null,
    'label' => null,
    'isactive' => '',
])

<li>
    <a href="{{ $href }}" title="{{ html_entity_decode(strip_tags($label)) }}"
        class="sm-center flex route_pop_up items-start align-items-center justify-center px-3 py-2 rounded-lg transition-all duration-200 group {{ $isactive === 'active' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">

        {{-- Icon dibuat mt-0.5 agar tetap konsisten di kiri atas --}}
        <i class="{{ $icon }} text-lg mr-3 mt-0.5 w-6 items-center text-center transition-colors shrink-0 {{ $isactive === 'active' ? 'text-white' : 'text-gray-400 group-hover:text-blue-500' }}"></i>

        {{-- Label teks penuh tanpa titik-titik dan {!! !!} agar tidak render &amp; --}}
        <span class="sm-hide text-sm tracking-wide items-center flex-1 break-words whitespace-normal leading-tight {{ $isactive === 'active' ? 'font-medium' : 'font-normal' }}">
            {!! $label !!}
        </span>
    </a>
</li>
