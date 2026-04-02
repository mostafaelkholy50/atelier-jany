<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 w-full">
            <span class="text-2xl">📏</span>
            <span class="font-bold text-xl text-blue-900">إعدادات الموديلات والمقاسات</span>
        </div>
    </x-slot>

    <div class="py-6" x-data="categoryManager()">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Add / Edit Form (Right side visually because of RTL) -->
            <div class="lg:col-span-4 order-1">
                <div class="glass-card p-6 sticky top-24 transition-all duration-300" :class="isEdit ? 'ring-2 ring-blue-400 shadow-[0_0_20px_rgba(59,130,246,0.3)]' : ''">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-lg" :class="isEdit ? 'text-blue-700' : 'text-gray-800'" x-text="isEdit ? '📝 تعديل موديل' : '✨ إضافة موديل جديد'"></h3>
                        
                        <button x-show="isEdit" @click="resetForm()" class="text-xs bg-gray-100 hover:bg-red-50 text-red-500 font-bold px-3 py-1.5 rounded-lg transition" x-transition>
                            إلغاء التعديل &times;
                        </button>
                    </div>

                    <form @submit.prevent="submitForm">
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">اسم الموديل</label>
                                <input type="text" x-model="name" required class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition shadow-sm" placeholder="مثلاً: فستان سواريه، عباية...">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">المقاسات المطلوبة (اختياري)</label>
                                <div class="flex gap-2 mb-3">
                                    <input type="text" x-model="newMeasurement" @keydown.enter.prevent="addMeasurement" class="flex-1 rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition text-sm shadow-sm" placeholder="اكتبي المقاس ثم اضغطي +">
                                    <button type="button" @click="addMeasurement" class="bg-blue-100 text-blue-600 px-4 rounded-xl font-bold hover:bg-blue-200 transition text-xl shadow-sm">+</button>
                                </div>

                                <div class="flex flex-wrap gap-2 p-3 bg-gray-50/80 rounded-xl min-h-[100px] border border-dashed border-gray-300 shadow-inner">
                                    <template x-for="(m, index) in measurements" :key="index">
                                        <div class="bg-white border border-blue-100 text-blue-700 px-3 py-1.5 rounded-lg flex items-center gap-2 shadow-sm animate-fade-in">
                                            <span x-text="m" class="text-xs font-bold"></span>
                                            <button type="button" @click="removeMeasurement(index)" class="text-red-400 hover:text-red-600 font-bold ml-1 text-sm bg-red-50 w-5 h-5 rounded-full flex items-center justify-center transition" title="مسح">&times;</button>
                                        </div>
                                    </template>
                                    <template x-if="measurements.length === 0">
                                        <div class="w-full h-full flex items-center justify-center">
                                            <span class="text-gray-400 text-xs italic">لم يتم إضافة مقاسات...</span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="pt-2">
                                <button type="submit" :disabled="loading" class="w-full py-3.5 rounded-xl font-bold transition shadow-lg disabled:opacity-50 flex items-center justify-center gap-2 text-white" :class="isEdit ? 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700' : 'bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700'">
                                    <span x-show="!loading" x-text="isEdit ? 'حفظ التعديلات' : 'إضافة الموديل'"></span>
                                    <span x-show="!loading" x-text="isEdit ? '💾' : '➕'"></span>
                                    <span x-show="loading">جاري الحفظ... ⏳</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Categories Grid (Left side visually) -->
            <div class="lg:col-span-8 order-2">
                <div class="glass-card p-6 min-h-[500px]">
                    <h3 class="font-bold text-gray-800 mb-6 text-lg">الموديلات المسجلة بالمكتبة</h3>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5">
                        <template x-for="category in categoriesList" :key="category.id">
                            <div class="bg-white p-5 rounded-2xl border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition duration-300 relative group flex flex-col h-full overflow-hidden" :class="editId === category.id ? 'ring-2 ring-blue-400 bg-blue-50/30' : ''">
                                <!-- Background Decoration -->
                                <div class="absolute -right-4 -top-4 w-16 h-16 bg-blue-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500 pointer-events-none"></div>

                                <div class="flex items-start justify-between mb-4 relative z-10">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-100 to-blue-50 text-blue-600 flex items-center justify-center text-2xl shadow-sm border border-blue-100">👗</div>
                                        <h3 class="font-bold text-lg text-gray-800" x-text="category.name"></h3>
                                    </div>
                                    <!-- Actions (Permanently visible) -->
                                    <div class="flex gap-2">
                                        <button type="button" @click.stop="editCategory(category)" class="bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white w-9 h-9 rounded-full flex items-center justify-center transition shadow-sm border border-blue-100" title="تعديل الموديل">📝</button>
                                        <button type="button" @click.stop="deleteCategory(category.id)" class="bg-red-50 hover:bg-red-500 text-red-500 hover:text-white w-9 h-9 rounded-full flex items-center justify-center transition shadow-sm border border-red-100" title="حذف الموديل">🗑️</button>
                                    </div>
                                </div>
                                
                                <div class="flex-1 mt-2 relative z-10">
                                    <div class="w-full h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent mb-4"></div>
                                    <h4 class="text-xs text-gray-400 mb-3 font-semibold tracking-wide">المقاسات المتاحة</h4>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="m in parseMeasurements(category.default_measurements)" :key="m">
                                            <span class="bg-gray-50 text-gray-700 text-xs px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm font-medium" x-text="m"></span>
                                        </template>
                                        <template x-if="parseMeasurements(category.default_measurements).length === 0">
                                            <span class="bg-orange-50 text-orange-600 text-xs px-3 py-1.5 rounded-lg border border-orange-100 shadow-sm">بدون مقاسات مخصصة</span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <template x-if="categoriesList.length === 0">
                            <div class="col-span-full text-center py-16 bg-gray-50/50 rounded-2xl border-2 border-dashed border-gray-200">
                                <span class="text-5xl block mb-4">🤷‍♀️</span>
                                <h4 class="text-lg font-bold text-gray-700 mb-2">مفيش أي موديلات لسه!</h4>
                                <p class="text-gray-500 text-sm">استخدمي الفورمة اللي على اليمين عشان تضيفي أنواع الموديلات (زي فستان، بلوزة، بنطلون).</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function categoryManager() {
            return {
                categoriesList: @json($categories),
                isEdit: false,
                editId: null,
                name: '',
                newMeasurement: '',
                measurements: [],
                loading: false,

                parseMeasurements(m) {
                    if (Array.isArray(m)) return m;
                    try { return JSON.parse(m) || []; } catch(e) { return []; }
                },

                addMeasurement() {
                    let val = this.newMeasurement.trim();
                    if (val !== '' && !this.measurements.includes(val)) {
                        this.measurements.push(val);
                        this.newMeasurement = '';
                    }
                },

                removeMeasurement(index) {
                    this.measurements.splice(index, 1);
                },

                editCategory(category) {
                    this.isEdit = true;
                    this.editId = category.id;
                    this.name = category.name;
                    this.measurements = this.parseMeasurements(category.default_measurements);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                resetForm() {
                    this.isEdit = false;
                    this.editId = null;
                    this.name = '';
                    this.measurements = [];
                    this.newMeasurement = '';
                },

                submitForm() {
                    this.loading = true;
                    const url = this.isEdit ? `/categories/${this.editId}` : '{{ route('categories.store') }}';
                    const method = this.isEdit ? 'PATCH' : 'POST';
                    
                    fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name: this.name,
                            measurements: this.measurements
                        })
                    })
                    .then(r => r.json().then(data => ({status: r.status, body: data})))
                    .then(res => {
                        this.loading = false;
                        if(res.status === 200 || res.status === 201) {
                            if(this.isEdit) {
                                let idx = this.categoriesList.findIndex(c => c.id === this.editId);
                                if(idx > -1) {
                                    this.categoriesList[idx] = res.body; 
                                }
                            } else {
                                this.categoriesList.push(res.body);
                            }
                            this.resetForm();
                        } else {
                            alert('حدث خطأ في الحفظ، تأكدي من البيانات.');
                        }
                    })
                    .catch(e => {
                        this.loading = false;
                        console.error(e);
                        alert('حدث خطأ في الاتصال بالخادم. سيتم تحديث الصفحة.');
                        window.location.reload();
                    });
                },

                deleteCategory(id) {
                    if(confirm('هل أنت متأكدة من مسح الموديل؟ لا يمكن التراجع عن هذا الإجراء.')) {
                        fetch(`/categories/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        }).then(r => {
                            if(r.ok) {
                                this.categoriesList = this.categoriesList.filter(c => c.id !== id);
                                if (this.editId === id) {
                                    this.resetForm();
                                }
                            }
                        });
                    }
                }
            }
        }
    </script>

    <style>
        .animate-fade-in { animation: fadeIn 0.3s ease-in-out; }
    </style>
</x-app-layout>
