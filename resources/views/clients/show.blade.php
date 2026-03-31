<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="font-bold text-xl text-blue-900 flex items-center gap-2">
                    <span>👗</span> {{ $client->name }}
                </span>
                @if($client->is_traveler)
                    <span class="inline-flex items-center gap-1.5 bg-gradient-to-r from-orange-400 to-amber-400 text-red-500 text-xs font-black px-3 py-1.5 rounded-full shadow-md shadow-orange-100">
                        ✈️ من سفر / استعجال
                    </span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-white text-gray-700 font-bold rounded-xl border border-gray-200 shadow-sm hover:bg-gray-50 transition flex items-center gap-2 text-sm">
                    🔙 الرجوع
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">

        @if(session('success'))
            <div class="px-5 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl font-bold text-sm flex items-center gap-2">
                ✅ {{ session('success') }}
            </div>
        @endif

        {{-- Profile Card --}}
        <div class="glass-card {{ $client->is_traveler ? 'border-orange-200 ring-2 ring-orange-100' : '' }} overflow-hidden">

            {{-- Traveler Banner --}}
            @if($client->is_traveler)
                <div class="w-full bg-gradient-to-r from-orange-400 to-amber-400 text-red-500 text-sm font-black text-center py-2 flex items-center justify-center gap-2">
                    ✈️ هذه العميلة من سفر — يُرجى الاستعجال في التنفيذ
                </div>
            @endif

            <div class="p-6 md:p-8">
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">

                    {{-- Avatar --}}
                    @if($client->image)
                        <img src="{{ asset('app-storage/' . $client->image) }}"
                             onclick="openImageModal(this.src)"
                             class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-xl ring-4 {{ $client->is_traveler ? 'ring-orange-200' : 'ring-blue-100' }} shrink-0 cursor-zoom-in hover:scale-105 transition duration-300">
                    @else
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-200 to-indigo-400 text-white flex items-center justify-center font-black text-4xl shadow-xl border-4 border-white ring-4 {{ $client->is_traveler ? 'ring-orange-200' : 'ring-blue-100' }} shrink-0">
                            {{ mb_substr($client->name, 0, 1) }}
                        </div>
                    @endif

                    {{-- Info --}}
                    <div class="flex-1 text-center sm:text-right">
                        <h2 class="text-2xl font-black text-gray-800 mb-1">{{ $client->name }}</h2>
                        @if($client->phone)
                            <a href="tel:{{ $client->phone }}" class="inline-flex items-center gap-1.5 text-gray-500 hover:text-blue-600 transition text-sm bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100 mb-3">
                                📞 {{ $client->phone }}
                            </a>
                        @endif

                        <div class="flex flex-wrap gap-3 justify-center sm:justify-start mt-3">
                            <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-2 text-center">
                                <div class="text-xl font-black text-blue-800">{{ $client->orders->count() }}</div>
                                <div class="text-xs text-gray-500 font-bold">إجمالي الطلبات</div>
                            </div>
                            @php
                                $totalSpent = $client->orders->sum('total_price');
                                $completed  = $client->orders->where('status', 'completed')->count();
                                $pending    = $client->orders->where('status', '!=', 'completed')->count();
                            @endphp
                            <div class="bg-green-50 border border-green-100 rounded-xl px-4 py-2 text-center">
                                <div class="text-xl font-black text-green-700">{{ $totalSpent }}</div>
                                <div class="text-xs text-gray-500 font-bold">ج.م إجمالي</div>
                            </div>
                            <div class="bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-2 text-center">
                                <div class="text-xl font-black text-indigo-700">{{ $completed }}</div>
                                <div class="text-xs text-gray-500 font-bold">مكتمل</div>
                            </div>
                            @if($pending > 0)
                            <div class="bg-yellow-50 border border-yellow-100 rounded-xl px-4 py-2 text-center">
                                <div class="text-xl font-black text-yellow-600">{{ $pending }}</div>
                                <div class="text-xs text-gray-500 font-bold">جاري</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ===== Action Buttons ===== --}}
                <div class="flex flex-wrap gap-3 mt-6 border-t border-gray-100 pt-6">
                    <a href="{{ route('clients.edit', $client) }}"
                       class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-200 transition-all transform hover:-translate-y-0.5 text-sm">
                        ✏️ تعديل البيانات
                    </a>
                    <a href="{{ route('orders.create') }}"
                       class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white font-bold rounded-2xl border border-indigo-200 transition-all transform hover:-translate-y-0.5 text-sm">
                        📋 إضافة طلب جديد
                    </a>
                    <form action="{{ route('clients.destroy', $client) }}" method="POST"
                          onsubmit="return confirm('متأكد إنك عايز تمسح العميلة دي؟ هيتمسح كل بياناتها!')"
                          class="flex-none mr-auto">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-3 bg-red-50 hover:bg-red-500 text-red-600 hover:text-white font-bold rounded-2xl border border-red-200 transition text-sm">
                            🗑️ حذف العميلة
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Latest Measurements --}}
        @if($client->orders->count() > 0)
            @php
                $latestOrder = $client->orders->sortByDesc('created_at')->first();
                $measurements = $latestOrder->measurements ?? [];
            @endphp
            @if(!empty($measurements))
            <div class="glass-card p-6">
                <h3 class="font-bold text-blue-900 text-lg mb-4 flex items-center gap-2">
                    <span class="bg-blue-100 p-2 rounded-xl text-xl">📏</span>
                    آخر مقاسات مسجلة
                    <span class="text-xs text-gray-400 font-normal mr-2">(من طلب #{{ $latestOrder->order_code }})</span>
                </h3>
                <div class="flex flex-wrap gap-3">
                    @foreach($measurements as $name => $value)
                        <div class="bg-white border-2 border-gray-100 hover:border-blue-200 px-4 py-3 rounded-2xl flex flex-col items-center gap-1 min-w-[90px] shadow-sm transition hover:-translate-y-0.5">
                            <span class="text-gray-400 text-[11px] font-bold">{{ $name }}</span>
                            <span class="text-indigo-800 font-black text-lg">{{ $value ?: '-' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endif

        {{-- Orders History --}}
        <div class="glass-card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-blue-900 text-lg flex items-center gap-2">
                    <span class="bg-blue-100 p-1.5 rounded-xl text-xl">📋</span>
                    سجل الطلبات
                </h3>
                <a href="{{ route('orders.create') }}" class="text-sm bg-blue-600 text-white px-4 py-2 rounded-xl font-bold hover:bg-blue-700 transition shadow-sm">
                    + طلب جديد
                </a>
            </div>

            @if($client->orders->isEmpty())
                <div class="text-center py-12 text-gray-400">
                    <div class="text-4xl mb-3 opacity-30">📭</div>
                    <p class="font-bold text-gray-500">مفيش طلبات لهذه العميلة لسه</p>
                </div>
            @else
                {{-- Desktop Table --}}
                <div class="hidden md:block">
                    <table class="w-full text-right">
                        <thead class="bg-blue-50/50 text-blue-900 text-xs font-bold border-b border-blue-100">
                            <tr>
                                <th class="py-3 px-4">الكود</th>
                                <th class="py-3 px-4">النوع</th>
                                <th class="py-3 px-4">تاريخ التسليم</th>
                                <th class="py-3 px-4">الحساب</th>
                                <th class="py-3 px-4 text-center">الحالة</th>
                                <th class="py-3 px-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($client->orders->sortByDesc('created_at') as $order)
                                @php
                                    $isOverdue   = $order->delivery_date && \Carbon\Carbon::parse($order->delivery_date)->isPast() && $order->status !== 'completed';
                                    $isCompleted = $order->status === 'completed';
                                    $rowBg = $isOverdue ? 'bg-red-50/50' : ($isCompleted ? 'bg-green-50/50' : '');
                                @endphp
                                <tr class="border-b border-gray-50 hover:bg-blue-50/20 transition {{ $rowBg }}">
                                    <td class="py-3 px-4 font-bold text-blue-700">#{{ $order->order_code }}</td>
                                    <td class="py-3 px-4">
                                        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-lg text-xs font-bold border border-gray-200">{{ $order->itemCategory->name ?? '-' }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-sm font-bold {{ $isOverdue ? 'text-red-600' : 'text-gray-700' }}">
                                        {{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d') : '-' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="text-sm font-black text-blue-900">{{ $order->total_price }} ج.م</div>
                                        @php $remaining = max(0, $order->total_price - ($order->deposit ?? 0)); @endphp
                                        @if($remaining > 0)
                                            <div class="text-xs text-red-500 font-bold">متبقي: {{ $remaining }} ج.م</div>
                                        @else
                                            <div class="text-xs text-green-600 font-bold">خالص ✅</div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($isOverdue)
                                            <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full border border-red-200 font-bold">⏰ متأخر</span>
                                        @elseif($isCompleted)
                                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full border border-green-200 font-bold">✅ مكتمل</span>
                                        @else
                                            <span class="bg-yellow-50 text-yellow-700 text-xs px-2 py-1 rounded-full border border-yellow-200 font-bold">🔄 جاري</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex gap-1.5 justify-end">
                                            <a href="{{ route('orders.show', $order) }}" class="p-1.5 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded-lg hover:bg-indigo-600 hover:text-white transition text-xs font-bold">👁️</a>
                                            <a href="{{ route('orders.edit', $order) }}" class="p-1.5 bg-blue-50 text-blue-600 border border-blue-100 rounded-lg hover:bg-blue-600 hover:text-white transition text-xs font-bold">✏️</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden divide-y divide-gray-50">
                    @foreach($client->orders->sortByDesc('created_at') as $order)
                        @php
                            $isOverdue   = $order->delivery_date && \Carbon\Carbon::parse($order->delivery_date)->isPast() && $order->status !== 'completed';
                            $isCompleted = $order->status === 'completed';
                        @endphp
                        <div class="p-4 flex items-center gap-3 {{ $isOverdue ? 'bg-red-50/30' : ($isCompleted ? 'bg-green-50/30' : '') }}">
                            <div class="flex-1">
                                <div class="font-bold text-blue-700 text-sm">#{{ $order->order_code }}</div>
                                <div class="text-xs text-gray-500">{{ $order->itemCategory->name ?? '-' }} &bull; {{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d') : '-' }}</div>
                                <div class="font-black text-sm text-blue-900 mt-0.5">{{ $order->total_price }} ج.م</div>
                            </div>
                            <div class="flex flex-col gap-1.5 items-end">
                                @if($isOverdue)
                                    <span class="text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-bold border border-red-200">⏰ متأخر</span>
                                @elseif($isCompleted)
                                    <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold border border-green-200">✅ مكتمل</span>
                                @else
                                    <span class="text-[10px] bg-yellow-50 text-yellow-700 px-2 py-0.5 rounded-full font-bold border border-yellow-200">🔄 جاري</span>
                                @endif
                                <div class="flex gap-1.5">
                                    <a href="{{ route('orders.show', $order) }}" class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg border border-indigo-100 text-xs font-bold">👁️</a>
                                    <a href="{{ route('orders.edit', $order) }}" class="p-1.5 bg-blue-50 text-blue-600 rounded-lg border border-blue-100 text-xs font-bold">✏️</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
