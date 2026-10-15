@extends('layouts.app')

@section('title', 'Редактировать объявление')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Редактировать объявление</h1>
        <p class="text-gray-600">Внесите изменения в ваше объявление</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8">
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                <strong class="font-bold">Ошибка!</strong>
                </div>
                <ul class="list-disc list-inside mt-2 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('ads.update', $ad->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

                <!-- Title -->
                <div>
                <label for="title" class="block text-gray-700 text-sm font-semibold mb-2">
                    Заголовок <span class="text-red-500">*</span>
                </label>
                    <input 
                        id="title"
                        type="text" 
                        name="title" 
                        value="{{ old('title', $ad->title) }}" 
                        required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        placeholder="Введите заголовок объявления">
                </div>

                <!-- Category -->
                <div>
                <label for="category_id" class="block text-gray-700 text-sm font-semibold mb-2">
                    Категория <span class="text-red-500">*</span>
                </label>
                    <select 
                        id="category_id"
                        name="category_id" 
                        required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $ad->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Price and City Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Price -->
                    <div>
                    <label for="price" class="block text-gray-700 text-sm font-semibold mb-2">Цена (〒)</label>
                        <input 
                            id="price"
                            type="number" 
                            name="price" 
                            value="{{ old('price', $ad->price) }}"
                            min="0"
                            step="0.01"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="0">
                    </div>

                    <!-- City -->
                    <div>
                    <label for="city" class="block text-gray-700 text-sm font-semibold mb-2">
                        Город <span class="text-red-500">*</span>
                    </label>
                        <input 
                            id="city"
                            type="text" 
                            name="city" 
                            value="{{ old('city', $ad->city) }}" 
                            required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="Введите город">
                    </div>
                </div>

                <!-- Description -->
                <div>
                <label for="description" class="block text-gray-700 text-sm font-semibold mb-2">
                    Описание <span class="text-red-500">*</span>
                </label>
                    <textarea 
                        id="description"
                        name="description" 
                    rows="8" 
                        required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
                        placeholder="Опишите ваше объявление подробно...">{{ old('description', $ad->description) }}</textarea>
                </div>

                <!-- Existing Images -->
                @if($ad->images->isNotEmpty())
                    <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-3">Текущие фотографии</label>
                    <p class="text-sm text-gray-500 mb-4">Отметьте фотографии, которые хотите удалить</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @foreach($ad->images as $image)
                                <div class="relative group">
                                <label class="block cursor-pointer">
                                    <div class="relative">
                                    <img src="{{ route('ads.image', $image->path) }}" 
                                             class="w-full h-32 object-cover rounded-lg border-2 transition-all {{ old('deleted_images') && in_array($image->id, old('deleted_images')) ? 'border-red-400 opacity-50' : 'border-gray-200 group-hover:border-red-400' }}">
                                    <div class="absolute top-2 right-2">
                                            <div class="w-6 h-6 bg-white rounded-full flex items-center justify-center shadow-md">
                                        <input type="checkbox" 
                                               name="deleted_images[]" 
                                               value="{{ $image->id }}" 
                                                       {{ old('deleted_images') && in_array($image->id, old('deleted_images')) ? 'checked' : '' }}
                                                       class="w-4 h-4 text-red-600 rounded focus:ring-red-500"
                                                       onchange="this.closest('label').querySelector('img').classList.toggle('opacity-50'); this.closest('label').querySelector('img').classList.toggle('border-red-400');">
                                            </div>
                                        </div>
                                        <div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-xs p-2 rounded-b-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                            Нажмите для удаления
                                    </div>
                                    </div>
                                </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- New Images Upload -->
                <div>
                <label for="new_images" class="block text-gray-700 text-sm font-semibold mb-2">
                    Добавить новые фотографии
                    </label>
                <div class="mt-1 flex justify-center px-6 pt-8 pb-8 border-2 border-dashed border-gray-300 rounded-xl hover:border-blue-400 transition-colors bg-gray-50">
                        <div class="space-y-1 text-center w-full">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                            <label for="new_images" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Выберите файлы</span>
                                    <input 
                                        id="new_images" 
                                        name="new_images[]" 
                                        type="file" 
                                        multiple 
                                        accept="image/*" 
                                        class="sr-only"
                                        onchange="updateNewFileList(this)">
                                </label>
                                <p class="pl-1">или перетащите сюда</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF до 5MB</p>
                        <div id="newFileList" class="mt-4"></div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('ads.show', $ad->id) }}" class="text-gray-600 hover:text-gray-800 font-medium transition-colors">
                    ← Отмена
                </a>
                <button 
                    type="submit" 
                    class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    Сохранить изменения
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateNewFileList(input) {
    const fileList = document.getElementById('newFileList');
    const files = input.files;
    
    if (files.length === 0) {
        fileList.innerHTML = '';
        return;
    }
    
    const currentCount = {{ $ad->images->count() }};
    const deleteCount = document.querySelectorAll('input[name="deleted_images[]"]:checked').length;
    const newCount = files.length;
    const totalAfter = currentCount - deleteCount + newCount;
    
    if (totalAfter > 10) {
        alert('Всего фотографий не может быть больше 10. Сейчас будет: ' + totalAfter);
        input.value = '';
        fileList.innerHTML = '';
        return;
    }
    
    let html = '<div class="grid grid-cols-2 md:grid-cols-5 gap-3">';
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        html += `
            <div class="bg-white p-3 rounded-lg border border-gray-200">
                <div class="text-xs font-medium text-gray-700 truncate" title="${file.name}">${file.name}</div>
                <div class="text-xs text-gray-500 mt-1">${fileSize} MB</div>
            </div>
        `;
    }
    html += '</div>';
    fileList.innerHTML = html;
}
</script>
@endsection
