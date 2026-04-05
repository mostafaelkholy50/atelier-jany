<x-app-layout>
    <x-slot name="header">
        <span class="font-bold text-xl text-blue-900">إضافة طلب جديد ✦</span>
    </x-slot>

    <div class="py-8" x-data="orderForm()" @submit="isSaving = true">
        <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            
            <div class="max-w-7xl mx-auto space-y-8 px-2 sm:px-0">
                
                <!-- Client Details Background Card -->
                <div class="glass-card p-4 md:p-8 shadow-sm">
                    <h3 class="text-lg font-black text-blue-900 mb-6 flex items-center gap-2 border-b border-blue-50 pb-4">
                        <span class="bg-blue-100 text-blue-600 p-2 rounded-lg">👤</span> بيانات العميلة
                    </h3>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="relative z-40">
                            <label class="block text-sm font-bold text-gray-700 mb-2">اسم العميلة</label>
                            <input type="text" name="name" x-model="clientSearch" @input="searchClient" required
                                class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition py-3 shadow-sm font-medium @error('name') border-red-500 bg-red-50 @enderror"
                                placeholder="ابحثي أو اكتبي اسم جديد..." autocomplete="off">
                            @error('name')
                                <p class="text-red-600 text-xs mt-2 font-black flex items-center gap-1 animate-pulse">
                                    <span>⚠️</span> اسم العميلة مطلوب يا ست الكل
                                </p>
                            @enderror

                            <div x-show="showSuggestions" @click.away="showSuggestions = false" class="absolute w-full bg-white mt-2 rounded-xl shadow-2xl border border-gray-100 max-h-60 overflow-y-auto z-50">
                                <template x-for="client in filteredClients" :key="client.id">
                                    <div @click="selectClient(client)" class="p-4 hover:bg-blue-50 cursor-pointer border-b border-gray-50 last:border-0 transition text-right flex items-center gap-4">
                                        <template x-if="client.image">
                                            <img :src="`/app-storage/${client.image}`" class="w-12 h-12 rounded-full object-cover shadow-sm border border-blue-100">
                                        </template>
                                        <template x-if="!client.image">
                                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-600 flex items-center justify-center font-black text-lg shadow-sm border border-blue-50" x-text="client.name.substring(0, 1)"></div>
                                        </template>
                                        <div>
                                            <div class="font-black text-gray-800 text-sm mb-0.5" x-text="client.name"></div>
                                            <div class="text-xs text-gray-500 font-bold" x-text="client.phone || 'بدون رقم'"></div>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="filteredClients.length === 0 && clientSearch.length > 1">
                                     <div class="p-4 text-sm text-gray-500 text-center font-bold bg-gray-50">
                                        🌟 عميلة جديدة سيتم تسجيلها
                                     </div>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">رقم التليفون (اختياري)</label>
                            <input type="text" name="phone" x-model="clientPhone"
                                class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition py-3 shadow-sm font-medium">
                        </div>

                        <div class="lg:col-span-2 flex flex-col xl:flex-row gap-6 mt-2 pt-6 border-t border-gray-100 align-center">
                            
                            <!-- Traveler Toggle -->
                            <label class="inline-flex flex-1 items-center justify-center cursor-pointer bg-white px-5 py-4 rounded-xl border-2 border-orange-100 hover:bg-orange-50 hover:border-orange-200 transition shadow-sm">
                                <input type="checkbox" name="is_traveler" value="1" x-model="isTraveler" class="w-5 h-5 text-orange-500 bg-gray-50 border-gray-300 rounded focus:ring-orange-400 focus:ring-2 transition">
                                <span class="ml-2 text-sm font-black text-orange-700 mr-3"><span>✈️</span> العميلة من سفر (استعجال / من خارج المحافظة)</span>
                            </label>

                            <div x-show="isNewClient" x-transition class="flex flex-1 items-center justify-between bg-blue-50/50 p-4 rounded-xl border-2 border-dashed border-blue-200 gap-4">
                                <div class="text-xs text-gray-600">
                                    <p class="font-bold text-blue-800 mb-1 text-sm">عميلة جديدة! ✨</p>
                                    <p>تقدري ترفعي صورة للعميلة لو تحبي.</p>
                                </div>
                                <label class="px-4 py-2 bg-white border border-blue-200 text-blue-600 rounded-lg cursor-pointer hover:bg-blue-50 transition text-xs font-bold shadow-sm whitespace-nowrap">
                                    🖼️ رفع صورة
                                    <input type="file" name="client_image" class="hidden" accept="image/*">
                                </label>
                            </div>
                        </div>

                        <!-- Global Dates inside Client Section -->
                        <div class="lg:col-span-2 grid grid-cols-1 lg:grid-cols-2 gap-6 mt-4 p-4 md:p-6 bg-gray-50 rounded-2xl border border-gray-100">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2"><span>📅</span> تاريخ الاستلام (تاريخ إضافة الطلب)</label>
                                <input type="date" name="order_date" required value="{{ old('order_date', date('Y-m-d')) }}"
                                    class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition py-3 text-sm shadow-sm font-bold text-gray-600 @error('order_date') border-red-500 bg-red-50 @enderror">
                                @error('order_date')
                                    <p class="text-red-600 text-xs mt-2 font-black flex items-center gap-1 animate-pulse">⚠️ تاريخ الاستلام مهم جداً</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2"><span>🎯</span> موعد التسليم النهائي لهذه الطلبات</label>
                                <input type="date" name="delivery_date" required value="{{ old('delivery_date') }}"
                                    class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition py-3 text-sm shadow-sm font-bold text-indigo-700 bg-white @error('delivery_date') border-red-500 bg-red-50 @enderror">
                                @error('delivery_date')
                                    <p class="text-red-600 text-xs mt-2 font-black flex items-center gap-1 animate-pulse">⚠️ لازم تحددي موعد التسليم</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Loop -->
                <div class="space-y-12">
                    <template x-for="(item, itemIndex) in items" :key="item.id">
                        <div class="glass-card shadow-sm border-2 border-blue-100 rounded-3xl relative overflow-hidden group">
                            <!-- Floating Item Badge -->
                            <div class="absolute top-0 right-0 bg-gradient-to-l from-blue-600 to-blue-500 text-white px-6 py-1.5 rounded-bl-2xl font-bold text-sm shadow-sm z-10 hidden md:block">
                                طلب القطعة رقم <span x-text="itemIndex + 1"></span>
                            </div>
                            
                            <!-- Mobile Item Header -->
                            <div class="md:hidden bg-blue-50 px-5 py-3 border-b border-blue-100 flex justify-between items-center">
                                <span class="font-bold text-blue-900 text-sm">طلب القطعة رقم <span x-text="itemIndex + 1"></span></span>
                                <button type="button" x-show="items.length > 1" @click="removeItem(itemIndex)" class="text-red-500 hover:bg-red-100 p-1.5 rounded-lg transition text-xs font-bold border border-transparent">
                                    حذف 🗑️
                                </button>
                            </div>

                            <button type="button" x-show="items.length > 1" @click="removeItem(itemIndex)" class="hidden md:flex absolute top-4 left-4 text-red-400 hover:text-red-600 hover:bg-red-50 px-3 py-1.5 rounded-lg text-sm font-bold transition opacity-50 group-hover:opacity-100 items-center justify-center gap-1 z-10 border border-transparent hover:border-red-200">
                                حذف الطلب <span class="text-base">🗑️</span>
                            </button>

                            <!-- Internal Multi-Column Structure -->
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">
                                
                                <!-- Left side (Data) -->
                                <div class="lg:col-span-8 p-4 md:p-8 space-y-8 flex flex-col justify-between border-b lg:border-b-0 lg:border-l border-gray-100">
                                    
                                    <!-- Category & Fabric -->
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 bg-white shrink-0">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">نوع القطعة</label>
                                            <select :name="'items['+itemIndex+'][item_category_id]'" x-model="item.selectedCategory" @change="fetchMeasurements(itemIndex)" required
                                                class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition shadow-sm py-3 font-medium bg-gray-50"
                                                :class="getError('items.'+itemIndex+'.item_category_id') ? 'border-red-500 bg-red-50' : ''">
                                                <option value="">اختاري النوع...</option>
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                            <template x-if="getError('items.'+itemIndex+'.item_category_id')">
                                                <p class="text-red-600 text-xs mt-2 font-black flex items-center gap-1 animate-pulse" x-text="'⚠️ لازم تختاري نوع القطعة هنا'"></p>
                                            </template>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">لون القماش</label>
                                            <input type="text" :name="'items['+itemIndex+'][fabric_color]'" x-model="item.fabric_color"
                                                class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition shadow-sm py-3 font-medium"
                                                placeholder="مثلاً: كحلي، أحمر جبردين...">
                                        </div>
                                    </div>

                                    <!-- dynamic Measurements -->
                                    <div class="bg-indigo-50/30 p-5 rounded-2xl border border-indigo-50 shrink-0">
                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-5 gap-3">
                                            <p class="text-indigo-900 font-bold text-sm bg-white px-3 py-1 rounded-lg shadow-sm border border-indigo-100 w-fit">📏 المقاسات المطلوية للقطعة:</p>
                                            <button type="button" @click="addExtraMeasurement(itemIndex)" class="text-xs bg-white text-indigo-600 px-3 py-2 rounded-xl border border-indigo-200 hover:bg-indigo-50 transition font-bold shadow-sm flex items-center justify-center gap-1 w-full sm:w-auto">
                                                <span>➕</span> إضافة مقاس آخر
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
                                            <template x-for="(m, mIndex) in item.measurements" :key="'meas_'+item.id+'_'+mIndex">
                                                <div class="relative group bg-white rounded-xl p-2 border border-indigo-100 hover:border-indigo-300 transition shadow-sm">
                                                    <label class="block text-xs font-bold text-gray-600 mb-1 text-center" x-text="m.name"></label>
                                                    <button type="button" @click="removeMeasurement(itemIndex, mIndex)" class="absolute -left-2 -top-2 text-white bg-red-400 hover:bg-red-600 opacity-0 group-hover:opacity-100 transition text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center shadow-md">&times;</button>
                                                    <input type="text" :name="'items['+itemIndex+'][measurements]['+m.name+']'" x-model="m.value"
                                                        class="w-full border-0 focus:ring-0 text-center text-sm p-1 font-bold text-indigo-900 bg-transparent placeholder-gray-300" placeholder="0">
                                                </div>
                                            </template>
                                            <template x-if="item.measurements.length === 0">
                                                <div class="col-span-full text-center text-indigo-400/70 text-xs py-6 font-bold bg-white rounded-xl border border-dashed border-indigo-100">
                                                    (اختاري نوع القطعة أو أضيفي مقاسات يدوياً للبدء)
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    
                                    <!-- Notes -->
                                    <div class="mt-2 shrink-0">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">ملاحظات إضافية (اختياري)</label>
                                        <textarea :name="'items['+itemIndex+'][notes]'" x-model="item.notes" rows="2"
                                            class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition py-3 shadow-sm font-medium focus:bg-white bg-gray-50/50"
                                            placeholder="اكتبي أي تفاصيل أو ملاحظات خاصة بالقطعة دي هنا..."></textarea>
                                    </div>
                                    
                                    <!-- Payment details for item -->
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-6 border-t border-gray-100 shrink-0">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">إجمالي هذه القطعة</label>
                                            <div class="relative">
                                                <input type="number" :name="'items['+itemIndex+'][total_price]'" x-model.number="item.totalPrice" required
                                                    class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition pl-10 pr-4 py-3 shadow-sm font-black text-blue-900 bg-blue-50/30 text-lg"
                                                    :class="getError('items.'+itemIndex+'.total_price') ? 'border-red-500 bg-red-50' : ''">
                                                <span class="absolute left-4 top-3.5 text-gray-400 text-sm font-bold">ج.م</span>
                                            </div>
                                            <template x-if="getError('items.'+itemIndex+'.total_price')">
                                                <p class="text-red-600 text-xs mt-2 font-black flex items-center gap-1 animate-pulse" x-text="'⚠️ لازم تكتبي السعر هنا'"></p>
                                            </template>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">المدفوع من حسابها (اختياري)</label>
                                            <div class="relative">
                                                <input type="number" :name="'items['+itemIndex+'][deposit]'" x-model.number="item.deposit"
                                                    class="w-full rounded-xl border-green-200 focus:border-green-400 focus:ring-2 focus:ring-green-100 transition pl-10 pr-4 py-3 shadow-sm font-black text-green-700 bg-green-50/50 text-lg">
                                                 <span class="absolute left-4 top-3.5 text-gray-400 text-sm font-bold">ج.م</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right side (Image Upload) -->
                                <div class="lg:col-span-4 p-4 md:p-8 flex flex-col bg-gray-50/30">
                                    <h3 class="text-sm font-bold text-gray-600 mb-4 text-center">صورة التصميم أو الموديل</h3>
                                    <div class="relative group cursor-pointer overflow-hidden rounded-2xl bg-white border-2 border-dashed border-gray-300 hover:border-blue-400 transition-all flex-1 min-h-[250px] shadow-sm">
                                        <template x-if="item.imagePreview">
                                            <div class="relative w-full h-full min-h-[250px] bg-black">
                                                <img :src="item.imagePreview" class="absolute inset-0 w-full h-full object-contain transition duration-500 group-hover:scale-105 opacity-90 group-hover:opacity-100">
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center backdrop-blur-sm">
                                                    <span class="text-white font-bold bg-white/20 border border-white/40 px-4 py-2 rounded-lg text-sm shadow-lg flex items-center gap-2">
                                                        <span>🔄</span> تغيير
                                                    </span>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!item.imagePreview">
                                            <div class="absolute inset-0 flex flex-col items-center justify-center text-gray-300 group-hover:bg-blue-50/50 group-hover:text-blue-500 transition">
                                                <span class="text-4xl mb-3 drop-shadow-sm">📸</span>
                                                <p class="font-bold text-sm">ارفعي صورة هنا</p>
                                            </div>
                                        </template>
                                        <input type="file" :name="'items['+itemIndex+'][design_image]'" @change="previewImage($event, itemIndex)" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" accept="image/*">
                                    </div>
                                    
                                    <!-- Mini summary -->
                                    <div class="mt-6 bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex flex-col gap-2">
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="text-gray-500 font-bold">حساب القطعة:</span>
                                            <span class="font-black text-gray-800" x-text="(item.totalPrice || 0) + ' ج'"></span>
                                        </div>
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="text-gray-500 font-bold">المدفوع:</span>
                                            <span class="font-black text-green-600" x-text="(item.deposit || 0) + ' ج'"></span>
                                        </div>
                                        <div class="flex justify-between items-center text-sm border-t border-gray-100 pt-2 mt-1">
                                            <span class="text-red-500 font-bold">المتبقي:</span>
                                            <span class="font-black text-red-600" x-text="Math.max(0, (item.totalPrice || 0) - (item.deposit || 0)) + ' ج.م'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div class="flex justify-center mb-10 pt-4">
                    <button type="button" @click="addItem()" class="bg-white text-blue-600 px-8 py-4 rounded-2xl border-2 border-dashed border-blue-300 hover:border-blue-500 hover:bg-blue-50 shadow-sm transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3 w-full sm:w-auto font-black text-lg">
                        <span class="text-2xl leading-none">➕</span> أضيفي قطعة أخرى للعميلة
                    </button>
                </div>

                <!-- Grand Total & Submit -->
                <div class="glass-card p-4 md:p-10 border-t-4 border-blue-500">
                    <h3 class="text-xl font-black text-blue-900 mb-6 text-center lg:text-right">إجمالي الفاتورة للعميلة بأكملها</h3>
                    
                    <div class="flex flex-col lg:flex-row justify-between items-stretch text-lg gap-4 bg-gray-50 rounded-2xl border border-gray-200 mb-8 overflow-hidden shadow-inner">
                        <div class="flex flex-col items-center justify-center w-full lg:flex-1 py-6 px-4 bg-white border-b lg:border-b-0 lg:border-l border-gray-200 transition hover:bg-gray-50">
                            <span class="text-gray-500 text-xs font-bold mb-2 uppercase tracking-widest">الإجمالي النهائي للطلب</span>
                            <span class="font-black text-blue-900 text-3xl" x-text="items.reduce((sum, item) => sum + (Number(item.totalPrice) || 0), 0) + ' ج.م'"></span>
                        </div>
                        <div class="flex flex-col items-center justify-center w-full lg:flex-1 py-6 px-4 bg-white border-b lg:border-b-0 lg:border-l border-gray-200 transition hover:bg-green-50">
                            <span class="text-green-700 text-xs font-bold mb-2 uppercase tracking-widest">المدفوع كله الآن</span>
                            <span class="font-black text-green-600 text-3xl" x-text="items.reduce((sum, item) => sum + (Number(item.deposit) || 0), 0) + ' ج.م'"></span>
                        </div>
                        <div class="flex flex-col items-center justify-center w-full lg:flex-1 py-6 px-4 bg-red-50/50 transition hover:bg-red-50">
                            <span class="text-red-700 text-xs font-bold mb-2 uppercase tracking-widest">المتبقي المطلوب لاحقاً</span>
                            <span class="font-black text-red-600 text-3xl" x-text="items.reduce((sum, item) => sum + Math.max(0, (Number(item.totalPrice) || 0) - (Number(item.deposit) || 0)), 0) + ' ج.م'"></span>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <button type="submit" :disabled="isSaving" 
                            class="w-full md:w-2/3 lg:w-1/2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-5 rounded-2xl font-black shadow-[0_10px_25px_rgba(37,99,235,0.3)] transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3 text-xl border border-blue-400 disabled:opacity-75 disabled:cursor-not-allowed">
                            <span x-show="!isSaving">حفظ الطلب بالكامل </span>
                            <span x-show="isSaving">جاري الحفظ.. خليكي مكانك ⏳</span>
                            <span class="text-2xl" x-show="!isSaving">🚀</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function orderForm() {
            return {
                clientSearch: '{{ old('name', '') }}',
                clientPhone: '{{ old('phone', '') }}',
                clients: @json($clients),
                filteredClients: [],
                showSuggestions: false,
                isTraveler: {{ old('is_traveler') ? 'true' : 'false' }},
                isSaving: false,
                laravelErrors: @json($errors->getMessages()),

                getError(field) {
                    return this.laravelErrors[field] ? this.laravelErrors[field][0] : null;
                },

                items: @json(old('items')) || [
                    {
                        id: Date.now(),
                        selectedCategory: '',
                        measurements: [],
                        fabric_color: '',
                        totalPrice: '',
                        deposit: '',
                        imagePreview: null,
                        notes: '',
                    }
                ],

                init() {
                    // Ensure each item has an id and proper structure if coming from old()
                    this.items = this.items.map((item, idx) => {
                        if (!item.id) item.id = Date.now() + idx;
                        
                        // Map internal field names if they came from Laravel old()
                        if (item.item_category_id && !item.selectedCategory) {
                            item.selectedCategory = item.item_category_id;
                        }
                        if (item.total_price && !item.totalPrice) {
                            item.totalPrice = item.total_price;
                        }

                        // Handle measurements format if it was saved as name => value (object) from laravel old()
                        if (item.measurements && !Array.isArray(item.measurements)) {
                            item.measurements = Object.keys(item.measurements).map(key => ({
                                name: key,
                                value: item.measurements[key]
                            }));
                        }
                        
                        // Ensure it's an array if it was null
                        if (!item.measurements) item.measurements = [];
                        
                        return item;
                    });
                },

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

                addItem() {
                    this.items.push({
                        id: Date.now() + Math.random(),
                        selectedCategory: '',
                        measurements: [],
                        fabric_color: '',
                        totalPrice: '',
                        deposit: '',
                        imagePreview: null,
                        notes: '',
                    });
                },

                removeItem(index) {
                    if(this.items.length > 1) {
                        if(confirm('متأكدة إنك عايزة تمسحي القطعة دي من الفاتورة؟')) {
                            this.items.splice(index, 1);
                        }
                    }
                },

                fetchMeasurements(index) {
                    const catId = this.items[index].selectedCategory;
                    if (!catId) {
                        this.items[index].measurements = [];
                        return;
                    }
                    fetch(`/api/categories/${catId}/measurements`)
                        .then(res => res.json())
                        .then(data => {
                            let parsed = [];
                            if (typeof data === 'string') {
                                try { parsed = JSON.parse(data); } catch(e) {}
                            } else if (Array.isArray(data)) {
                                parsed = data;
                            }
                            this.items[index].measurements = parsed.map(m => ({name: m}));
                        });
                },

                addExtraMeasurement(index) {
                    const name = prompt('اكتبي اسم المقاس الجديد للقطعة دي:');
                    if(name && name.trim()) {
                        this.items[index].measurements.push({name: name.trim()});
                    }
                },

                removeMeasurement(itemIndex, mIndex) {
                    this.items[itemIndex].measurements.splice(mIndex, 1);
                },

                previewImage(e, index) {
                    const file = e.target.files[0];
                    if (file) {
                        this.items[index].imagePreview = URL.createObjectURL(file);
                    } else {
                        this.items[index].imagePreview = null;
                    }
                }
            }
        }
    </script>
</x-app-layout>
