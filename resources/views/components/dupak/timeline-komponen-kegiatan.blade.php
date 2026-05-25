{{--
    Komponen Blade untuk satu item di Timeline DUPAK.
    Props:
      $item['title']          - string
      $item['date']           - string
      $item['content']        - HTML string (optional)
      $item['dot_color']      - Tailwind bg class (optional)
      $item['border_color']   - Tailwind border-l class (optional)
      $item['is_expanded']    - bool
      $item['activity_items'] - array of { deskripsi, komponen, kum, status, evaluations[] } (optional)
      $item['details']        - array or assoc for button type (optional, legacy fallback)
      $item['evaluation']     - array of { role, status, comment } (optional)
--}}
@php
    $statusBadge = [
        'pending'  => 'bg-yellow-100 text-yellow-700 border border-yellow-200',
        'approved' => 'bg-green-100 text-green-700 border border-green-200',
        'rejected' => 'bg-red-100 text-red-700 border border-red-200',
        'revision' => 'bg-blue-100 text-blue-700 border border-blue-200',
    ];
    $hasBody = !empty($item['content'])
        || !empty($item['activity_items'])
        || (!empty($item['details']) && $item['details'] !== null)
        || (!empty($item['evaluation']) && count($item['evaluation']) > 0);
@endphp

<div class="relative pl-8 pb-1 group">

    {{-- Timeline Dot --}}
    <div class="absolute left-0 top-3.5 w-4 h-4 {{ $item['dot_color'] ?? 'bg-blue-900' }} rounded-full border-4 border-white z-10 shadow-sm"></div>

    {{-- Card --}}
    <div class="bg-white rounded-xl accordion-item border border-gray-200 border-l-4 {{ $item['border_color'] ?? 'border-l-gray-400' }} shadow-sm hover:shadow-md transition-shadow">

        {{-- Header --}}
        <button
            class="w-full text-left px-5 py-4 flex items-center justify-between focus:outline-none accordion-header group"
            aria-expanded="{{ $item['is_expanded'] ? 'true' : 'false' }}"
        >
            <div class="flex items-center gap-3 min-w-0">
                <div class="min-w-0">
                    <h3 class="text-sm font-bold text-gray-800 leading-tight">{{ $item['title'] ?? 'Langkah Tanpa Judul' }}</h3>
                    @if(isset($item['date']) && $item['date'] !== '-')
                        <p class="text-xs text-gray-400 mt-0.5 flex items-center gap-1">
                            <i class="far fa-calendar-alt"></i> {{ $item['date'] }}
                        </p>
                    @endif
                </div>
            </div>
            {{-- Chevron --}}
            @if($hasBody)
                <span class="transform transition-transform duration-300 accordion-icon flex-shrink-0 ml-3 {{ $item['is_expanded'] ? 'rotate-180' : '' }}">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </span>
            @endif
        </button>

        {{-- Body --}}
        @if($hasBody)
        <div class="accordion-content overflow-hidden transition-all duration-300 ease-in-out">
            <div class="px-5 pb-5 border-t border-gray-100 space-y-4">

                {{-- Summary text --}}
                @if(!empty($item['content']))
                    <p class="text-sm text-gray-600 leading-relaxed pt-3">{!! $item['content'] !!}</p>
                @endif

                {{-- === STRUCTURED ACTIVITY ITEMS (new format) === --}}
                @if(!empty($item['activity_items']))
                    <div class="pt-1 space-y-2">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Daftar Butir Kegiatan</p>
                        @foreach($item['activity_items'] as $act)
                            @php
                                $s = strtolower($act['status'] ?? 'pending');
                                $badgeCls = $statusBadge[$s] ?? 'bg-gray-100 text-gray-600 border border-gray-200';
                            @endphp
                            <div class="rounded-lg border border-gray-100 bg-gray-50 p-3 group/act hover:bg-blue-50 hover:border-blue-100 transition-colors">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-gray-800 leading-snug">{{ $act['deskripsi'] }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $act['komponen'] }}</p>
                                    </div>
                                    <div class="flex flex-col items-end gap-1 flex-shrink-0">
                                        <span class="text-xs font-black text-blue-900 bg-blue-50 border border-blue-100 px-2 py-0.5 rounded-lg">{{ $act['kum'] }} KUM</span>
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $badgeCls }}">{{ ucfirst($act['status'] ?? 'pending') }}</span>
                                    </div>
                                </div>
                                {{-- Per-item evaluations --}}
                                @if(!empty($act['evaluations']))
                                    <div class="mt-2 pt-2 border-t border-gray-200 space-y-1.5">
                                        @foreach($act['evaluations'] as $ev)
                                            <div class="flex items-start gap-2">
                                                <i class="fas fa-comment-dots text-blue-400 text-xs mt-0.5 flex-shrink-0"></i>
                                                <div class="text-xs text-gray-600 leading-relaxed">
                                                    <span class="font-semibold text-gray-700">{{ $ev['role'] }}</span>
                                                    @if(!empty($ev['comment']))
                                                        <span class="text-gray-400 mx-1">·</span>{{ $ev['comment'] }}
                                                    @endif
                                                    <span class="ml-1 px-1.5 py-0.5 rounded text-[9px] font-bold {{ $ev['status'] === 'Verified' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                        {{ $ev['status'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- === LEGACY DETAILS FALLBACK === --}}
                @if(!empty($item['details']) && empty($item['activity_items']))
                    @if(isset($item['details']['type']) && $item['details']['type'] === 'button')
                        <div class="pt-2">
                            <p class="text-sm text-gray-600 mb-3">SK Digital Anda tersedia di sini:</p>
                            <button class="text-white {{ $item['details']['button_color'] }} font-medium rounded-lg text-sm px-4 py-2 focus:outline-none flex items-center gap-2">
                                <i class="fas fa-download"></i> {{ $item['details']['label'] }}
                            </button>
                        </div>
                    @elseif(is_array($item['details']) && isset($item['details'][0]))
                        <div class="bg-gray-50 rounded-lg p-3 space-y-1">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ $item['details'][0] }}</p>
                            <ul class="space-y-1 text-xs text-gray-700 list-disc ml-4 mt-1">
                                @for ($i = 1; $i < count($item['details']); $i++)
                                    @php $row = $item['details'][$i]; @endphp
                                    <li>{!! is_array($row) ? ($row[0] ?? '') : $row !!}</li>
                                @endfor
                            </ul>
                        </div>
                    @endif
                @endif

                {{-- === OVERALL EVALUATIONS === --}}
                @if(!empty($item['evaluation']) && count($item['evaluation']) > 0)
                    <div class="pt-2 space-y-2">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Catatan Penilaian</p>
                        @foreach($item['evaluation'] as $eval)
                            @php $isAdmin = str_contains($eval['role'], 'Admin'); @endphp
                            <div class="flex items-start gap-3 p-3 rounded-lg {{ $isAdmin ? 'bg-amber-50 border border-amber-100' : 'bg-blue-50 border border-blue-100' }}">
                                <i class="fas {{ $isAdmin ? 'fa-user-shield text-amber-500' : 'fa-user-check text-blue-500' }} text-sm mt-0.5 flex-shrink-0"></i>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold {{ $isAdmin ? 'text-amber-700' : 'text-blue-700' }}">{{ $eval['role'] }}</p>
                                    @if(!empty($eval['comment']))
                                        <p class="text-xs text-gray-600 italic mt-0.5">"{{ $eval['comment'] }}"</p>
                                    @endif
                                </div>
                                <span class="flex-shrink-0 text-[10px] px-2 py-0.5 rounded-full font-semibold {{ $eval['status'] === 'Verified' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $eval['status'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
        @endif

    </div>
</div>