<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span class="font-bold text-xl text-blue-900 flex items-center gap-2"><span>👗</span> إضافة عميلة جديدة</span>
            <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-white text-gray-700 font-bold rounded-xl border border-gray-200 shadow-sm hover:bg-gray-50 transition flex items-center gap-2 text-sm">
                🔙 الرجوع
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto" x-data="clientForm()">
        <form action="{{ route('clients.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            <div class="glass-card p-6 md:p-8 space-y-6">

                {{-- Image Upload --}}
                <div class="flex flex-col items-center gap-3">
                    <div class="relative group cursor-pointer" @click="$refs.imageFile.click()">
                        <template x-if="preview">
                            <img :src="preview" class="w-28 h-28 rounded-full object-cover border-4 border-blue-100 shadow-lg group-hover:opacity-80 transition">
                        </template>
                        <template x-if="!preview">
                            <div class="w-28 h-28 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 flex flex-col items-center justify-center border-4 border-dashed border-blue-200 group-hover:border-blue-400 transition">
                                <span class="text-3xl opacity-40">📸</span>
                                <span class="text-[11px] text-blue-500 font-bold mt-1">صورة (اختياري)</span>
                            </div>
                        </template>
                        <div class="absolute -bottom-1 -left-1 bg-blue-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm shadow-md border-2 border-white">+</div>
                    </div>
                    <input type="file" name="image" x-ref="imageFile" @change="onImage" class="hidden" accept="image/*">
                    <p class="text-xs text-gray-400 text-center">اضغطي على الدائرة لرفع صورة (اختياري)</p>
                </div>

                <div class="cloud-divider"></div>

                {{-- Name --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">اسم العميلة <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition @error('name') border-red-500 bg-red-50 @enderror"
                        placeholder="مثلاً: فاطمة أحمد...">
                    @error('name') 
                        <p class="text-red-700 text-xs mt-2 font-black animate-pulse flex items-center gap-1">
                            <span>⚠️</span> لازم تكتبي اسم العميلة هنا!
                        </p> 
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">رقم التليفون <span class="text-gray-400 font-normal">(اختياري)</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition @error('phone') border-red-500 bg-red-50 @enderror"
                        placeholder="01xxxxxxxxx">
                    @error('phone') 
                        <p class="text-red-700 text-[10px] mt-1 font-black">⚠️ رقم موبايل غير صحيح أو مكرر</p> 
                    @enderror
                </div>

                {{-- Is Traveler --}}
                <div>
                    <label class="inline-flex items-center cursor-pointer bg-orange-50 px-4 py-3 rounded-xl border border-orange-100 hover:bg-orange-100 transition w-full">
                        <input type="checkbox" name="is_traveler" value="1" {{ old('is_traveler') ? 'checked' : '' }}
                            class="w-5 h-5 text-orange-500 bg-white border-gray-300 rounded focus:ring-orange-400 transition">
                        <span class="mr-3 text-sm font-bold text-orange-700 flex items-center gap-2">
                            <span>✈️</span> العميلة من سفر (استعجال / من خارج المحافظة)
                        </span>
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3.5 rounded-2xl font-bold shadow-lg shadow-blue-200 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2 text-base">
                    <span>💾</span> حفظ العميلة
                </button>
            </div>
        </form>
    </div>

    <script>
        function clientForm() {
            return {
                preview: null,
                onImage(e) {
                    const file = e.target.files[0];
                    if (file) this.preview = URL.createObjectURL(file);
                    else this.preview = null;
                }
            }
        }
    </script>
</x-app-layout>
