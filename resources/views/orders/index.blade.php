<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center w-full gap-4">
            <span class="font-bold text-xl text-blue-900 flex items-center gap-2"><span>📋</span> إدارة الطلبات</span>
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto mt-2 md:mt-0">
                <form id="searchForm" method="GET" action="{{ route('orders.index') }}" class="w-full sm:flex-1 md:w-64">
                    <div class="relative">
                        <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو الكود..." class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition pl-10 h-10 text-sm shadow-sm" autocomplete="off">
                        <span id="searchIcon" class="absolute left-3 top-2.5 opacity-50 pointer-events-none">🔍</span>
                        <span id="searchSpinner" class="absolute left-3 top-2.5 hidden">
                            <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        </span>
                    </div>
                </form>
                <a href="{{ route('orders.create') }}" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition shadow-lg shadow-blue-200 whitespace-nowrap flex items-center justify-center">
                    + إضافة طلب
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="orderIndex()">
        <div class="glass-card shadow-sm" id="tableContainer">
            @if(request()->filled('search'))
                <div class="px-6 pt-4 pb-2 text-sm text-gray-600 bg-blue-50/30 border-b border-blue-50 flex items-center gap-2">
                    نتائج البحث عن: <strong class="text-blue-900" x-text="searchQuery"></strong>
                    <button type="button" @click="searchQuery=''; performSearch()" class="text-red-500 hover:underline text-xs mr-auto bg-white px-2 py-1 rounded-md border border-red-100 shadow-sm">&times; إلغاء البحث</button>
                </div>
            @endif
            
            <div class="p-4 md:p-6 transition-opacity duration-300" :class="{ 'opacity-30 pointer-events-none': isSearching }">
                <div id="resultsContainer">
                
                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-hidden rounded-xl border border-blue-50">
                    <table class="w-full text-right border-collapse bg-white">
                        <thead>
                            <tr class="border-b border-blue-100 text-blue-900 bg-blue-50/50">
                            <th class="py-4 px-3 font-semibold text-sm">كود الطلب</th>
                            <th class="py-4 px-3 font-semibold text-sm">العميلة</th>
                            <th class="py-4 px-3 font-semibold text-sm">نوع القطعة</th>
                            <th class="py-4 px-3 font-semibold text-sm">المواعيد</th>
                            <th class="py-4 px-3 font-semibold text-sm">الحساب</th>
                            <th class="py-4 px-3 font-semibold text-sm text-center">حالة الدفع</th>
                            <th class="py-4 px-3 font-semibold text-sm text-center">التسليم</th>
                            <th class="py-4 px-3 font-semibold text-sm text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @foreach ($orders as $order)
                            @php
                                $isOverdue = $order->delivery_date && \Carbon\Carbon::parse($order->delivery_date)->isPast() && $order->status !== 'completed';
                                $isCompleted = $order->status === 'completed';
                                $rowBg = $isOverdue
                                    ? 'bg-red-50/60 border-red-100 hover:bg-red-50'
                                    : ($isCompleted
                                        ? 'bg-green-50/60 border-green-100 hover:bg-green-50'
                                        : 'bg-white border-gray-100 hover:bg-blue-50/40');
                                $cardBg = $isOverdue
                                    ? 'bg-red-50 border-red-200'
                                    : ($isCompleted
                                        ? 'bg-green-50 border-green-200'
                                        : 'bg-white border-gray-100');
                                $statusBadge = $isOverdue
                                    ? ['label' => '⏰ متأخر', 'class' => 'bg-red-100 text-red-700 border-red-200']
                                    : ($isCompleted
                                        ? ['label' => '✅ تم التسليم', 'class' => 'bg-green-100 text-green-700 border-green-200']
                                        : ['label' => '🔄 جاري', 'class' => 'bg-yellow-50 text-yellow-700 border-yellow-200']);
                            @endphp
                            <tr class="border-b transition group {{ $rowBg }}" x-data="orderRow({{ $order->id }}, '{{ $order->status }}', {{ $order->is_fully_paid ? 'true' : 'false' }}, {{ $order->total_price ?? 0 }}, {{ $order->deposit ?? 0 }})">
                                <td class="py-4 px-3 font-bold text-blue-800">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $statusBadge['class'] }}">{{ $statusBadge['label'] }}</span>
                                        <a href="{{ route('orders.show', $order) }}" class="hover:underline flex items-center gap-1.5 focus:outline-none focus:text-blue-600 transition bg-blue-50 px-2 py-1 rounded-lg border border-blue-100 shadow-sm w-fit">
                                            #{{ $order->order_code }}
                                        </a>
                                    </div>
                                </td>
                                <td class="py-4 px-3">
                                    <a href="{{ route('orders.show', $order) }}" class="flex items-center gap-2 hover:bg-blue-50 p-1.5 rounded-xl transition focus:outline-none group-hover:bg-white border border-transparent group-hover:border-blue-50 group-hover:shadow-sm w-full text-right">
                                        @if($order->client->image)
                                            <img src="{{ asset('app-storage/' . $order->client->image) }}" class="w-9 h-9 rounded-full border border-gray-200 object-cover shadow-sm">
                                        @else
                                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-700 flex items-center justify-center font-bold text-sm shadow-sm border border-blue-200">{{ mb_substr($order->client->name, 0, 1) }}</div>
                                        @endif
                                        <div>
                                            <div class="font-bold text-gray-800 text-sm flex items-center gap-1.5">
                                                {{ $order->client->name }}
                                                @if($order->client->is_traveler)
                                                    <span class="text-[10px] text-orange-600 bg-orange-100 px-1.5 py-0.5 rounded-md border border-orange-200" title="من سفر / استعجال">✈️</span>
                                                @endif
                                            </div>
                                            <div class="text-[11px] text-gray-500">{{ $order->client->phone ?? 'بدون رقم' }}</div>
                                        </div>
                                    </a>
                                </td>
                                <td class="py-4 px-3">
                                    <span class="bg-gray-100 text-gray-700 text-xs px-2.5 py-1.5 rounded-lg border border-gray-200 font-medium whitespace-nowrap">{{ $order->itemCategory->name ?? 'غير معروف' }}</span>
                                </td>
                                <td class="py-4 px-3">
                                    <div class="text-[11px] text-gray-500 mb-1 flex justify-between gap-2 max-w-[120px]">
                                        <span>البروفة:</span> 
                                        <span class="font-bold text-gray-700">{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('Y-m-d') : '-' }}</span>
                                    </div>
                                    <div class="text-[11px] flex justify-between gap-2 max-w-[120px] {{ \Carbon\Carbon::parse($order->delivery_date)->isPast() && $order->status != 'completed' ? 'text-red-500 font-bold bg-red-50 px-1 rounded' : 'text-gray-500' }}">
                                        <span>التسليم:</span>
                                        <span class="font-bold {{ \Carbon\Carbon::parse($order->delivery_date)->isPast() && $order->status != 'completed' ? 'text-red-600' : 'text-blue-900/80' }}">{{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d') : '-' }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-3">
                                    <div class="text-sm font-black text-blue-900">{{ $order->total_price }} ج.م</div>
                                    <div class="text-[10px] text-gray-500" x-text="'مدفوع: ' + deposit + ' ج.م'"></div>
                                </td>
                                <td class="py-4 px-3 text-center">
                                    <label class="inline-flex items-center cursor-pointer p-1 rounded-lg hover:bg-gray-50 transition">
                                        <input type="checkbox" x-model="isPaid" @change="togglePaid()" class="w-4 h-4 text-green-500 bg-white border-gray-300 rounded focus:ring-green-400 focus:ring-2 transition shadow-sm" :disabled="isPaid || loading">
                                        <span class="ml-1 text-[11px] font-bold mr-1.5" :class="isPaid ? 'text-green-600' : 'text-orange-500'" x-text="isPaid ? 'خالص' : 'متبقي'"></span>
                                    </label>
                                </td>
                                <td class="py-4 px-3 text-center">
                                     <label class="inline-flex items-center cursor-pointer p-1 rounded-lg hover:bg-gray-50 transition">
                                        <input type="checkbox" x-model="isDelivered" @change="toggleDelivered()" class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2 transition shadow-sm" :disabled="loading">
                                        <span class="ml-1 text-[11px] font-bold mr-1.5" :class="isDelivered ? 'text-blue-700' : 'text-gray-500'" x-text="isDelivered ? 'مكتمل' : 'جاري'"></span>
                                     </label>
                                </td>
                                <td class="py-4 px-3">
                                    <div class="flex justify-center gap-2 opacity-100 lg:opacity-50 group-hover:opacity-100 transition relative z-20">
                                        <a href="{{ route('orders.show', $order) }}" class="p-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition shadow-sm border border-indigo-100" title="عرض التفاصيل الكاملة">
                                            👁️
                                        </a>
                                        <a href="{{ route('orders.edit', $order) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition shadow-sm border border-blue-100" title="تعديل">
                                            ✏️
                                        </a>
                                        <form action="{{ route('orders.destroy', $order) }}" method="POST" onsubmit="return confirm('متأكدة إنك عايزة تمسحي الطلب ده؟')" class="mb-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition shadow-sm border border-red-100" title="حذف بالكامل">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>

                <!-- Mobile & Tablet Cards View -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:hidden gap-4">
                    @foreach ($orders as $order)
                        <div class="border rounded-2xl p-4 shadow-sm relative {{ $cardBg }}" x-data="orderRow({{ $order->id }}, '{{ $order->status }}', {{ $order->is_fully_paid ? 'true' : 'false' }}, {{ $order->total_price ?? 0 }}, {{ $order->deposit ?? 0 }})">
                            <div class="flex justify-between items-start mb-3 border-b pb-3 {{ $isOverdue ? 'border-red-100' : ($isCompleted ? 'border-green-100' : 'border-gray-50') }}">
                                <a href="{{ route('orders.show', $order) }}" class="flex items-center gap-3 w-full">
                                    @if($order->client->image)
                                        <img src="{{ asset('app-storage/' . $order->client->image) }}" class="w-12 h-12 rounded-full border border-gray-200 object-cover shadow-sm">
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-700 flex items-center justify-center font-bold text-lg shadow-sm border border-blue-200">{{ mb_substr($order->client->name, 0, 1) }}</div>
                                    @endif
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-800 text-sm flex items-center gap-1.5">
                                            {{ $order->client->name }}
                                            @if($order->client->is_traveler)
                                                <span class="text-[10px] text-orange-600 bg-orange-100 px-1.5 py-0.5 rounded-md border border-orange-200">✈️</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <div class="text-xs text-blue-700 font-bold">#{{ $order->order_code }}</div>
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $statusBadge['class'] }}">{{ $statusBadge['label'] }}</span>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mb-4 bg-gray-50/50 p-3 rounded-xl border border-gray-50">
                                <div>
                                    <div class="text-[10px] text-gray-400 font-bold mb-1">النوع:</div>
                                    <div class="text-xs font-bold text-gray-700 bg-white px-2 py-1 rounded inline-block shadow-sm align-middle">{{ $order->itemCategory->name ?? 'غير معروف' }}</div>
                                </div>
                                <div class="text-left">
                                    <div class="text-sm font-black text-blue-900 mb-0.5">{{ $order->total_price }} ج.م</div>
                                    <div class="text-[10px] text-gray-500 font-bold" x-text="'مدفوع: ' + deposit + ' ج.'"></div>
                                </div>
                                <div>
                                    <div class="text-[10px] text-gray-400 font-bold mb-0.5">البروفة:</div>
                                    <div class="text-xs font-bold text-gray-700">{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('Y-m-d') : '-' }}</div>
                                </div>
                                <div class="text-left">
                                    <div class="text-[10px] text-gray-400 font-bold mb-0.5">التسليم:</div>
                                    <div class="text-xs font-bold {{ \Carbon\Carbon::parse($order->delivery_date)->isPast() && $order->status != 'completed' ? 'text-red-500 bg-red-50 px-1 rounded inline-block' : 'text-blue-800' }}">
                                        {{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d') : '-' }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between border-t border-gray-50 pt-3 mb-3 px-1">
                                <label class="inline-flex items-center cursor-pointer p-1 rounded-lg hover:bg-gray-50">
                                    <input type="checkbox" x-model="isPaid" @change="togglePaid()" class="w-4 h-4 text-green-500 bg-white border-gray-300 rounded focus:ring-green-400" :disabled="isPaid || loading">
                                    <span class="ml-1 text-[11px] font-bold mr-1.5" :class="isPaid ? 'text-green-600' : 'text-orange-500'" x-text="isPaid ? 'خالص' : 'متبقي'"></span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer p-1 rounded-lg hover:bg-gray-50">
                                    <input type="checkbox" x-model="isDelivered" @change="toggleDelivered()" class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500" :disabled="loading">
                                    <span class="ml-1 text-[11px] font-bold mr-1.5" :class="isDelivered ? 'text-blue-700' : 'text-gray-500'" x-text="isDelivered ? 'مكتمل' : 'جاري'"></span>
                                </label>
                            </div>

                            <div class="flex justify-between gap-2 border-t border-gray-100 pt-3">
                                <a href="{{ route('orders.show', $order) }}" class="flex-1 text-center py-2 bg-indigo-50 text-indigo-700 font-bold text-sm rounded-xl hover:bg-indigo-600 hover:text-white transition">
                                    التفاصيل
                                </a>
                                <a href="{{ route('orders.edit', $order) }}" class="flex-1 text-center py-2 bg-blue-50 text-blue-700 font-bold text-sm rounded-xl hover:bg-blue-600 hover:text-white transition">
                                    تعديل
                                </a>
                                <form action="{{ route('orders.destroy', $order) }}" method="POST" onsubmit="return confirm('متأكدة إنك عايزة تمسحي الطلب ده؟')" class="mb-0 flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full text-center py-2 bg-red-50 text-red-600 font-bold text-sm rounded-xl hover:bg-red-500 hover:text-white transition text-center">
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($orders->isEmpty())
                    <div class="text-center py-16 px-4 text-gray-500 bg-gray-50/50 rounded-2xl border border-gray-100 mt-4">
                        @if(request()->filled('search'))
                            <div class="text-5xl mb-4 opacity-50">🔍</div>
                            <h3 class="text-xl font-bold text-gray-700 mb-2">مفيش نتايج للبحث ده!</h3>
                            <p class="text-sm max-w-sm mx-auto">تأكدي من كود الطلب أو اسم العميلة وجربي تاني.</p>
                        @else
                            <div class="text-5xl mb-4 opacity-50">📭</div>
                            <h3 class="text-xl font-bold text-gray-700 mb-3">مفيش طلبات لسه متسجلة!</h3>
                            <a href="{{ route('orders.create') }}" class="text-sm border border-dashed hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700 border-gray-300 p-3 rounded-xl transition inline-block font-bold">بدء إضافة أول طلب +</a>
                        @endif
                    </div>
                @endif

                <div class="mt-6 flex justify-center w-full">
                    <div class="w-full overflow-x-auto">
                        {{ $orders->links() }}
                    </div>
                </div>
                </div><!-- end resultsContainer -->
            </div>
        </div>
    </div>

    <script>
        // ============== LIVE SEARCH (AJAX + Alpine reinit) ==============
        (function() {
            const input = document.getElementById('searchInput');
            const icon  = document.getElementById('searchIcon');
            const spin  = document.getElementById('searchSpinner');
            if (!input) return;

            let timer;
            input.addEventListener('input', function() {
                clearTimeout(timer);
                timer = setTimeout(function() {
                    const val = input.value.trim();
                    const url = new URL(window.location.href);

                    if (val === '') {
                        url.searchParams.delete('search');
                    } else {
                        url.searchParams.set('search', val);
                    }
                    url.searchParams.delete('page');

                    // Show spinner
                    if (icon) icon.classList.add('hidden');
                    if (spin) spin.classList.remove('hidden');

                    fetch(url.toString(), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(r => r.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContainer = doc.getElementById('resultsContainer');
                        const oldContainer = document.getElementById('resultsContainer');

                        if (newContainer && oldContainer) {
                            // Destroy existing Alpine components inside old container
                            oldContainer.querySelectorAll('[x-data]').forEach(el => {
                                if (el._x_dataStack) {
                                    try { Alpine.destroyTree(el); } catch(e) {}
                                }
                            });

                            oldContainer.innerHTML = newContainer.innerHTML;

                            // Re-initialize Alpine on new content
                            Alpine.initTree(oldContainer);
                        }

                        // Update browser URL without reload
                        window.history.pushState({}, '', url.toString());

                        // Restore icon
                        if (icon) icon.classList.remove('hidden');
                        if (spin) spin.classList.add('hidden');
                    })
                    .catch(() => {
                        if (icon) icon.classList.remove('hidden');
                        if (spin) spin.classList.add('hidden');
                    });

                }, 400);
            });
        })();
        // ============== END LIVE SEARCH ==============

        function orderIndex() {
            return {};
        }

        function orderRow(id, initialStatus, initialPaid, totalPrice, initialDeposit) {
            return {
                id: id,
                isDelivered: initialStatus === 'completed',
                isPaid: initialPaid,
                deposit: initialDeposit,
                totalPrice: totalPrice,
                loading: false,

                toggleDelivered() {
                    const original = !this.isDelivered;
                    this.loading = true;
                    fetch(`/orders/${this.id}/toggle`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            status: this.isDelivered ? 'completed' : 'pending'
                        })
                    }).then(res => {
                        this.loading = false;
                        if (!res.ok) { alert('حصل مشكلة، حاولي تاني.'); this.isDelivered = original; }
                    }).catch(() => {
                        this.loading = false;
                        this.isDelivered = original;
                    });
                },

                togglePaid() {
                    if (this.isPaid && this.deposit !== this.totalPrice) {
                        if (!confirm('العميلة دفعت الحساب بالكامل؟ (المدفوع هيبقا ' + this.totalPrice + ' ج.م)')) {
                            this.isPaid = false;
                            return;
                        }
                        const originalPaid = false;
                        const originalDeposit = this.deposit;
                        this.loading = true;
                        
                        fetch(`/orders/${this.id}/toggle`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                is_fully_paid: true
                            })
                        }).then(res => {
                            this.loading = false;
                            if (res.ok) {
                                this.deposit = this.totalPrice; // update visually
                                // Update global window data so modal sees changes
                                let updatedOrder = window.ordersData.find(o => o.id === this.id);
                                if(updatedOrder) {
                                    updatedOrder.deposit = this.deposit;
                                    updatedOrder.is_fully_paid = true;
                                }
                            } else {
                                this.isPaid = originalPaid;
                                this.deposit = originalDeposit;
                                alert('حصل مشكلة، حاولي تاني.');
                            }
                        }).catch(() => {
                            this.loading = false;
                            this.isPaid = originalPaid;
                            this.deposit = originalDeposit;
                        });
                    }
                }
            }
        }
    </script>
</x-app-layout>
