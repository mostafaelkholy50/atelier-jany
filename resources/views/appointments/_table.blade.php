{{-- Shared reusable appointments table --}}
{{-- Variables: $orders, $emptyText, $dateField (default: delivery_date), $dateLabel, $showStatus --}}
@php
    $dateField  = $dateField  ?? 'delivery_date';
    $dateLabel  = $dateLabel  ?? 'تاريخ التسليم';
    $showStatus = $showStatus ?? false;
@endphp

@if($orders->isEmpty())
    @if(!empty($emptyText))
        <div class="text-center py-10 text-gray-400">
            <div class="text-3xl mb-2 opacity-30">📭</div>
            <p class="text-sm font-bold">{{ $emptyText }}</p>
        </div>
    @endif
@else
    {{-- Desktop Table --}}
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-right">
            <thead class="bg-gray-50/60 text-xs text-gray-500 font-bold border-b border-gray-100">
                <tr>
                    <th class="py-3 px-4">الكود</th>
                    <th class="py-3 px-4">العميلة</th>
                    <th class="py-3 px-4">النوع</th>
                    <th class="py-3 px-4">{{ $dateLabel }}</th>
                    <th class="py-3 px-4">التسليم النهائي</th>
                    <th class="py-3 px-4 text-center">الدفع</th>
                    @if($showStatus)<th class="py-3 px-4 text-center">الحالة</th>@endif
                    <th class="py-3 px-4"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    @php
                        $isOverdue   = $order->delivery_date && \Carbon\Carbon::parse($order->delivery_date)->lt(\Carbon\Carbon::today()) && $order->status !== 'completed';
                        $isCompleted = $order->status === 'completed';
                        $isToday     = $order->delivery_date && \Carbon\Carbon::parse($order->delivery_date)->isToday();
                        $rowBg = $isOverdue ? 'bg-red-50/50' : ($isCompleted ? 'bg-green-50/40' : ($isToday ? 'bg-blue-50/40' : ''));
                        $dateVal = $order->$dateField;
                    @endphp
                    <tr class="border-b border-gray-50 hover:bg-blue-50/20 transition {{ $rowBg }}">
                        <td class="py-3 px-4">
                            <a href="{{ route('orders.show', $order) }}" class="font-black text-blue-700 hover:underline bg-blue-50 px-2 py-1 rounded-lg border border-blue-100 text-sm">
                                #{{ $order->order_code }}
                            </a>
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('orders.show', $order) }}" class="flex items-center gap-2 hover:opacity-80 transition">
                                @if($order->client->image)
                                    <img src="{{ asset('app-storage/' . $order->client->image) }}" class="w-8 h-8 rounded-full object-cover border-2 border-gray-200 shrink-0">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-black text-xs border-2 border-blue-200 shrink-0">{{ mb_substr($order->client->name, 0, 1) }}</div>
                                @endif
                                <div>
                                    <div class="font-bold text-gray-800 text-sm flex items-center gap-1">
                                        {{ $order->client->name }}
                                        @if($order->client->is_traveler)
                                            <span class="text-[10px] text-orange-600 bg-orange-100 border border-orange-200 px-1.5 py-0.5 rounded-full">✈️</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-gray-400">{{ $order->client->phone ?? '' }}</div>
                                </div>
                            </a>
                        </td>
                        <td class="py-3 px-4">
                            <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-lg border border-gray-200 font-bold">{{ $order->itemCategory->name ?? '-' }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('orders.show', $order) }}" class="font-black text-sm {{ $isOverdue ? 'text-red-600' : ($isToday ? 'text-blue-700' : 'text-gray-700') }} hover:underline">
                                {{ $dateVal ? \Carbon\Carbon::parse($dateVal)->format('Y-m-d') : '-' }}
                            </a>
                        </td>
                        <td class="py-3 px-4 font-bold text-sm text-gray-600">
                            {{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d') : '-' }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full border {{ $order->is_fully_paid ? 'bg-green-100 text-green-700 border-green-200' : 'bg-orange-50 text-orange-600 border-orange-200' }}">
                                {{ $order->is_fully_paid ? '✅ خالص' : '💸 باقي' }}
                            </span>
                        </td>
                        @if($showStatus)
                        <td class="py-3 px-4 text-center">
                            @if($isOverdue)
                                <span class="text-xs bg-red-100 text-red-700 border border-red-200 px-2 py-1 rounded-full font-bold">⏰ متأخر</span>
                            @elseif($isCompleted)
                                <span class="text-xs bg-green-100 text-green-700 border border-green-200 px-2 py-1 rounded-full font-bold">✅ مكتمل</span>
                            @elseif($isToday)
                                <span class="text-xs bg-blue-100 text-blue-700 border border-blue-200 px-2 py-1 rounded-full font-bold">🎯 اليوم</span>
                            @else
                                <span class="text-xs bg-yellow-50 text-yellow-700 border border-yellow-200 px-2 py-1 rounded-full font-bold">🔄 جاري</span>
                            @endif
                        </td>
                        @endif
                        <td class="py-3 px-4">
                            <div class="flex gap-1.5 justify-end">
                                <a href="{{ route('orders.show', $order) }}" class="p-1.5 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded-lg hover:bg-indigo-600 hover:text-white transition text-xs">👁️</a>
                                <a href="{{ route('orders.edit', $order) }}" class="p-1.5 bg-blue-50 text-blue-600 border border-blue-100 rounded-lg hover:bg-blue-600 hover:text-white transition text-xs">✏️</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden divide-y divide-gray-50">
        @foreach($orders as $order)
            @php
                $isOverdue = $order->delivery_date && \Carbon\Carbon::parse($order->delivery_date)->lt(\Carbon\Carbon::today()) && $order->status !== 'completed';
                $isToday   = $order->delivery_date && \Carbon\Carbon::parse($order->delivery_date)->isToday();
                $dateVal   = $order->$dateField;
            @endphp
            <a href="{{ route('orders.show', $order) }}"
               class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50/20 transition {{ $isOverdue ? 'bg-red-50/30' : ($isToday ? 'bg-blue-50/30' : '') }}">
                @if($order->client->image)
                    <img src="{{ asset('app-storage/' . $order->client->image) }}" class="w-10 h-10 rounded-full object-cover border-2 border-gray-200 shrink-0">
                @else
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-black text-sm border-2 border-blue-200 shrink-0">{{ mb_substr($order->client->name, 0, 1) }}</div>
                @endif
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-gray-800 text-sm flex items-center gap-1">
                        {{ $order->client->name }}
                        @if($order->client->is_traveler)
                            <span class="text-[10px] text-orange-600">✈️</span>
                        @endif
                    </div>
                    <div class="text-xs text-gray-400">#{{ $order->order_code }} &bull; {{ $order->itemCategory->name ?? '-' }}</div>
                    <div class="text-xs font-bold {{ $isOverdue ? 'text-red-600' : 'text-blue-800' }} mt-0.5">
                        {{ $dateVal ? \Carbon\Carbon::parse($dateVal)->format('Y-m-d') : '-' }}
                    </div>
                </div>
                <div class="shrink-0">
                    @if($isOverdue)
                        <span class="text-[10px] bg-red-100 text-red-700 border border-red-200 px-2 py-0.5 rounded-full font-bold block mb-1">⏰</span>
                    @elseif($isToday)
                        <span class="text-[10px] bg-blue-100 text-blue-700 border border-blue-200 px-2 py-0.5 rounded-full font-bold block mb-1">🎯</span>
                    @endif
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold border block {{ $order->is_fully_paid ? 'bg-green-100 text-green-700 border-green-200' : 'bg-orange-50 text-orange-600 border-orange-200' }}">
                        {{ $order->is_fully_paid ? '✅' : '💸' }}
                    </span>
                </div>
            </a>
        @endforeach
    </div>
@endif
