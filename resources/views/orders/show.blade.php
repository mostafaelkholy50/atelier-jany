<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span class="font-bold text-xl text-blue-900 flex items-center gap-2">
                <span>📝</span> تفاصيل الطلب #{{ $order->order_code }}
            </span>
            <a href="{{ route('orders.index') }}" class="px-4 py-2 bg-white text-gray-700 font-bold rounded-xl border border-gray-200 shadow-sm hover:bg-gray-50 transition flex items-center gap-2 text-sm">
                <span>الرجوع للطلبات</span> <span>🔙</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 max-w-7xl mx-auto">
                            
            <!-- Middle/Right Column: Details -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Client Info -->
                <div class="glass-card p-4 md:p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row items-center gap-4 sm:gap-6 hover:shadow-md transition">
                    @if($order->client->image)
                        <img src="{{ asset('app-storage/' . $order->client->image) }}" 
                             onclick="openImageModal(this.src)"
                             class="w-20 h-20 rounded-full object-cover border-4 border-blue-50 shadow-sm cursor-zoom-in hover:scale-110 transition duration-300">
                    @else
                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-600 flex items-center justify-center font-bold text-3xl shadow-inner border border-blue-200">{{ mb_substr($order->client->name, 0, 1) }}</div>
                    @endif
                    <div>
                        <h4 class="font-bold text-gray-800 text-2xl flex items-center gap-3 mb-2">
                            {{ $order->client->name }}
                            @if($order->client->is_traveler)
                                <span class="text-xs bg-orange-100 text-orange-600 px-3 py-1 rounded-full whitespace-nowrap font-bold border border-orange-200 shadow-[0_2px_10px_rgba(234,88,12,0.1)]">✈️ استعجال / سفر</span>
                            @endif
                        </h4>
                        <div class="flex items-center gap-4 text-sm">
                            <a href="tel:{{ $order->client->phone }}" class="text-gray-500 hover:text-blue-600 transition flex items-center gap-1.5 font-medium bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">📞 {{ $order->client->phone ?? 'بدون رقم' }}</a>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Order Info -->
                    <div class="glass-card p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4 relative overflow-hidden">
                        <div class="absolute -left-6 -top-6 text-6xl opacity-5">👗</div>
                        <h4 class="font-bold text-blue-900 border-b border-gray-100 pb-3 mb-4 text-lg relative z-10">تفاصيل الموديل</h4>
                        <div class="flex items-center justify-between relative z-10">
                            <span class="text-gray-500 text-sm font-bold">نوع القطعة:</span>
                            <span class="font-bold text-indigo-900 bg-indigo-50 border border-indigo-100 px-3 py-1.5 rounded-lg text-sm shadow-sm">{{ $order->itemCategory->name ?? 'غير معروف' }}</span>
                        </div>
                        <div class="flex items-center justify-between relative z-10 mt-3">
                            <span class="text-gray-500 text-sm font-bold">التفاصيل/اللون:</span>
                            <span class="font-bold text-gray-800 text-sm bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">{{ $order->fabric_color ?? 'غير محدد' }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-4 border-t border-gray-50 mt-2 relative z-10">
                            <span class="text-gray-500 text-sm font-bold">حالة الطلب:</span>
                            @php
                                $statusClasses = [
                                    'completed' => 'bg-green-100 text-green-700 border-green-200',
                                    'processing' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                ];
                                $statusLabels = [
                                    'completed' => 'مكتمل وتم التسليم ✔️',
                                    'processing' => 'في مرحلة البروفة ✂️',
                                    'pending' => 'قيد التنفيذ / مقاس 📝',
                                ];
                                $stClass = $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-700';
                                $stLabel = $statusLabels[$order->status] ?? $order->status;
                            @endphp
                            <span class="font-bold text-sm px-3 py-1.5 rounded-lg border shadow-sm {{ $stClass }}">
                                {{ $stLabel }}
                            </span>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="glass-card p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                        <h4 class="font-bold text-blue-900 border-b border-gray-100 pb-3 mb-4 text-lg flex items-center gap-2"><span>🗓️</span> المواعيد</h4>
                        <div class="flex flex-col gap-1.5">
                            <span class="text-gray-500 text-sm font-bold">تاريخ الطلب / البروفة:</span>
                            <div class="font-black text-gray-700 bg-gray-50 p-3 rounded-xl border border-gray-200 flex items-center gap-2">
                                <span class="opacity-50 text-xl">📌</span> {{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('Y-m-d') : '-' }}
                            </div>
                        </div>
                        <div class="flex flex-col gap-1.5 mt-3">
                            <span class="text-gray-500 text-sm font-bold">التسليم النهائي:</span>
                            @php
                                $isOverdue = \Carbon\Carbon::parse($order->delivery_date)->isPast() && $order->status != 'completed';
                            @endphp
                            <div class="font-black p-3 rounded-xl border flex items-center gap-2 {{ $isOverdue ? 'bg-red-50 text-red-700 border-red-200 shadow-sm' : 'bg-blue-50 text-blue-900 border-blue-200' }}">
                                <span class="opacity-50 text-xl">{{ $isOverdue ? '⚠️' : '🎯' }}</span> {{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d') : '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                @if($order->notes)
                <!-- Notes -->
                <div class="glass-card p-6 md:p-8 rounded-2xl border border-yellow-200 shadow-sm relative overflow-hidden bg-gradient-to-r from-yellow-50/50 to-orange-50/50">
                    <div class="absolute -left-6 -bottom-6 text-7xl opacity-10">📝</div>
                    <h4 class="font-bold text-yellow-900 mb-4 text-lg relative z-10 flex items-center gap-3">
                        <span class="bg-yellow-200 text-yellow-800 p-2 rounded-xl text-xl shadow-sm">📝</span> ملاحظات إضافية
                    </h4>
                    <div class="relative z-10 bg-white p-4 rounded-xl border border-yellow-100 shadow-sm">
                        <p class="text-gray-800 font-medium leading-relaxed whitespace-pre-line">{{ $order->notes }}</p>
                    </div>
                </div>
                @endif

                <!-- Measurements -->
                <div class="glass-card bg-gradient-to-r from-gray-50 to-white p-6 md:p-8 rounded-2xl border border-blue-50 shadow-sm relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-blue-50 rounded-full opacity-50 border-[10px] border-white"></div>
                    <div class="absolute -left-10 -bottom-10 w-24 h-24 bg-indigo-50 rounded-full opacity-50"></div>
                    
                    <h4 class="font-bold text-blue-900 mb-6 text-xl relative z-10 flex items-center gap-3">
                        <span class="bg-blue-100 p-2 rounded-xl text-2xl shadow-sm">📏</span> مقاسات العميلة 
                    </h4>
                    
                    <div class="flex flex-wrap gap-4 relative z-10">
                        @if(is_array($order->measurements) && count($order->measurements) > 0)
                            @foreach($order->measurements as $name => $value)
                                <div class="bg-white border-2 border-gray-100 hover:border-blue-200 hover:shadow-md px-3 py-2 md:px-4 md:py-3 rounded-2xl flex flex-col items-center gap-1 min-w-[100px] flex-1 transition-all transform hover:-translate-y-1">
                                    <span class="text-gray-500 text-xs font-bold">{{ $name }}</span>
                                    <span class="text-indigo-800 font-black text-xl">{{ $value ?: '-' }}</span>
                                </div>
                            @endforeach
                        @else
                            <div class="w-full text-center py-6 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                                <span class="text-5xl opacity-20 block mb-2">🤷‍♀️</span>
                                <span class="text-gray-500 font-bold">لا توجد مقاسات مسجلة للموديل ده.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Left Column: Image & Financials -->
            <div class="lg:col-span-4 space-y-6 flex flex-col">
                <!-- Image -->
                <div class="glass-card p-3 rounded-2xl border border-gray-100 shadow-sm h-auto min-h-[320px] lg:h-96 w-full relative group overflow-hidden bg-white">
                    @if($order->design_image)
                        <div onclick="openImageModal('{{ asset('app-storage/' . $order->design_image) }}')" 
                             class="block w-full h-full relative cursor-zoom-in" title="اضغط لتكبير الصورة">
                            <img src="{{ asset('app-storage/' . $order->design_image) }}" class="w-full h-full object-contain bg-gray-50 rounded-xl transition duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition rounded-xl flex flex-col items-center justify-center gap-2 backdrop-blur-[2px]">
                                <span class="text-4xl shadow-sm">🔍</span>
                                <span class="bg-white/90 text-blue-900 text-sm px-4 py-2 rounded-xl font-bold backdrop-blur-md shadow-lg">اضغط للتكبير</span>
                            </div>
                        </div>
                    @else
                        <div class="w-full h-full bg-gray-50 flex flex-col gap-4 items-center justify-center rounded-xl border-2 border-dashed border-gray-200">
                            <span class="text-6xl opacity-30 drop-shadow-sm">📸</span>
                            <span class="text-gray-400 text-sm font-bold bg-white px-4 py-2 rounded-lg border border-gray-100 shadow-sm">لا توجد صورة للتصميم</span>
                        </div>
                    @endif
                </div>

                <!-- Financials -->
                <div class="glass-card bg-gradient-to-b from-white to-gray-50 p-6 xl:p-8 rounded-2xl border border-gray-100 shadow-lg relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 text-8xl opacity-[0.03] select-none pointer-events-none">💰</div>
                    
                    <h4 class="font-bold text-gray-800 border-b border-gray-200 pb-3 mb-5 relative z-10 flex items-center gap-2 text-lg">
                        <span class="text-xl">💳</span> تفاصيل الحساب
                    </h4>
                    
                    <div class="space-y-4 relative z-10">
                        <div class="flex justify-between items-center bg-white border border-gray-100 p-3 px-4 rounded-xl shadow-sm hover:border-blue-200 transition">
                            <span class="text-gray-500 text-sm font-bold">الإجمالي:</span>
                            <span class="font-black text-gray-800 text-lg">{{ $order->total_price }} ج.م</span>
                        </div>
                        <div class="flex justify-between items-center bg-green-50 border border-green-100 p-3 px-4 rounded-xl shadow-sm hover:bg-green-100 transition">
                            <span class="text-gray-600 text-sm font-bold">المدفوع:</span>
                            <span class="font-black text-green-700 text-lg">{{ $order->deposit ?? 0 }} ج.م</span>
                        </div>
                        
                        @php
                            $remaining = max(0, $order->total_price - ($order->deposit ?? 0));
                        @endphp
                        <div class="flex justify-between items-center p-4 px-5 mt-4 rounded-2xl shadow-md border-b-4 {{ $remaining > 0 ? 'bg-red-50 border-red-200' : 'bg-green-100 border-green-300' }} hover:-translate-y-1 transition-transform">
                            <span class="font-black text-sm {{ $remaining > 0 ? 'text-red-600' : 'text-green-800' }}">المتبقي:</span>
                            <span class="font-black text-2xl drop-shadow-sm {{ $remaining > 0 ? 'text-red-700' : 'text-green-800' }}">{{ $remaining }} ج.م</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col gap-3">
                    <a href="{{ route('orders.edit', $order) }}" class="flex w-full bg-blue-600 text-white hover:bg-blue-700 hover:shadow-xl hover:shadow-blue-200/50 transition-all py-4 rounded-2xl font-bold text-center items-center justify-center gap-2 shadow-lg transform hover:-translate-y-1">
                        تعديل الطلب <span class="text-xl">✏️</span>
                    </a>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
