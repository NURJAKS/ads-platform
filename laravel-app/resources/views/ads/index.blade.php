@extends('layouts.app')

@section('title', 'Все объявления')

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-r from-blue-600 via-purple-600 to-blue-600 text-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-2xl md:text-3xl font-bold mb-2">Найдите то, что ищете</h1>
            <p class="text-sm md:text-base text-blue-100">Тысячи объявлений от проверенных продавцов</p>
        </div>
    </div>
</div>

<!-- Search & Filters -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6">
    <div class="bg-white rounded-lg shadow-md p-4 border border-gray-200">
        <form id="adsFilterForm" action="{{ route('ads.index') }}" method="GET" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <label class="block text-gray-700 text-xs font-medium mb-1">Поиск</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Что вы ищете?"
                            class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <svg class="absolute left-2.5 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-gray-700 text-xs font-medium mb-1">Категория</label>
                    <select name="category_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <option value="">Все категории</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- City -->
                <div>
                    <label class="block text-gray-700 text-xs font-medium mb-1">Город</label>
                    <input type="text" name="city" value="{{ request('city') }}" placeholder="Город"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>

                <!-- Price Range -->
                <div>
                    <label class="block text-gray-700 text-xs font-medium mb-1">Цена</label>
                    <div class="flex space-x-1.5">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="От"
                            class="w-full px-2 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="До"
                            class="w-full px-2 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-1">
                <a href="{{ route('ads.index') }}" class="text-xs text-gray-600 hover:text-gray-800 font-medium transition-colors">
                    Сбросить фильтры
                </a>
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-lg text-sm font-semibold hover:from-blue-700 hover:to-purple-700 transition-all shadow-sm hover:shadow-md">
                    Найти
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Ads Grid -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    @if($ads->count() > 0)
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Найдено объявлений: {{ $ads->total() }}</h2>
            <div class="text-xs text-gray-600">
                Страница {{ $ads->currentPage() }} из {{ $ads->lastPage() }}
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3">
            @foreach($ads as $ad)
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden border border-gray-200 group cursor-pointer">
                    <a href="{{ route('ads.show', $ad->id) }}" class="block">
                        <!-- Image -->
                        <div class="relative h-40 bg-gray-100 overflow-hidden">
                            @if($ad->images->isNotEmpty())
                                <img src="{{ route('ads.image', $ad->images->first()->path) }}" 
                                     alt="{{ $ad->title }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Category Badge -->
                            <div class="absolute top-2 left-2">
                                <span class="bg-white/95 backdrop-blur-sm text-gray-700 text-[10px] font-medium px-2 py-0.5 rounded border border-gray-200">
                                    {{ $ad->category->name }}
                                </span>
                            </div>
                            
                            <!-- Favorite Button -->
                            <div class="absolute top-2 right-2">
                                @auth
                                    @if(Auth::user()->favorites()->where('ad_id', $ad->id)->exists())
                                        <form action="{{ route('favorites.remove', $ad->id) }}" method="POST" onclick="event.stopPropagation(); event.preventDefault(); this.submit();">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-8 h-8 bg-white/95 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white transition-all shadow-sm">
                                                <svg class="w-4 h-4 text-red-500 fill-current" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('favorites.add', $ad->id) }}" method="POST" onclick="event.stopPropagation(); event.preventDefault(); this.submit();">
                                            @csrf
                                            <button type="submit" class="w-8 h-8 bg-white/95 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white transition-all shadow-sm">
                                                <svg class="w-4 h-4 text-gray-400 hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" onclick="event.stopPropagation();" class="w-8 h-8 bg-white/95 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white transition-all shadow-sm">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    </a>
                                @endauth
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-3">
                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-900 mb-1 line-clamp-2 group-hover:text-blue-600 transition-colors leading-tight">
                                {{ $ad->title }}
                            </h3>

                            <!-- Price -->
                            <div class="mb-2">
                                <div class="text-lg font-bold text-gray-900">
                                    @if($ad->price)
                                        {{ number_format($ad->price, 0, ',', ' ') }} 〒
                                    @else
                                        <span class="text-gray-500 text-sm">Договорная</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Location & Date -->
                            <div class="flex items-center justify-between text-[11px] text-gray-500">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>{{ $ad->city }}</span>
                                </div>
                                <span>{{ $ad->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $ads->withQueryString()->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-16 w-16 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Объявлений не найдено</h3>
            <p class="text-sm text-gray-600 mb-4">Попробуйте изменить параметры поиска</p>
            <a href="{{ route('ads.index') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
                Сбросить фильтры
            </a>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('adsFilterForm');
    const categorySelect = form.querySelector('select[name="category_id"]');
    const cityInput = form.querySelector('input[name="city"]');
    const minPrice = form.querySelector('input[name="min_price"]');
    const maxPrice = form.querySelector('input[name="max_price"]');

    categorySelect?.addEventListener('change', () => form.submit());
    cityInput?.addEventListener('blur', () => {
        if (cityInput.value) form.submit();
    });
    minPrice?.addEventListener('blur', () => {
        if (minPrice.value) form.submit();
    });
    maxPrice?.addEventListener('blur', () => {
        if (maxPrice.value) form.submit();
    });
});
</script>
@endsection
