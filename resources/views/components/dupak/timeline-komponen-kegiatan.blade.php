{{--
    Komponen Blade untuk satu item di Timeline DUPAK.
    Menerima variabel $item yang berisi data langkah.
--}}
<div class="relative pl-6 md:pl-8 group">
    
    {{-- Timeline Dot --}}
    <div class="absolute -left-[10px] top-0 w-5 h-5 {{ $item['dot_color'] ?? 'bg-blue-900' }} rounded-full border-4 border-white dark:border-gray-900 z-10"></div>
    
    {{-- Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg accordion-item border-l-4 {{ $item['border_color'] ?? 'border-gray-400' }} shadow-sm">
        
        {{-- Header (Clickable) --}}
        <button 
            class="w-full text-left px-4 py-3 flex items-center justify-between focus:outline-none accordion-header" 
            aria-expanded="{{ $item['is_expanded'] ? 'true' : 'false' }}"
        >
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $item['title'] ?? 'Langkah Tanpa Judul' }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    @if(isset($item['date']))
                        <i class="far fa-calendar-alt mr-1"></i> {{ $item['date'] }}
                    @endif
                </p>
            </div>
            {{-- Chevron Icon --}}
            <span class="transform transition-transform duration-300 accordion-icon {{ $item['is_expanded'] ? 'rotate-180' : '' }}">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </span>
        </button>
        
        {{-- Body (Content) --}}
        <div class="accordion-content overflow-hidden transition-all duration-300 ease-in-out">
            <div class="px-4 pb-3 pt-0 border-t border-gray-100 dark:border-gray-700">
                
                @if (isset($item['content']) && $item['content'])
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed pt-2">
                        {!! $item['content'] !!}
                    </p>
                @endif

                @if (isset($item['details']))
                    @if (isset($item['details']['type']) && $item['details']['type'] === 'button')
                        {{-- Kasus Tombol (Contoh: Unduh SK) --}}
                        <div class="pt-2">
                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">SK Digital Anda tersedia di sini:</p>
                            <button class="text-white {{ $item['details']['button_color'] }} hover:{{ $item['details']['button_color'] }}/90 focus:ring-4 focus:ring-gray-300 font-medium rounded-md text-sm px-4 py-2 focus:outline-none">
                                <i class="fas fa-download mr-1"></i> {{ $item['details']['label'] }}
                            </button>
                        </div>
                    @else
                        {{-- Kasus Rincian Kegiatan (Contoh: List Kegiatan) --}}
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-md p-3 mt-2">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">{{ $item['details'][0] ?? 'Rincian:' }}</h4>
                            <ul class="space-y-1 text-xs text-gray-700 dark:text-gray-300 list-disc ml-4">
                                @for ($i = 1; $i < count($item['details']); $i++)
                                    <li>{!! $item['details'][$i][0] !!}</li>
                                @endfor
                            </ul>
                        </div>
                    @endif
                @endif

                {{-- Slot Evaluasi dari TPAK / Admin --}}
                @if (isset($item['evaluation']) && count($item['evaluation']) > 0)
                    <div class="mt-4 space-y-3">
                        @foreach($item['evaluation'] as $eval)
                            <div class="p-3 rounded-md bg-blue-50 dark:bg-blue-900/20 border-l-4 {{ $eval['role'] === 'Admin' ? 'border-amber-400' : 'border-blue-400' }}">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold uppercase tracking-wider {{ $eval['role'] === 'Admin' ? 'text-amber-700 dark:text-amber-400' : 'text-blue-700 dark:text-blue-400' }}">
                                        <i class="fas {{ $eval['role'] === 'Admin' ? 'fa-user-shield' : 'fa-user-check' }} mr-1"></i>
                                        Catatan {{ $eval['role'] }}
                                    </span>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full {{ $eval['status'] === 'Verified' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $eval['status'] }}
                                    </span>
                                </div>
                                <p class="text-sm italic text-gray-700 dark:text-gray-300">"{{ $eval['comment'] }}"</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>