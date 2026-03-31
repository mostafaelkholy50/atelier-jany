<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span class="font-bold text-xl text-blue-900 flex items-center gap-2"><span>✏️</span> تعديل بيانات {{ $client->name }}</span>
            <a href="{{ route('clients.show', $client) }}" class="px-4 py-2 bg-white text-gray-700 font-bold rounded-xl border border-gray-200 shadow-sm hover:bg-gray-50 transition flex items-center gap-2 text-sm">
                🔙 الرجوع
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto" x-data="clientEditForm('{{ $client->image ? asset('app-storage/' . $client->image) : '' }}')">
        <form action="{{ route('clients.update', $client) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
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
                                <span class="text-[11px] text-blue-500 font-bold mt-1">بدون صورة</span>
                            </div>
                        </template>
                        <div class="absolute -bottom-1 -left-1 bg-blue-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm shadow-md border-2 border-white">✏️</div>
                    </div>
                    <input type="file" name="image" x-ref="imageFile" @change="onImage" class="hidden" accept="image/*">
                    <p class="text-xs text-gray-400 text-center">اضغطي لتغيير الصورة (اختياري)</p>
                </div>

                <div class="cloud-divider"></div>

                {{-- Name --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">اسم العميلة <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $client->name) }}" required
                        class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">رقم التليفون <span class="text-gray-400 font-normal">(اختياري)</span></label>
                    <input type="text" name="phone" value="{{ old('phone', $client->phone) }}"
                        class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring focus:ring-blue-100 transition"
                        placeholder="01xxxxxxxxx">
                </div>

                {{-- Is Traveler --}}
                <div>
                    <label class="inline-flex items-center cursor-pointer bg-orange-50 px-4 py-3 rounded-xl border border-orange-100 hover:bg-orange-100 transition w-full">
                        <input type="checkbox" name="is_traveler" value="1" {{ old('is_traveler', $client->is_traveler) ? 'checked' : '' }}
                            class="w-5 h-5 text-orange-500 bg-white border-gray-300 rounded focus:ring-orange-400 transition">
                        <span class="mr-3 text-sm font-bold text-orange-700 flex items-center gap-2">
                            <span>✈️</span> العميلة من سفر (استعجال / من خارج المحافظة)
                        </span>
                    </label>
                </div>

                {{-- Submit --}}
                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3.5 rounded-2xl font-bold shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        <span>💾</span> حفظ التعديلات
                    </button>
                    <a href="{{ route('clients.show', $client) }}"
                        class="px-5 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-2xl font-bold transition flex items-center gap-2">
                        إلغاء
                    </a>
                </div>
            </div>
        </form>
    </div>

    <script>
        function clientEditForm(existingImage) {
            return {
                preview: existingImage || null,
                onImage(e) {
                    const file = e.target.files[0];
                    if (file) this.preview = URL.createObjectURL(file);
                }
            }
        }
    </script>
</x-app-layout>
