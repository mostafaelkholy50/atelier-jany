<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center w-full gap-4">
            <span class="font-bold text-xl text-blue-900 flex items-center gap-2"><span>👗</span> العميلات</span>
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto mt-2 md:mt-0">
                <form method="GET" action="{{ route('clients.index') }}" class="w-full sm:flex-1 md:w-64">
                    <div class="relative">
                        <input type="text" id="clientSearchInput" name="search" value="{{ request('search') }}"
                            placeholder="بحث بالاسم أو التليفون..."
                            class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition pl-10 h-10 text-sm shadow-sm" autocomplete="off">
                        <span id="clientSearchIcon" class="absolute left-3 top-2.5 opacity-50 pointer-events-none">🔍</span>
                        <span id="clientSearchSpinner" class="absolute left-3 top-2.5 hidden">
                            <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        </span>
                    </div>
                </form>
                <a href="{{ route('clients.create') }}" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition shadow-lg shadow-blue-200 whitespace-nowrap flex items-center justify-center">
                    + إضافة عميلة
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        @if(session('success'))
            <div class="mb-4 px-5 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl font-bold text-sm flex items-center gap-2">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if(request()->filled('search'))
            <div class="mb-4 text-sm text-gray-600 bg-white/60 backdrop-blur px-4 py-2.5 rounded-xl border border-gray-100 flex items-center gap-2 shadow-sm">
                نتائج البحث عن: <strong class="text-blue-900">"{{ request('search') }}"</strong>
                <span class="text-gray-400">({{ $clients->total() }} نتيجة)</span>
                <a href="{{ route('clients.index') }}" class="text-red-500 hover:underline text-xs mr-auto bg-white px-2 py-1 rounded-md border border-red-100 shadow-sm">&times; إلغاء</a>
            </div>
        @endif

        @if($clients->isEmpty())
            <div class="text-center py-20 glass-card">
                <div class="text-6xl mb-4 opacity-30">👗</div>
                <h3 class="text-xl font-bold text-gray-700 mb-2">
                    {{ request()->filled('search') ? 'مفيش نتايج للبحث ده' : 'مفيش عميلات لسه!' }}
                </h3>
                <a href="{{ route('clients.create') }}" class="inline-block mt-3 px-6 py-2.5 bg-blue-600 text-white rounded-xl font-bold text-sm shadow-lg hover:bg-blue-700 transition">
                    + إضافة أول عميلة
                </a>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-4">
                @foreach($clients as $client)
                    @php
                        $hasOverdue = $client->orders->contains(fn($o) =>
                            $o->delivery_date && \Carbon\Carbon::parse($o->delivery_date)->isPast() && $o->status !== 'completed'
                        );
                    @endphp
                    <div class="glass-card flex flex-col relative group overflow-hidden transition-all duration-200 hover:shadow-xl hover:-translate-y-1
                                {{ $client->is_traveler ? 'border-orange-200 ring-2 ring-orange-100' : '' }}">

                        {{-- Traveler ribbon --}}
                        @if($client->is_traveler)
                            <div class="absolute top-0 right-0 left-0 bg-gradient-to-r from-orange-400 to-amber-400 text-white text-[11px] font-black text-center py-1 tracking-wide flex items-center justify-center gap-1 shadow-sm">
                                ✈️ من سفر / استعجال
                            </div>
                        @endif

                        {{-- Overdue badge --}}
                        @if($hasOverdue)
                            <div class="absolute top-2 left-2 {{ $client->is_traveler ? 'top-7' : '' }} text-[10px] bg-red-100 text-red-600 border border-red-200 px-2 py-0.5 rounded-full font-bold z-10">⏰ متأخر</div>
                        @endif

                        {{-- Profile & info --}}
                        <a href="{{ route('clients.show', $client) }}"
                           class="flex flex-col items-center text-center gap-3 p-4 pt-{{ $client->is_traveler ? '8' : '4' }} flex-1">

                            @if($client->image)
                                <img src="{{ asset('app-storage/' . $client->image) }}"
                                     class="w-16 h-16 rounded-full object-cover border-4 border-white shadow-md group-hover:scale-105 transition-transform">
                            @else
                                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-200 to-indigo-300 text-white flex items-center justify-center font-black text-2xl shadow-md border-4 border-white group-hover:scale-105 transition-transform">
                                    {{ mb_substr($client->name, 0, 1) }}
                                </div>
                            @endif

                            <div class="w-full">
                                <div class="font-bold text-gray-800 text-sm leading-snug">{{ $client->name }}</div>
                                @if($client->phone)
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $client->phone }}</div>
                                @endif
                            </div>

                            <div class="text-xs w-full justify-center border-t border-gray-100 pt-2">
                                <span class="bg-blue-50 text-blue-700 border border-blue-100 px-2 py-1 rounded-lg font-bold">
                                    {{ $client->orders_count }} طلب
                                </span>
                            </div>
                        </a>

                        {{-- Action buttons always visible at bottom --}}
                        <div class="flex border-t border-gray-100">
                            <a href="{{ route('clients.show', $client) }}"
                               class="flex-1 text-center py-2.5 text-xs font-bold text-indigo-600 hover:bg-indigo-50 transition flex items-center justify-center gap-1">
                                👁️ عرض
                            </a>
                            <div class="w-px bg-gray-100"></div>
                            <a href="{{ route('clients.edit', $client) }}"
                               class="flex-1 text-center py-2.5 text-xs font-bold text-blue-600 hover:bg-blue-50 transition flex items-center justify-center gap-1">
                                ✏️ تعديل
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">{{ $clients->links() }}</div>
        @endif
    </div>

    <script>
        (function() {
            const input = document.getElementById('clientSearchInput');
            const icon  = document.getElementById('clientSearchIcon');
            const spin  = document.getElementById('clientSearchSpinner');
            if (!input) return;
            let timer;
            input.addEventListener('input', function() {
                clearTimeout(timer);
                timer = setTimeout(function() {
                    const val = input.value.trim();
                    const url = new URL(window.location.href);
                    if (val === '') { url.searchParams.delete('search'); }
                    else { url.searchParams.set('search', val); }
                    url.searchParams.delete('page');
                    if (icon) icon.classList.add('hidden');
                    if (spin) spin.classList.remove('hidden');
                    window.location.href = url.toString();
                }, 500);
            });
        })();
    </script>
</x-app-layout>
