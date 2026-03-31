<x-app-layout>
    <x-slot name="header">
        <span>تعديل الطلب #{{ $order->order_code }} 📝</span>
    </x-slot>

    <div class="py-6" x-data="orderForm()">
        <form action="{{ route('orders.update', $order) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="client_id" value="{{ $order->client_id }}">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-6">
                    <!-- Client Details -->
                    <div class="glass-card p-6 md:p-8">
                        <h3 class="text-lg font-bold text-gray-500 mb-6 flex items-center gap-2">
                            <span>👤</span> بيانات العميلة
                        </h3>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <div class="flex items-center gap-4">
                                @if($order->client->image)
                                    <img src="{{ asset('app-storage/' . $order->client->image) }}" class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-bold text-lg">{{ mb_substr($order->client->name, 0, 1) }}</div>
                                @endif
                                <div>
                                    <h4 class="font-bold text-gray-700 text-lg">{{ $order->client->name }}</h4>
                                    <p class="text-gray-500 text-sm">{{ $order->client->phone ?? 'بدون رقم' }}</p>
                                </div>
                            </div>
                            
                            <label class="inline-flex items-center cursor-pointer bg-white px-3 py-2 rounded-lg border border-orange-100 hover:bg-orange-50 transition opacity-100 !cursor-pointer">
                                <input type="checkbox" name="is_traveler" value="1" {{ $order->client->is_traveler ? 'checked' : '' }} class="w-5 h-5 text-orange-500 bg-gray-100 border-gray-300 rounded focus:ring-orange-400 focus:ring-2 transition">
                                <span class="ml-2 text-sm font-bold text-orange-700 mr-2"><span>✈️</span> استعجال / سفر</span>
                            </label>
                        </div>
                    </div>

                    <!-- Model Data -->
                    <div class="glass-card p-6 md:p-8">
                        <h3 class="text-lg font-bold text-blue-900 mb-6 flex items-center gap-2">
                            <span>👗</span> تفاصيل الموديل والمقاسات
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">نوع القطعة</label>
                                <select name="item_category_id" x-model="selectedCategory" @change="confirmCategoryChange" required
                                    class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition bg-gray-50">
                                    <option value="">اختاري النوع...</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">لون القماش والتفاصيل</label>
                                <input type="text" name="fabric_color" value="{{ $order->fabric_color }}"
                                    class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition"
                                    placeholder="مثلاً: كحلي، أحمر جبردين...">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">حالة الطلب</label>
                                <select name="status" x-model="status" class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition font-bold" :class="statusColor">
                                    <option value="pending" class="text-yellow-600">قيد التنفيذ / مقاس</option>
                                    <option value="processing" class="text-blue-600">في مرحلة البروفة</option>
                                    <option value="completed" class="text-green-600">مكتمل وتم التسليم</option>
                                </select>
                            </div>
                        </div>

                        <!-- Dynamic Measurements -->
                        <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100 shadow-inner">
                            <div class="flex justify-between items-center mb-4">
                                <p class="text-blue-900 font-bold text-sm">📏 المقاسات المسجلة:</p>
                                <button type="button" @click="addExtraMeasurement" class="text-xs bg-white text-blue-600 px-3 py-1.5 rounded-lg border border-blue-200 hover:bg-blue-50 transition font-medium shadow-sm">
                                    + إضافة مقاس إضافي
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <template x-for="(m, index) in measurements" :key="'meas_'+index">
                                    <div class="relative group mt-2">
                                        <label class="block text-xs font-bold text-blue-800 mb-1" x-text="m.name"></label>
                                        <button type="button" @click="removeMeasurement(index)" class="absolute left-0 -top-6 text-red-400 hover:text-red-600 opacity-50 group-hover:opacity-100 transition text-sm font-bold bg-white rounded-full w-5 h-5 flex items-center justify-center border border-red-100 shadow-sm" title="حذف المقاس">&times;</button>
                                        <input type="text" :name="'measurements['+m.name+']'" x-model="m.value"
                                            class="w-full bg-white rounded-lg border-gray-200 focus:border-blue-400 focus:ring-1 focus:ring-blue-200 text-center text-sm p-3 transition shadow-sm font-medium text-gray-800">
                                    </div>
                                </template>
                                <template x-if="measurements.length === 0">
                                    <div class="col-span-full text-center text-gray-400 text-sm py-4 italic">
                                        لا توجد مقاسات مسجلة. (اختاري النوع أو أضيفي يدوياً)
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Payment & Timing -->
                    <div class="glass-card p-6 md:p-8">
                        <h3 class="text-lg font-bold text-blue-900 mb-6 flex items-center gap-2">
                            <span>💰</span> الحساب والمواعيد
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">السعر الإجمالي</label>
                                <div class="relative">
                                    <input type="number" name="total_price" x-model.number="totalPrice" required
                                        class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition pl-10 pr-3 font-bold text-blue-900">
                                    <span class="absolute left-3 top-2.5 text-gray-500 text-sm font-bold">ج.م</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">اللي اندفع (العربون)</label>
                                <div class="relative">
                                    <input type="number" name="deposit" x-model.number="deposit"
                                        class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition pl-10 pr-3 font-bold text-green-700 bg-green-50/30">
                                     <span class="absolute left-3 top-2.5 text-gray-500 text-sm font-bold">ج.م</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">تاريخ الاستلام (البروفة)</label>
                                <input type="date" name="order_date" required value="{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('Y-m-d') : date('Y-m-d') }}"
                                    class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2 whitespace-nowrap">تاريخ التسليم النهائي</label>
                                <input type="date" name="delivery_date" x-model="deliveryDate" required
                                    class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition font-bold" :class="isOverdue ? 'text-red-500 bg-red-50' : ''">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Image and Summary -->
                <div class="lg:col-span-1">
                    <div class="glass-card p-6 sticky top-24 transform transition-all hover:shadow-xl">
                        <div class="text-center mb-6">
                            <span class="text-blue-900 font-bold block mb-3 text-lg">صورة التصميم</span>
                            <div class="relative group cursor-pointer overflow-hidden rounded-2xl bg-gray-50 border border-gray-200">
                                <template x-if="imagePreview">
                                    <div class="relative w-full aspect-[3/4]">
                                        <img :src="imagePreview" class="absolute inset-0 w-full h-full object-cover transition duration-300 group-hover:scale-105">
                                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                            <span class="text-white font-bold bg-black/60 px-4 py-2 rounded-lg text-sm shadow-lg">تغيير الصورة</span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!imagePreview">
                                    <div class="w-full aspect-[3/4]  flex flex-col items-center justify-center text-blue-400 border-2 border-dashed border-blue-200 group-hover:bg-blue-100 transition">
                                        <span class="text-6xl mb-3 drop-shadow-sm opacity-50">📸</span>
                                        <p class="font-medium text-blue-600 text-sm">تغيير صورة التصميم</p>
                                    </div>
                                </template>
                                <input type="file" name="design_image" @change="previewImage" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" accept="image/*">
                            </div>
                        </div>

                        <div class="cloud-divider my-6"></div>

                        <!-- Summary -->
                        <div class="bg-gradient-to-b from-white to-blue-50/30 p-5 rounded-2xl border border-blue-100 shadow-sm relative overflow-hidden">
                            <div class="absolute -right-4 -top-4 w-12 h-12 bg-blue-100 rounded-full opacity-50 blur-sm"></div>
                            <h4 class="font-bold text-blue-900 mb-4 text-center border-b border-blue-100 pb-2 relative z-10">الحساب</h4>
                            <div class="space-y-3 relative z-10">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500 font-medium">الإجمالي:</span>
                                    <span class="font-bold text-blue-900 bg-white border border-blue-50 px-2 py-1 rounded shadow-sm" x-text="(totalPrice || 0) + ' ج.م'"></span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500 font-medium">المدفوع:</span>
                                    <span class="font-bold text-green-700 bg-green-50 border border-green-100 px-2 py-1 rounded shadow-sm" x-text="(deposit || 0) + ' ج.م'"></span>
                                </div>
                                <div class="flex justify-between items-center text-lg border-t border-dashed border-gray-200 pt-3 mt-2">
                                    <span class="text-gray-800 font-extrabold text-sm mt-1">المتبقي:</span>
                                    <span class="font-extrabold bg-white px-3 py-1.5 rounded-lg border shadow-sm" :class="remaining > 0 ? 'text-orange-500 border-orange-100' : 'text-green-600 border-green-100'" x-text="remaining > 0 ? remaining + ' ج.م' : 'خالص ✔️'"></span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8">
                            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-4 rounded-2xl font-bold shadow-[0_8px_20px_rgba(37,99,235,0.2)] transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2 text-lg">
                                <span>حفظ التعديلات</span>
                                <span>💾</span>
                            </button>
                        </div>
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
                    if(this.status === 'completed') return 'text-green-600 bg-green-50';
                    if(this.status === 'processing') return 'text-blue-600 bg-blue-50';
                    return 'text-yellow-700 bg-yellow-50';
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
                        // revert to original if canceled
                        this.imagePreview = {!! $order->design_image ? "'" . asset('app-storage/' . $order->design_image) . "'" : "null" !!};
                    }
                }
            }
        }
    </script>
</x-app-layout>
