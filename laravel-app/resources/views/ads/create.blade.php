@extends('layouts.app')

@section('title', 'Создать объявление')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Создать новое объявление</h1>
        <p class="text-gray-600">Заполните форму ниже, чтобы разместить ваше объявление</p>
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

        <form action="{{ route('ads.store') }}" method="POST" enctype="multipart/form-data" id="adForm" class="space-y-6">
            @csrf

                <!-- Title -->
                <div>
                <label for="title" class="block text-gray-700 text-sm font-semibold mb-2">
                    Заголовок <span class="text-red-500">*</span>
                </label>
                    <input 
                        id="title"
                        type="text" 
                        name="title" 
                        value="{{ old('title') }}" 
                        required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    placeholder="Например: Продаю iPhone 13 Pro Max">
                <p class="mt-1 text-sm text-gray-500">Придумайте понятный и информативный заголовок</p>
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
                        <option value="">Выберите категорию</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
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
                            value="{{ old('price') }}"
                            min="0"
                            step="0.01"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="0">
                    <p class="mt-1 text-sm text-gray-500">Оставьте пустым для "Договорная"</p>
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
                            value="{{ old('city') }}" 
                            required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        placeholder="Например: Алматы">
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
                    placeholder="Опишите ваше объявление подробно: состояние, характеристики, условия продажи...">{{ old('description') }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Чем подробнее описание, тем больше откликов</p>
                </div>

                <!-- Images Upload -->
                <div>
                <label for="images" class="block text-gray-700 text-sm font-semibold mb-2">
                        Фотографии (максимум 10)
                    </label>
                <div class="mt-1 flex justify-center px-6 pt-8 pb-8 border-2 border-dashed border-gray-300 rounded-xl hover:border-blue-400 transition-colors bg-gray-50">
                        <div class="space-y-1 text-center w-full">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                            <label for="images" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Выберите файлы</span>
                                    <input 
                                        id="images" 
                                        name="images[]" 
                                        type="file" 
                                        multiple 
                                        accept="image/*" 
                                        class="sr-only"
                                        onchange="updateFileList(this)">
                                </label>
                                <p class="pl-1">или перетащите сюда</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF до 5MB, максимум 10 файлов</p>
                        <div id="fileList" class="mt-4"></div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('ads.index') }}" class="text-gray-600 hover:text-gray-800 font-medium transition-colors">
                    ← Назад к объявлениям
                </a>
                <button 
                    type="submit" 
                    class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    Создать объявление
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateFileList(input) {
    const fileList = document.getElementById('fileList');
    const files = input.files;
    
    if (files.length === 0) {
        fileList.innerHTML = '';
        return;
    }
    
    if (files.length > 10) {
        alert('Можно загрузить максимум 10 файлов');
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
