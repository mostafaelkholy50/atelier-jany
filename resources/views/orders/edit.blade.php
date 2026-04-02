<x-app-layout>
    <x-slot name="header">
        <span class="font-bold text-xl text-blue-900">تعديل الطلب #{{ $order->order_code }} 📝</span>
    </x-slot>

    <div class="py-8" x-data="orderForm()">
        <form action="{{ route('orders.update', $order) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="client_id" value="{{ $order->client_id }}">
            
            <div class="max-w-7xl mx-auto space-y-8">
                
                <!-- Client Details & Status Top Bar -->
                <div class="glass-card p-6 md:p-8 shadow-sm flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                    <div class="flex items-center gap-5">
                        @if($order->client->image)
                            <img src="{{ asset('app-storage/' . $order->client->image) }}" class="w-16 h-16 rounded-full object-cover shadow-md border-2 border-white">
                        @else
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-700 flex items-center justify-center font-bold text-2xl shadow-md border-2 border-white">{{ mb_substr($order->client->name, 0, 1) }}</div>
                        @endif
                        <div>
                            <h4 class="font-black text-gray-800 text-xl">{{ $order->client->name }}</h4>
                            <p class="text-gray-500 text-sm mt-1">{{ $order->client->phone ?? 'بدون رقم مسجل' }}</p>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 w-full lg:w-auto mt-2 lg:mt-0">
                        <label class="inline-flex items-center cursor-pointer bg-orange-50 px-4 py-3 rounded-xl border border-orange-100 hover:bg-orange-100 transition whitespace-nowrap lg:w-auto w-full justify-center">
                            <input type="checkbox" name="is_traveler" value="1" {{ $order->client->is_traveler ? 'checked' : '' }} class="w-5 h-5 text-orange-500 bg-white border-gray-300 rounded focus:ring-orange-400 focus:ring-2 transition shadow-sm">
                            <span class="ml-2 text-sm font-bold text-orange-700 mr-2"><span>✈️</span> حالة استعجال / سفر</span>
                        </label>

                        <div class="w-full sm:w-64">
                            <select name="status" x-model="status" class="w-full rounded-xl border-gray-200 focus:ring transition font-bold py-3 text-center shadow-sm" :class="statusColor">
                                <option value="pending" class="text-yellow-700">📌 قيد التنفيذ / مقاس</option>
                                <option value="processing" class="text-blue-700">✂️ في مرحلة البروفة</option>
                                <option value="completed" class="text-green-700">✅ مكتمل وتم التسليم</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    
                    <!-- Left Main Area (Takes 8 columns on large screens) -->
                    <div class="lg:col-span-8 space-y-8 flex flex-col">
                        
                        <!-- Model Info -->
                        <div class="glass-card p-6 md:p-8 flex-1">
                            <h3 class="text-lg font-bold text-blue-900 mb-6 flex items-center gap-2">
                                <span class="bg-blue-100 text-blue-600 p-2 rounded-lg">👗</span> 
                                تفاصيل القطعة المطلوبة
                            </h3>
                            
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">نوع القطعة</label>
                                    <select name="item_category_id" x-model="selectedCategory" @change="confirmCategoryChange" required
                                        class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition bg-white shadow-sm">
                                        <option value="">اختاري النوع...</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">لون القماش والتفاصيل</label>
                                    <input type="text" name="fabric_color" value="{{ $order->fabric_color }}"
                                        class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition shadow-sm"
                                        placeholder="مثلاً: كحلي، أحمر جبردين...">
                                </div>
                            </div>
                        </div>

                        <!-- Measurements -->
                        <div class="glass-card p-6 md:p-8 flex-1">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-4 border-b border-gray-100 pb-4">
                                <h3 class="text-lg font-bold text-blue-900 flex items-center gap-2">
                                    <span class="bg-indigo-100 text-indigo-600 p-2 rounded-lg">📏</span> 
                                    المقاسات المسجلة
                                </h3>
                                <button type="button" @click="addExtraMeasurement" class="text-sm bg-white text-indigo-600 px-4 py-2 rounded-xl border border-indigo-200 hover:bg-indigo-50 transition font-bold shadow-sm flex items-center justify-center gap-2">
                                    <span>➕</span> إضافة مقاس جديد
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-x-4 gap-y-6">
                                <template x-for="(m, index) in measurements" :key="'meas_'+index">
                                    <div class="relative group bg-gray-50 rounded-xl p-3 border border-gray-100 hover:border-blue-200 transition">
                                        <label class="block text-xs font-bold text-gray-600 mb-2 text-center" x-text="m.name"></label>
                                        <button type="button" @click="removeMeasurement(index)" class="absolute -left-2 -top-2 text-red-500 hover:text-white hover:bg-red-500 opacity-20 group-hover:opacity-100 transition text-sm font-bold bg-white rounded-full w-6 h-6 flex items-center justify-center border border-red-200 shadow-md z-10" title="حذف المقاس">&times;</button>
                                        <input type="text" :name="'measurements['+m.name+']'" x-model="m.value"
                                            class="w-full bg-white rounded-lg border-gray-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 text-center text-base p-2 transition shadow-sm font-bold text-blue-900 placeholder-gray-300" placeholder="0">
                                    </div>
                                </template>
                                <template x-if="measurements.length === 0">
                                    <div class="col-span-full flex flex-col items-center justify-center bg-gray-50 rounded-xl p-8 border border-dashed border-gray-200">
                                        <span class="text-3xl mb-2 opacity-50">📐</span>
                                        <p class="text-gray-500 text-sm font-medium">لا توجد مقاسات מסجلة حالياً.</p>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>

                    <!-- Right Main Area (Takes 4 columns on large screens) -->
                    <div class="lg:col-span-4 flex flex-col gap-8 h-full">

                        <!-- Image Section -->
                        <div class="glass-card p-6 md:p-8 flex-1 flex flex-col">
                            <h3 class="text-lg font-bold text-blue-900 mb-6 flex items-center gap-2">
                                <span class="bg-pink-100 text-pink-600 p-2 rounded-lg">📸</span> 
                                صورة التصميم
                            </h3>
                            
                            <div class="relative group cursor-pointer overflow-hidden rounded-2xl bg-gray-50 border-2 border-dashed border-gray-300 hover:border-blue-400 transition-all flex-1 min-h-[300px]">
                                <template x-if="imagePreview">
                                    <div class="w-full h-full min-h-[300px]">
                                        <img :src="imagePreview" class="absolute inset-0 w-full h-full object-cover transition duration-500 group-hover:scale-105">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center backdrop-blur-[2px]">
                                            <span class="text-white font-bold bg-white/20 border border-white/40 px-5 py-2.5 rounded-xl shadow-lg flex items-center gap-2">
                                                <span>🔄</span> تغيير الصورة
                                            </span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!imagePreview">
                                    <div class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 group-hover:bg-blue-50/50 group-hover:text-blue-500 transition">
                                        <span class="text-5xl mb-4 drop-shadow-sm">🖼️</span>
                                        <p class="font-bold text-sm">اضغطي لرفع التصميم</p>
                                    </div>
                                </template>
                                <input type="file" name="design_image" @change="previewImage" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" accept="image/*">
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Bottom Full Width Area -->
                <div class="glass-card p-6 md:p-8 shadow-cm">
                    <h3 class="text-lg font-bold text-blue-900 mb-6 flex items-center gap-2">
                        <span class="bg-green-100 text-green-600 p-2 rounded-lg">💰</span> 
                        الحساب والمواعيد النهائية
                    </h3>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <label class="block text-sm font-semibold text-gray-700 mb-3 text-center">السعر الإجمالي</label>
                            <div class="relative">
                                <input type="number" name="total_price" x-model.number="totalPrice" required
                                    class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-200 transition text-center font-black text-xl text-blue-900 py-3 shadow-sm">
                                <span class="absolute left-3 top-3.5 text-gray-400 text-sm font-bold">ج.م</span>
                            </div>
                        </div>
                        <div class="bg-green-50/50 p-4 rounded-xl border border-green-100">
                            <label class="block text-sm font-semibold text-green-800 mb-3 text-center">اللي اندفع (عربون)</label>
                            <div class="relative">
                                <input type="number" name="deposit" x-model.number="deposit"
                                    class="w-full rounded-xl border-green-200 focus:border-green-500 focus:ring focus:ring-green-200 transition text-center font-black text-xl text-green-700 py-3 shadow-sm bg-white">
                                 <span class="absolute left-3 top-3.5 text-gray-400 text-sm font-bold">ج.م</span>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <label class="block text-sm font-semibold text-gray-700 mb-3 text-center">تاريخ البروفة</label>
                            <input type="date" name="order_date" required value="{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('Y-m-d') : date('Y-m-d') }}"
                                class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition py-3 text-center font-bold text-gray-700 shadow-sm text-sm">
                        </div>
                        <div class="p-4 rounded-xl border" :class="isOverdue ? 'bg-red-50 border-red-200' : 'bg-gray-50 border-gray-100'">
                            <label class="block text-sm font-semibold mb-3 text-center whitespace-nowrap" :class="isOverdue ? 'text-red-700' : 'text-gray-700'">تاريخ التسليم النهائي</label>
                            <input type="date" name="delivery_date" x-model="deliveryDate" required
                                class="w-full rounded-xl focus:ring transition py-3 text-center font-bold shadow-sm text-sm" 
                                :class="isOverdue ? 'border-red-300 text-red-600 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 text-gray-700 focus:border-blue-400 focus:ring-blue-100'">
                        </div>
                    </div>
                    
                    <!-- Sticky Bottom Action Bar inside Card -->
                    <div class="flex flex-col lg:flex-row items-center justify-between gap-6 pt-6 border-t border-gray-100 mt-6">
                        <div class="w-full lg:w-auto bg-blue-50/50 px-6 py-4 rounded-2xl border border-blue-100 flex items-center gap-6 justify-center shadow-inner">
                            <div class="text-center">
                                <span class="block text-xs font-bold text-gray-500 mb-1">الإجمالي</span>
                                <span class="font-black text-gray-800" x-text="(totalPrice || 0) + ' ج.م'"></span>
                            </div>
                            <div class="h-8 w-px bg-blue-200/50"></div>
                            <div class="text-center">
                                <span class="block text-xs font-bold text-gray-500 mb-1">متبقي الدفع</span>
                                <span class="font-black" :class="remaining > 0 ? 'text-orange-600' : 'text-green-600'" x-text="remaining > 0 ? remaining + ' ج.م' : 'تم الوفاء ✨'"></span>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full md:w-1/3 bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-bold hover:shadow-[0_8px_20px_rgba(37,99,235,0.3)] transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3 text-lg">
                            <span>حفظ التعديلات بنجاح</span>
                            <span class="text-xl">🚀</span>
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>

    @php
        $measArray = [];
        if(is_array($order->measurements)) {
            foreach($order->measurements as $k => $v) {
                $measArray[] = ['name' => $k, 'value' => $v];
            }
        }
    @endphp

    <script>
        function orderForm() {
            return {
                selectedCategory: '{{ $order->item_category_id }}',
                measurements: @json($measArray),
                imagePreview: {!! $order->design_image ? "'" . asset('app-storage/' . $order->design_image) . "'" : "null" !!},
                totalPrice: {{ (float) $order->total_price }},
                deposit: {{ (float) ($order->deposit ?? 0) }},
                status: '{{ $order->status }}',
                deliveryDate: '{{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d') : '' }}',

                get remaining() {
                    return Math.max(0, (this.totalPrice || 0) - (this.deposit || 0));
                },

                get isOverdue() {
                    if(!this.deliveryDate || this.status === 'completed') return false;
                    return new Date(this.deliveryDate) < new Date(new Date().toDateString());
                },
                
                get statusColor() {
                    if(this.status === 'completed') return 'text-green-700 bg-green-50 focus:border-green-400 focus:ring-green-100';
                    if(this.status === 'processing') return 'text-blue-700 bg-blue-50 focus:border-blue-400 focus:ring-blue-100';
                    return 'text-yellow-700 bg-yellow-50 focus:border-yellow-400 focus:ring-yellow-100';
                },

                confirmCategoryChange(e) {
                    if (this.measurements.length > 0) {
                        if (!confirm('تغيير نوع الموديل هيحط مقاسات جديدة فاضية، وهيمسح المقاسات الحالية لو مختلفة، متأكدة؟')) {
                            e.target.value = this.selectedCategory; // revert
                            return;
                        }
                    }
                    this.selectedCategory = e.target.value;
                    this.fetchMeasurements();
                },

                fetchMeasurements() {
                    if (!this.selectedCategory) {
                        this.measurements = [];
                        return;
                    }
                    fetch(`/api/categories/${this.selectedCategory}/measurements`)
                        .then(res => res.json())
                        .then(data => {
                            let parsed = [];
                            if (typeof data === 'string') {
                                try { parsed = JSON.parse(data); } catch(e) {}
                            } else if (Array.isArray(data)) {
                                parsed = data;
                            }
                            this.measurements = parsed.map(m => ({name: m, value: ''}));
                        });
                },

                addExtraMeasurement() {
                    const name = prompt('اكتبي اسم المقاس الجديد:');
                    if(name && name.trim()) {
                        this.measurements.push({name: name.trim(), value: ''});
                    }
                },

                removeMeasurement(index) {
                    if(confirm("متأكدة إنك عايزة تمسحي المقاس ده؟")) {
                        this.measurements.splice(index, 1);
                    }
                },

                previewImage(e) {
                    const file = e.target.files[0];
                    if (file) {
                        this.imagePreview = URL.createObjectURL(file);
                    } else {
                        this.imagePreview = {!! $order->design_image ? "'" . asset('app-storage/' . $order->design_image) . "'" : "null" !!};
                    }
                }
            }
        }
    </script>
</x-app-layout>
