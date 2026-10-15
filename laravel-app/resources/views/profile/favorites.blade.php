@extends('layouts.app')

@section('title', 'Избранное')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
<div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Избранное</h1>
        <p class="text-gray-600">Ваши сохраненные объявления</p>
</div>

    @if($favorites->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($favorites as $favorite)
        @php $ad = $favorite->ad; @endphp
                <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 group cursor-pointer transform hover:-translate-y-1">
                    <a href="{{ route('ads.show', $ad->id) }}" class="block">
                        <!-- Image -->
                        <div class="relative h-48 bg-gray-100 overflow-hidden">
                @if($ad->images->isNotEmpty())
                                <img src="{{ route('ads.image', $ad->images->first()->path) }}" 
                                     alt="{{ $ad->title }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                @endif
                            
                            <!-- Category Badge -->
                     @if($ad->category)
                                <div class="absolute top-3 left-3">
                                    <span class="bg-white/90 backdrop-blur-sm text-gray-700 text-xs font-semibold px-2.5 py-1 rounded-full border border-gray-200">
                                        {{ $ad->category->name }}
                                    </span>
                                </div>
                     @endif

                            <!-- Favorite Button -->
                            <div class="absolute top-3 right-3">
                                <form action="{{ route('favorites.remove', $ad->id) }}" method="POST" onclick="event.stopPropagation(); event.preventDefault(); this.submit();">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white transition-all shadow-sm">
                                        <svg class="w-5 h-5 text-red-500 fill-current" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                </div>

                        <!-- Content -->
                        <div class="p-4">
                    <!-- Title -->
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                        {{ $ad->title }}
                            </h3>

                            <!-- Description -->
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                {{ Str::limit($ad->description, 80) }}
                            </p>

                    <!-- Price -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="text-2xl font-bold text-gray-900">
                                    @if($ad->price)
                        {{ number_format($ad->price, 0, ',', ' ') }} 〒
                                    @else
                                        <span class="text-gray-500 text-lg">Договорная</span>
                                    @endif
                    </div>
                </div>

                            <!-- Location & Date -->
                            <div class="flex items-center justify-between text-xs text-gray-500 pt-3 border-t border-gray-100">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <div class="mt-12">
            {{ $favorites->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <h3 class="text-xl font-bold text-gray-900 mb-2">В избранном пока пусто</h3>
            <p class="text-gray-600 mb-6">Сохраняйте понравившиеся объявления, чтобы не потерять их</p>
            <a href="{{ route('ads.index') }}" class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all shadow-md hover:shadow-lg">
                Перейти к объявлениям
            </a>
        </div>
    @endif
</div>
@endsection
