<x-app-layout>
    <x-slot name="header">الرئيسية ✦ أهلاً بك في الأتيليه</x-slot>

    <div class="py-6 space-y-8">

        {{-- ===== Stats Row ===== --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="glass-card p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0">📋</div>
                <div>
                    <p class="text-xs text-gray-500 font-bold">إجمالي الطلبات</p>
                    <h3 class="text-2xl font-black text-blue-900">{{ $totalOrders }}</h3>
                </div>
            </div>
            <div class="glass-card p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-100 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0">👗</div>
                <div>
                    <p class="text-xs text-gray-500 font-bold">العميلات</p>
                    <h3 class="text-2xl font-black text-blue-900">{{ $totalClients }}</h3>
                </div>
            </div>
            <div class="glass-card p-5 flex items-center gap-4
                        {{ $overdueOrders->count() > 0 ? 'border-red-200 bg-red-50/60' : '' }}">
                <div class="w-12 h-12 {{ $overdueOrders->count() > 0 ? 'bg-red-100' : 'bg-green-100' }} rounded-2xl flex items-center justify-center text-2xl flex-shrink-0">
                    {{ $overdueOrders->count() > 0 ? '⏰' : '✅' }}
                </div>
                <div>
                    <p class="text-xs {{ $overdueOrders->count() > 0 ? 'text-red-500' : 'text-gray-500' }} font-bold">متأخرة</p>
                    <h3 class="text-2xl font-black {{ $overdueOrders->count() > 0 ? 'text-red-600' : 'text-green-700' }}">{{ $overdueOrders->count() }}</h3>
                </div>
            </div>
            <div class="glass-card p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0">💰</div>
                <div>
                    <p class="text-xs text-gray-500 font-bold">إجمالي الإيرادات</p>
                    <h3 class="text-xl font-black text-blue-900">{{ number_format($totalRevenue) }} <span class="text-sm font-bold">ج.م</span></h3>
                </div>
            </div>
        </div>

        {{-- ===== Overdue Alert ===== --}}
        @if($overdueOrders->count() > 0)
        <div class="glass-card border-red-200 ring-2 ring-red-100 overflow-hidden">
            <div class="bg-gradient-to-r from-red-500 to-rose-500 text-white px-6 py-3 flex items-center justify-between">
                <h3 class="font-black text-base flex items-center gap-2">⏰ طلبات متأخرة — {{ $overdueOrders->count() }}</h3>
                <a href="{{ route('appointments.index') }}" class="text-xs bg-white/20 hover:bg-white/40 px-3 py-1 rounded-lg font-bold transition">عرض الكل</a>
            </div>
            <div class="divide-y divide-red-50">
                @foreach($overdueOrders->take(5) as $order)
                <a href="{{ route('orders.show', $order) }}"
                   class="flex items-center gap-4 px-5 py-3 hover:bg-red-50/40 transition">
                    @if($order->client->image)
                        <img src="{{ asset('app-storage/' . $order->client->image) }}" class="w-9 h-9 rounded-full object-cover border-2 border-red-200 shrink-0">
                    @else
                        <div class="w-9 h-9 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-black text-sm border-2 border-red-200 shrink-0">{{ mb_substr($order->client->name, 0, 1) }}</div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-gray-800 text-sm flex items-center gap-2">
                            {{ $order->client->name }}
                            @if($order->client->is_traveler)
                                <span class="text-[10px] bg-orange-100 text-orange-600 border border-orange-200 px-1.5 py-0.5 rounded-full font-bold">✈️ سفر</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-400">#{{ $order->order_code }} &bull; {{ $order->itemCategory->name ?? '-' }}</div>
                    </div>
                    <div class="text-left shrink-0">
                        <div class="text-xs font-black text-red-600 bg-red-50 px-2 py-1 rounded-lg border border-red-200">
                            {{ \Carbon\Carbon::parse($order->delivery_date)->diffForHumans() }}
                        </div>
                        <div class="text-[11px] text-gray-400 text-left mt-0.5">{{ \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d') }}</div>
                    </div>
                </a>
                @endforeach
            </div>
            @if($overdueOrders->count() > 5)
                <div class="px-5 py-3 text-center border-t border-red-100">
                    <a href="{{ route('appointments.index') }}" class="text-sm text-red-600 font-bold hover:underline">+ {{ $overdueOrders->count() - 5 }} طلب متأخر آخر</a>
                </div>
            @endif
        </div>
        @endif

        {{-- ===== Today + Upcoming ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Today's Deliveries --}}
            <div class="glass-card overflow-hidden">
                <div class="px-5 py-4 border-b border-blue-50 flex items-center justify-between bg-blue-50/30">
                    <h3 class="font-black text-blue-900 flex items-center gap-2">
                        🎯 تسليمات اليوم
                        @if($deliveriesToday->count() > 0)
                            <span class="bg-blue-600 text-white text-xs px-2 py-0.5 rounded-full">{{ $deliveriesToday->count() }}</span>
                        @endif
                    </h3>
                    <a href="{{ route('appointments.index') }}" class="text-xs text-blue-600 font-bold hover:underline">الجدول الكامل</a>
                </div>
                @forelse($deliveriesToday as $order)
                    <a href="{{ route('orders.show', $order) }}" class="flex items-center gap-3 px-5 py-3.5 border-b border-gray-50 hover:bg-blue-50/30 transition last:border-0">
                        @if($order->client->image)
                            <img src="{{ asset('app-storage/' . $order->client->image) }}" class="w-9 h-9 rounded-full object-cover border-2 border-blue-100 shrink-0">
                        @else
                            <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-black text-sm border-2 border-blue-200 shrink-0">{{ mb_substr($order->client->name, 0, 1) }}</div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-gray-800 text-sm flex items-center gap-1.5">
                                {{ $order->client->name }}
                                @if($order->client->is_traveler)
                                    <span class="text-[10px] text-orange-600 bg-orange-100 border border-orange-200 px-1.5 py-0.5 rounded-full">✈️</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-400">#{{ $order->order_code }} &bull; {{ $order->itemCategory->name ?? '-' }}</div>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full border {{ $order->is_fully_paid ? 'bg-green-100 text-green-700 border-green-200' : 'bg-orange-50 text-orange-600 border-orange-200' }}">
                            {{ $order->is_fully_paid ? '✅ خالص' : '💸 باقي' }}
                        </span>
                    </a>
                @empty
                    <div class="text-center py-10 text-gray-400">
                        <div class="text-3xl mb-2 opacity-30">🎉</div>
                        <p class="text-sm font-bold">مفيش تسليمات اليوم</p>
                    </div>
                @endforelse
            </div>

            {{-- Upcoming Deliveries 48h --}}
            <div class="glass-card overflow-hidden">
                <div class="px-5 py-4 border-b border-amber-50 flex items-center justify-between bg-amber-50/30">
                    <h3 class="font-black text-amber-800 flex items-center gap-2">
                        📅 في اليومين القادمين
                        @if($upcomingDeliveries->count() > 0)
                            <span class="bg-amber-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $upcomingDeliveries->count() }}</span>
                        @endif
                    </h3>
                    <a href="{{ route('appointments.index') }}" class="text-xs text-amber-600 font-bold hover:underline">الجدول الكامل</a>
                </div>
                @forelse($upcomingDeliveries as $order)
                    <a href="{{ route('orders.show', $order) }}" class="flex items-center gap-3 px-5 py-3.5 border-b border-gray-50 hover:bg-amber-50/20 transition last:border-0">
                        @if($order->client->image)
                            <img src="{{ asset('app-storage/' . $order->client->image) }}" class="w-9 h-9 rounded-full object-cover border-2 border-amber-100 shrink-0">
                        @else
                            <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-black text-sm border-2 border-amber-200 shrink-0">{{ mb_substr($order->client->name, 0, 1) }}</div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-gray-800 text-sm flex items-center gap-1.5">
                                {{ $order->client->name }}
                                @if($order->client->is_traveler)
                                    <span class="text-[10px] text-orange-600 bg-orange-100 border border-orange-200 px-1.5 py-0.5 rounded-full">✈️</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-400">#{{ $order->order_code }} &bull; {{ $order->itemCategory->name ?? '-' }}</div>
                        </div>
                        <div class="text-left shrink-0">
                            <div class="text-xs font-black text-amber-700 bg-amber-50 px-2 py-1 rounded-lg border border-amber-200">
                                {{ \Carbon\Carbon::parse($order->delivery_date)->diffForHumans() }}
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-10 text-gray-400">
                        <div class="text-3xl mb-2 opacity-30">📅</div>
                        <p class="text-sm font-bold">مفيش مواعيد في اليومين القادمين</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ===== Next 5 Pending Orders ===== --}}
        @if($pendingOrders->count() > 0)
        <div class="glass-card overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-black text-blue-900 flex items-center gap-2">🔄 طلبات جارية (أقرب مواعيد)</h3>
                <a href="{{ route('orders.index') }}" class="text-xs text-blue-600 font-bold hover:underline">عرض الكل</a>
            </div>
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-gray-50/50 text-xs text-gray-500 font-bold border-b border-gray-100">
                        <tr>
                            <th class="py-3 px-4">الكود</th>
                            <th class="py-3 px-4">العميلة</th>
                            <th class="py-3 px-4">النوع</th>
                            <th class="py-3 px-4">التسليم</th>
                            <th class="py-3 px-4 text-center">الدفع</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingOrders as $order)
                            <tr class="border-b border-gray-50 hover:bg-blue-50/20 transition">
                                <td class="py-3 px-4">
                                    <a href="{{ route('orders.show', $order) }}" class="font-bold text-blue-700 hover:underline">#{{ $order->order_code }}</a>
                                </td>
                                <td class="py-3 px-4 font-bold text-gray-700 text-sm">{{ $order->client->name }}</td>
                                <td class="py-3 px-4">
                                    <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-lg text-xs border border-gray-200">{{ $order->itemCategory->name ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-4 text-sm font-bold text-blue-900">{{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d') : '-' }}</td>
                                <td class="py-3 px-4 text-center">
                                    <span class="text-xs font-bold px-2 py-1 rounded-full border {{ $order->is_fully_paid ? 'bg-green-100 text-green-700 border-green-200' : 'bg-orange-50 text-orange-600 border-orange-200' }}">
                                        {{ $order->is_fully_paid ? '✅ خالص' : '💸 باقي' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="md:hidden divide-y divide-gray-50">
                @foreach($pendingOrders as $order)
                    <a href="{{ route('orders.show', $order) }}" class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50/20 transition">
                        <div class="font-bold text-blue-700 text-sm">#{{ $order->order_code }}</div>
                        <div class="flex-1 text-sm text-gray-700">{{ $order->client->name }}</div>
                        <div class="text-xs text-blue-900 font-bold">{{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d') : '-' }}</div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</x-app-layout>
