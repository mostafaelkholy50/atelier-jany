<x-app-layout>
    <x-slot name="header">
        <span>إضافة طلب جديد ✦</span>
    </x-slot>

    <div class="py-6" x-data="orderForm()">
        <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Form appears first in DOM, so it shows on the right in RTL -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2 space-y-6">
                    <!-- Client Details -->
                    <div class="glass-card p-6 md:p-8">
                        <h3 class="text-lg font-bold text-blue-900 mb-6 flex items-center gap-2">
                            <span>👤</span> بيانات العميلة
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="relative z-40">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">اسم العميلة</label>
                                <input type="text" name="name" x-model="clientSearch" @input="searchClient" required
                                    class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition"
                                    placeholder="ابحثي أو اكتبي اسم جديد..." autocomplete="off">

                                <div x-show="showSuggestions" @click.away="showSuggestions = false" class="absolute w-full bg-white mt-1 rounded-xl shadow-2xl border border-gray-100 max-h-60 overflow-y-auto">
                                    <template x-for="client in filteredClients" :key="client.id">
                                        <div @click="selectClient(client)" class="p-3 hover:bg-blue-50 cursor-pointer border-b border-gray-50 last:border-0 transition text-right flex items-center gap-3">
                                            <template x-if="client.image">
                                                <img :src="`/storage/${client.image}`" class="w-10 h-10 rounded-full object-cover shadow-sm border border-blue-100">
                                            </template>
                                            <template x-if="!client.image">
                                                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm" x-text="client.name.substring(0, 1)"></div>
                                            </template>
                                            <div>
                                                <div class="font-bold text-blue-900" x-text="client.name"></div>
                                                <div class="text-xs text-gray-500" x-text="client.phone"></div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="filteredClients.length === 0 && clientSearch.length > 1">
                                         <div class="p-3 text-sm text-gray-500 text-center">
                                            🌟 عميلة جديدة سيتم تسجيلها
                                         </div>
                                    </template>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">رقم التليفون (اختياري)</label>
                                <input type="text" name="phone" x-model="clientPhone"
                                    class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition">
                            </div>

                            <div class="md:col-span-2">
                                <label class="inline-flex items-center cursor-pointer bg-orange-50 px-4 py-3 rounded-xl border border-orange-100 hover:bg-orange-100 transition w-full sm:w-auto">
                                    <input type="checkbox" name="is_traveler" value="1" x-model="isTraveler" class="w-5 h-5 text-orange-500 bg-white border-gray-300 rounded focus:ring-orange-400 focus:ring-2 transition">
                                    <span class="ml-2 text-sm font-bold text-orange-700 mr-2 flex items-center gap-2"><span>✈️</span> العميلة من سفر (استعجال/من خارج المحافظة)</span>
                                </label>
                            </div>

                            <div x-show="isNewClient" x-transition class="md:col-span-2 bg-blue-50/50 p-4 rounded-xl border border-dashed border-blue-200 flex items-center gap-4">
                                <div class="text-sm text-gray-600 flex-1">
                                    <p class="font-bold text-blue-800 mb-1">عميلة جديدة!</p>
                                    <p>تقدري ترفعي صورة للعميلة لو تحبي (اختياري).</p>
                                </div>
                                <div>
                                    <label class="px-4 py-2 bg-white border border-blue-200 text-blue-600 rounded-lg cursor-pointer hover:bg-blue-50 transition text-sm font-medium shadow-sm">
                                        🖼️ رفع صورة
                                        <input type="file" name="client_image" class="hidden" accept="image/*">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Model Data -->
                    <div class="glass-card p-6 md:p-8">
                        <h3 class="text-lg font-bold text-blue-900 mb-6 flex items-center gap-2">
                            <span>👗</span> تفاصيل الموديل والمقاسات
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">نوع القطعة</label>
                                <select name="item_category_id" x-model="selectedCategory" @change="fetchMeasurements" required
                                    class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition">
                                    <option value="">اختاري النوع...</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">لون القماش والتفاصيل</label>
                                <input type="text" name="fabric_color"
                                    class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition"
                                    placeholder="مثلاً: كحلي، أحمر جبردين...">
                            </div>
                        </div>

                        <!-- Dynamic Measurements -->
                        <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100 shadow-inner">
                            <div class="flex justify-between items-center mb-4">
                                <p class="text-blue-900 font-bold text-sm">📏 المقاسات (اختيارية):</p>
                                <button type="button" @click="addExtraMeasurement" class="text-xs bg-white text-blue-600 px-3 py-1.5 rounded-lg border border-blue-200 hover:bg-blue-50 transition font-medium">
                                    + إضافة مقاس إضافي
                                </button>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <template x-for="(m, index) in measurements" :key="'meas_'+index">
                                    <div class="relative group">
                                        <label class="block text-xs font-semibold text-gray-500 mb-1" x-text="m.name"></label>
                                        <button type="button" @click="removeMeasurement(index)" class="absolute left-0 -top-1 text-red-400 opacity-0 group-hover:opacity-100 transition text-xs" title="حذف">&times;</button>
                                        <input type="text" :name="'measurements['+m.name+']'"
                                            class="w-full bg-white rounded-lg border-gray-200 focus:border-blue-400 focus:ring-1 focus:ring-blue-200 text-center text-sm p-2 transition">
                                    </div>
                                </template>
                                <template x-if="measurements.length === 0">
                                    <div class="col-span-full text-center text-gray-400 text-sm py-4 italic">
                                        اختاري نوع القطعة لعرض المقاسات المناسبة، أو أضيفي مقاس يدوياً.
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
                                        class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition pl-10 pr-3">
                                    <span class="absolute left-3 top-2.5 text-gray-400 text-sm font-bold">ج.م</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">العربون (اختياري)</label>
                                <div class="relative">
                                    <input type="number" name="deposit" x-model.number="deposit"
                                        class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition pl-10 pr-3">
                                     <span class="absolute left-3 top-2.5 text-gray-400 text-sm font-bold">ج.م</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">تاريخ الاستلام</label>
                                <input type="date" name="order_date" required value="{{ date('Y-m-d') }}"
                                    class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">تاريخ التسليم النهائي</label>
                                <input type="date" name="delivery_date" required
                                    class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Image and Summary (Visually on the left in RTL) -->
                <div class="lg:col-span-1">
                    <div class="glass-card p-6 sticky top-24 transform transition-all hover:shadow-xl">
                        <div class="text-center mb-6">
                            <span class="text-blue-900 font-bold block mb-3 text-lg">صورة التصميم</span>
                            <div class="relative group cursor-pointer overflow-hidden rounded-2xl">
                                <template x-if="imagePreview">
                                    <div class="relative w-full aspect-[3/4]">
                                        <img :src="imagePreview" class="absolute inset-0 w-full h-full object-cover transition duration-300 group-hover:scale-105">
                                        <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                            <span class="text-white font-bold bg-black/50 px-4 py-2 rounded-lg">تغيير الصورة</span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!imagePreview">
                                    <div class="w-full aspect-[3/4] bg-gradient-to-br from-blue-50 to-blue-100/50 flex flex-col items-center justify-center text-blue-400 border-2 border-dashed border-blue-200 group-hover:bg-blue-100 transition">
                                        <span class="text-6xl mb-3 drop-shadow-md">📸</span>
                                        <p class="font-medium text-blue-600">اضغطي هنا لرفع تصميم</p>
                                    </div>
                                </template>
                                <input type="file" name="design_image" @change="previewImage" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" accept="image/*">
                            </div>
                        </div>

                        <div class="cloud-divider my-6"></div>

                        <!-- Summary -->
                        <div class="bg-gradient-to-b from-white to-blue-50/30 p-5 rounded-2xl border border-blue-100 shadow-sm">
                            <h4 class="font-bold text-blue-900 mb-4 text-center border-b border-blue-100 pb-2">ملخص الحساب</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">إجمالي المبلغ:</span>
                                    <span class="font-bold text-gray-800 bg-white px-2 py-1 rounded shadow-sm" x-text="(totalPrice || 0) + ' ج.م'"></span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">تم دفع:</span>
                                    <span class="font-bold text-green-600 bg-green-50 px-2 py-1 rounded shadow-sm" x-text="(deposit || 0) + ' ج.م'"></span>
                                </div>
                                <div class="flex justify-between items-center text-lg border-t pt-3 border-blue-100/50 mt-2">
                                    <span class="text-blue-900 font-extrabold">المتبقي:</span>
                                    <span class="font-extrabold text-red-500 bg-red-50 px-3 py-1.5 rounded-lg shadow-sm" x-text="Math.max(0, (totalPrice || 0) - (deposit || 0)) + ' ج.م'"></span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-bold shadow-[0_8px_20px_rgba(37,99,235,0.2)] transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2 text-lg">
                                <span>حفظ الطلب بنجاح</span>
                                <span>🚀</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        function orderForm() {
            return {
                clientSearch: '',
                clientPhone: '',
                clients: @json($clients),
                filteredClients: [],
                showSuggestions: false,

                selectedCategory: '',
                measurements: [],
                imagePreview: null,
                totalPrice: 0,
                deposit: 0,
                isTraveler: false,

                get isNewClient() {
                    if(this.clientSearch.length < 2) return false;
                    const exactMatch = this.clients.find(c => c.name === this.clientSearch);
                    return !exactMatch;
                },

                searchClient() {
                    if (this.clientSearch.length < 1) {
                        this.showSuggestions = false;
                        return;
                    }
                    this.filteredClients = this.clients.filter(c =>
                        c.name.includes(this.clientSearch) || (c.phone && c.phone.includes(this.clientSearch))
                    );
                    this.showSuggestions = true;
                },

                selectClient(client) {
                    this.clientSearch = client.name;
                    this.clientPhone = client.phone || '';
                    this.isTraveler = client.is_traveler == 1;
                    this.showSuggestions = false;
                },

                fetchMeasurements() {
                    if (!this.selectedCategory) {
                        this.measurements = [];
                        return;
                    }
                    fetch(`/api/categories/${this.selectedCategory}/measurements`)
                        .then(res => res.json())
                        .then(data => {
                            // Map string array to objects
                            let parsed = [];
                            if (typeof data === 'string') {
                                try { parsed = JSON.parse(data); } catch(e) {}
                            } else if (Array.isArray(data)) {
                                parsed = data;
                            }
                            this.measurements = parsed.map(m => ({name: m}));
                        });
                },

                addExtraMeasurement() {
                    const name = prompt('اكتبي اسم المقاس الجديد:');
                    if(name && name.trim()) {
                        this.measurements.push({name: name.trim()});
                    }
                },

                removeMeasurement(index) {
                    this.measurements.splice(index, 1);
                },

                previewImage(e) {
                    const file = e.target.files[0];
                    if (file) {
                        this.imagePreview = URL.createObjectURL(file);
                    } else {
                        this.imagePreview = null;
                    }
                }
            }
        }
    </script>
</x-app-layout>
