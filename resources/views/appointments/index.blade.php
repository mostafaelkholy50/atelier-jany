<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <span class="font-bold text-xl text-blue-900 flex items-center gap-2"><span>📅</span> جدول المواعيد</span>
            <a href="{{ route('orders.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition shadow-lg shadow-blue-200">
                + طلب جديد
            </a>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">

        {{-- ========= OVERDUE ALERT ========= --}}
        @if($overdueOrders->count() > 0)
        <div class="glass-card border-red-200 ring-2 ring-red-100 overflow-hidden">
            <div class="bg-gradient-to-r from-red-500 to-rose-500 text-white px-5 py-3 flex items-center justify-between">
                <h3 class="font-black text-sm flex items-center gap-2">⏰ طلبات متأخرة — {{ $overdueOrders->count() }}</h3>
                <a href="{{ route('orders.index') }}" class="text-xs bg-white/20 hover:bg-white/40 px-3 py-1 rounded-lg font-bold transition">إدارة الطلبات</a>
            </div>
            <div class="divide-y divide-red-50">
                @foreach($overdueOrders as $order)
                <a href="{{ route('orders.show', $order) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-red-50/40 transition">
                    @if($order->client->image)
                        <img src="{{ asset('app-storage/' . $order->client->image) }}" class="w-8 h-8 rounded-full object-cover border-2 border-red-200 shrink-0">
                    @else
                        <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-black text-xs border-2 border-red-200 shrink-0">{{ mb_substr($order->client->name,0,1) }}</div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-gray-800 text-sm">
                            {{ $order->client->name }}
                            @if($order->client->is_traveler)<span class="text-orange-500 text-xs">✈️</span>@endif
                        </div>
                        <div class="text-xs text-gray-400">#{{ $order->order_code }} · {{ $order->itemCategory->name ?? '-' }}</div>
                    </div>
                    <div class="text-xs font-black text-red-600 bg-red-50 px-2 py-1 rounded-lg border border-red-200 shrink-0">
                        {{ \Carbon\Carbon::parse($order->delivery_date)->diffForHumans() }}
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ========= WEEK NAVIGATION ========= --}}
        <div class="glass-card p-4 flex items-center justify-between gap-3">
            <a href="{{ route('appointments.index', ['week' => $weekOffset - 1]) }}"
               class="flex items-center gap-1.5 px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 transition shadow-sm whitespace-nowrap">
                &#8594; السابق
            </a>

            <div class="text-center flex-1 px-2">
                <div class="font-black text-blue-900 text-sm md:text-base">
                    {{ $weekStart->format('d/m') }} — {{ $weekEnd->format('d/m/Y') }}
                </div>
                <div class="text-xs mt-0.5 font-bold
                    {{ $weekOffset === 0 ? 'text-blue-500' : ($weekOffset < 0 ? 'text-gray-400' : 'text-amber-500') }}">
                    @if($weekOffset === 0) النهارده وال 6 أيام القادمة
                    @elseif($weekOffset < 0) {{ abs($weekOffset) }} {{ abs($weekOffset) === 1 ? 'أسبوع مضى' : 'أسابيع مضت' }}
                    @else {{ $weekOffset }} {{ $weekOffset === 1 ? 'أسبوع من الآن' : 'أسابيع من الآن' }}
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if($weekOffset !== 0)
                    <a href="{{ route('appointments.index') }}"
                       class="hidden sm:block px-3 py-2.5 bg-blue-600 text-white text-xs font-bold rounded-xl hover:bg-blue-700 transition whitespace-nowrap">
                        هذا الأسبوع
                    </a>
                @endif
                <a href="{{ route('appointments.index', ['week' => $weekOffset + 1]) }}"
                   class="flex items-center gap-1.5 px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 transition shadow-sm whitespace-nowrap">
                    القادم &#8592;
                </a>
            </div>
        </div>

        {{-- ========= WEEKLY CALENDAR ========= --}}
        @php
            $todayStr = \Carbon\Carbon::today()->format('Y-m-d');
        @endphp

        {{-- Desktop: 7-column calendar (Using flex to prevent missing Tailwind JIT grid classes) --}}
        <div class="hidden lg:flex gap-3 items-stretch justify-between w-full">
            @for($i = 0; $i < 7; $i++)
                @php
                    $day      = $weekStart->copy()->addDays($i);
                    $dayStr   = $day->format('Y-m-d');
                    $isToday  = $dayStr === $todayStr;
                    $isPast   = $dayStr < $todayStr;
                    $dayLabel = $day->isoFormat('ddd');
                    $dayOrders = $weekOrders->filter(fn($o) =>
                        $o->delivery_date && \Carbon\Carbon::parse($o->delivery_date)->format('Y-m-d') === $dayStr
                    );
                @endphp
                <div class="flex flex-col min-h-[200px] rounded-2xl overflow-hidden border
                    {{ $isToday ? 'border-blue-400 ring-2 ring-blue-200 shadow-lg' : ($isPast ? 'border-gray-100 opacity-75' : 'border-gray-100') }}"
                    style="width: calc(100% / 7);">

                    <div class="px-2 py-2.5 text-center font-black text-xs
                        {{ $isToday ? 'bg-blue-600 text-white' : ($isPast ? 'bg-gray-50 text-gray-400' : 'bg-white/80 text-blue-900') }}">
                        <div>{{ $dayLabel }}</div>
                        <div class="text-xl font-black mt-0.5 {{ $isToday ? 'text-white' : ($isPast ? 'text-gray-300' : 'text-blue-800') }}">{{ $day->format('d') }}</div>
                        @if($dayOrders->count() > 0)
                            <div class="inline-block mt-1 text-[10px] font-black px-2 py-0.5 rounded-full
                                {{ $isToday ? 'bg-white text-blue-600' : 'bg-blue-100 text-blue-700' }}">
                                {{ $dayOrders->count() }}
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 p-1.5 space-y-1.5 bg-white/50">
                        @forelse($dayOrders as $order)
                            @php
                                $oDone    = $order->status === 'completed';
                                $oLate    = !$oDone && $dayStr < $todayStr;
                            @endphp
                            <a href="{{ route('orders.show', $order) }}"
                               class="block p-2 rounded-xl border text-[11px] font-bold transition hover:shadow-md hover:-translate-y-0.5
                                {{ $oDone ? 'bg-green-50 border-green-200 text-green-800' : ($oLate ? 'bg-red-50 border-red-200 text-red-800' : 'bg-blue-50 border-blue-100 text-blue-900') }}">
                                <div class="flex items-center gap-1 flex-wrap leading-snug">
                                    @if($order->client->is_traveler)<span>✈️</span>@endif
                                    <span class="truncate max-w-full">{{ $order->client->name }}</span>
                                </div>
                                <div class="text-[10px] opacity-60 mt-0.5 truncate">#{{ $order->order_code }}</div>
                                <div class="mt-1 flex items-center justify-between text-[10px]">
                                    <span>{{ $oDone ? '✅' : ($oLate ? '⏰' : '🔄') }}</span>
                                    <span>{{ $order->is_fully_paid ? '💰' : '💸' }}</span>
                                </div>
                            </a>
                        @empty
                            <div class="text-center text-gray-200 text-xs pt-6 select-none">—</div>
                        @endforelse
                    </div>
                </div>
            @endfor
        </div>

        {{-- Mobile/Tablet: one card per day --}}
        <div class="lg:hidden space-y-3">
            @for($i = 0; $i < 7; $i++)
                @php
                    $day      = $weekStart->copy()->addDays($i);
                    $dayStr   = $day->format('Y-m-d');
                    $isToday  = $dayStr === $todayStr;
                    $isPast   = $dayStr < $todayStr;
                    $dayLabel = $day->isoFormat('ddd');
                    $dayOrders = $weekOrders->filter(fn($o) =>
                        $o->delivery_date && \Carbon\Carbon::parse($o->delivery_date)->format('Y-m-d') === $dayStr
                    );
                @endphp
                @if($dayOrders->isNotEmpty() || $isToday)
                <div class="glass-card overflow-hidden {{ $isToday ? 'ring-2 ring-blue-300' : '' }}">
                    <div class="px-4 py-3 flex items-center gap-3
                        {{ $isToday ? 'bg-blue-600' : ($isPast ? 'bg-gray-50' : 'bg-white/70') }}">
                        <div class="text-2xl font-black {{ $isToday ? 'text-white' : ($isPast ? 'text-gray-300' : 'text-blue-900') }}">{{ $day->format('d') }}</div>
                        <div>
                            <div class="font-black text-sm {{ $isToday ? 'text-white' : ($isPast ? 'text-gray-400' : 'text-blue-900') }}">{{ $dayLabel }}</div>
                            <div class="text-xs {{ $isToday ? 'text-blue-100' : 'text-gray-400' }}">{{ $day->format('Y-m-d') }}</div>
                        </div>
                        @if($dayOrders->count() > 0)
                            <span class="mr-auto text-xs font-black px-2.5 py-1 rounded-full {{ $isToday ? 'bg-white text-blue-600' : 'bg-blue-100 text-blue-700' }}">
                                {{ $dayOrders->count() }} طلب
                            </span>
                        @endif
                    </div>

                    @if($dayOrders->isEmpty())
                        <div class="px-4 py-3 text-xs text-gray-400 text-center font-bold">لا توجد مواعيد</div>
                    @else
                        <div class="divide-y divide-gray-50">
                            @foreach($dayOrders as $order)
                                @php
                                    $oDone = $order->status === 'completed';
                                    $oLate = !$oDone && $dayStr < $todayStr;
                                @endphp
                                <a href="{{ route('orders.show', $order) }}"
                                   class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50/30 transition
                                    {{ $oDone ? 'bg-green-50/20' : ($oLate ? 'bg-red-50/20' : '') }}">
                                    @if($order->client->image)
                                        <img src="{{ asset('app-storage/' . $order->client->image) }}" class="w-10 h-10 rounded-full object-cover border-2 border-gray-200 shrink-0">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-black text-sm border-2 border-blue-200 shrink-0">{{ mb_substr($order->client->name,0,1) }}</div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <div class="font-bold text-gray-800 text-sm">
                                            {{ $order->client->name }}
                                            @if($order->client->is_traveler)<span class="text-orange-500">✈️</span>@endif
                                        </div>
                                        <div class="text-xs text-gray-400">#{{ $order->order_code }} · {{ $order->itemCategory->name ?? '-' }}</div>
                                    </div>
                                    <div class="shrink-0 flex flex-col items-end gap-1">
                                        <span class="text-[10px] font-black px-2 py-0.5 rounded-full border
                                            {{ $oDone ? 'bg-green-100 text-green-700 border-green-200' : ($oLate ? 'bg-red-100 text-red-700 border-red-200' : 'bg-yellow-50 text-yellow-700 border-yellow-200') }}">
                                            {{ $oDone ? '✅' : ($oLate ? '⏰' : '🔄') }}
                                        </span>
                                        <span class="text-[10px] font-bold {{ $order->is_fully_paid ? 'text-green-600' : 'text-orange-500' }}">
                                            {{ $order->is_fully_paid ? '💰 خالص' : '💸 باقي' }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
                @endif
            @endfor
        </div>

        @if($weekOrders->isEmpty())
        <div class="text-center py-16 glass-card">
            <div class="text-5xl mb-3 opacity-30">🗓️</div>
            <h3 class="text-lg font-black text-gray-600 mb-1">مفيش مواعيد في هذا الأسبوع</h3>
            <p class="text-sm text-gray-400">استخدم الأزرار للتنقل بين الأسابيع</p>
        </div>
        @endif

    </div>
</x-app-layout>
