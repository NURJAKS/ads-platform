@extends('layouts.app')

@section('title', $ad->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumbs -->
    <nav class="mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('ads.index') }}" class="hover:text-blue-600 transition-colors">Главная</a></li>
            <li><span>/</span></li>
            <li><a href="{{ route('ads.index', ['category_id' => $ad->category_id]) }}" class="hover:text-blue-600 transition-colors">{{ $ad->category->name }}</a></li>
            <li><span>/</span></li>
            <li class="text-gray-900 font-medium">{{ Str::limit($ad->title, 50) }}</li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Images & Description -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Image Gallery -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                @if($ad->images->isNotEmpty())
                    <div class="relative">
                        <!-- Main Image -->
                        <div class="aspect-[4/3] bg-gray-50 flex items-center justify-center relative group">
                        <img src="{{ route('ads.image', $ad->images->first()->path) }}" 
                             alt="{{ $ad->title }}" 
                                 class="max-w-full max-h-full object-contain cursor-zoom-in transition-transform"
                             id="mainImage">
                        
                            <!-- Navigation Arrows -->
                            @if($ad->images->count() > 1)
                                <button onclick="previousImage()" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/90 backdrop-blur-sm p-3 rounded-full shadow-lg hover:bg-white transition-all opacity-0 group-hover:opacity-100">
                                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                        </button>
                                <button onclick="nextImage()" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/90 backdrop-blur-sm p-3 rounded-full shadow-lg hover:bg-white transition-all opacity-0 group-hover:opacity-100">
                                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                        </button>
                            @endif
                    </div>

                    <!-- Thumbnails -->
                    @if($ad->images->count() > 1)
                            <div class="p-4 bg-gray-50 border-t border-gray-200">
                                <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
                                    @foreach($ad->images as $index => $image)
                                        <button onclick="changeImage({{ $index }})" 
                                                class="thumbnail-btn flex-shrink-0 w-24 h-24 border-2 rounded-lg overflow-hidden hover:border-blue-500 focus:outline-none focus:border-blue-500 transition-colors {{ $index === 0 ? 'border-blue-500' : 'border-gray-200' }}">
                                            <img src="{{ route('ads.image', $image->path) }}" 
                                                 class="w-full h-full object-cover"
                                                 alt="Thumbnail {{ $index + 1 }}">
                                </button>
                            @endforeach
                                </div>
                        </div>
                    @endif
                    </div>
                @else
                    <div class="aspect-[4/3] bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                        <div class="text-center">
                            <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-gray-500">Нет изображений</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Description -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Описание</h2>
                <div class="text-gray-700 leading-relaxed whitespace-pre-line mb-6">
                    {{ $ad->description }}
                </div>
                
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-6 text-sm text-gray-500">
                    <span>ID: {{ $ad->id }}</span>
                        <span>Просмотров: {{ rand(10, 500) }}</span>
                    </div>
                    @auth
                        <button onclick="document.getElementById('complaintModal').classList.remove('hidden')" 
                                class="flex items-center space-x-2 text-red-500 hover:text-red-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>Пожаловаться</span>
                    </button>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="space-y-6">
            <!-- Price & Actions Card -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 sticky top-20">
                <div class="mb-4">
                    <div class="text-3xl font-bold text-gray-900 mb-2">
                        @if($ad->price)
                            {{ number_format($ad->price, 0, ',', ' ') }} 〒
                        @else
                            <span class="text-gray-600 text-2xl">Договорная</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500">
                        Опубликовано {{ $ad->created_at->isoFormat('D MMMM YYYY') }} г.
                    </p>
                </div>

                <div class="space-y-3 mb-6">
                    @auth
                        <!-- Favorite Button -->
                        @php 
                            $isFav = Auth::user()->favorites()->where('ad_id', $ad->id)->exists(); 
                        @endphp
                        <form action="{{ $isFav ? route('favorites.remove', $ad->id) : route('favorites.add', $ad->id) }}" method="POST">
                            @csrf 
                            @if($isFav) @method('DELETE') @endif
                            <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-3 rounded-lg font-semibold transition-all {{ $isFav ? 'bg-red-50 text-red-600 border-2 border-red-200 hover:bg-red-100' : 'bg-gray-100 text-gray-700 border-2 border-gray-200 hover:bg-gray-200' }}">
                                <svg class="w-5 h-5 {{ $isFav ? 'fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <span>{{ $isFav ? 'В избранном' : 'В избранное' }}</span>
                            </button>
                        </form>

                        @if(Auth::id() === $ad->user_id)
                            <div class="grid grid-cols-2 gap-3">
                                <a href="{{ route('ads.edit', $ad->id) }}" class="flex items-center justify-center space-x-2 px-4 py-3 bg-blue-50 text-blue-600 rounded-lg font-semibold hover:bg-blue-100 transition-colors border border-blue-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span>Редактировать</span>
                                </a>
                                <form action="{{ route('ads.destroy', $ad->id) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить это объявление?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-red-50 text-red-600 rounded-lg font-semibold hover:bg-red-100 transition-colors border border-red-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span>Удалить</span>
                        </button>
                                </form>
                    </div>
                        @endif
                @else
                        <a href="{{ route('login') }}" class="block w-full text-center px-4 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                        Войдите, чтобы добавить в избранное
                    </a>
                @endauth
                </div>

                <!-- Seller Info -->
                <div class="pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                            {{ strtoupper(substr($ad->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $ad->user->name }}</p>
                            <p class="text-sm text-gray-500">Продавец</p>
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <div class="pt-4 border-t border-gray-200 mt-4">
                    <div class="flex items-center space-x-2 text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="font-medium">{{ $ad->city }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Complaint Modal -->
@auth
<div id="complaintModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Пожаловаться на объявление</h3>
                <button onclick="document.getElementById('complaintModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form action="{{ route('ads.complaint', $ad->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Причина</label>
                        <select name="reason" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="spam">Спам</option>
                        <option value="fraud">Мошенничество</option>
                        <option value="inappropriate">Неприемлемый контент</option>
                        <option value="other">Другое</option>
                    </select>
                </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Комментарий</label>
                        <textarea name="comment" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Опишите проблему..."></textarea>
                </div>
                    <div class="flex space-x-3 pt-4">
                        <button type="submit" class="flex-1 bg-red-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                        Отправить
                    </button>
                        <button type="button" onclick="document.getElementById('complaintModal').classList.add('hidden')" class="flex-1 bg-gray-100 text-gray-700 px-4 py-3 rounded-lg font-semibold hover:bg-gray-200 transition-colors">
                        Отмена
                    </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endauth

<script>
let currentImageIndex = 0;
const images = @json($ad->images->pluck('path')->toArray());
const imageBaseUrl = '{{ route("ads.image", "") }}/';

function changeImage(index) {
    currentImageIndex = index;
    document.getElementById('mainImage').src = imageBaseUrl + images[index];
    
    // Update thumbnail borders
    document.querySelectorAll('.thumbnail-btn').forEach((btn, i) => {
        if (i === index) {
            btn.classList.add('border-blue-500');
            btn.classList.remove('border-gray-200');
        } else {
            btn.classList.remove('border-blue-500');
            btn.classList.add('border-gray-200');
        }
    });
}

function nextImage() {
    if (images.length > 1) {
        currentImageIndex = (currentImageIndex + 1) % images.length;
        changeImage(currentImageIndex);
    }
}

function previousImage() {
    if (images.length > 1) {
        currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
        changeImage(currentImageIndex);
    }
}
</script>
@endsection
