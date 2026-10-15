@extends('layouts.app')

@section('title', 'Мои объявления')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Мои объявления</h1>
            <p class="text-gray-600">Управляйте своими объявлениями</p>
        </div>
        <a href="{{ route('ads.create') }}" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Разместить объявление</span>
    </a>
</div>

    @if($ads->count() > 0)
        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Объявление
                    </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Цена
                    </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Статус
                    </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Создано
                    </th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Действия
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($ads as $ad)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                            <div class="flex items-center">
                                        <div class="flex-shrink-0 h-16 w-16 rounded-lg overflow-hidden border border-gray-200">
                                    @if($ad->images->isNotEmpty())
                                                <img class="h-full w-full object-cover" src="{{ route('ads.image', $ad->images->first()->path) }}" alt="">
                                    @else
                                                <div class="h-full w-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900">
                                                <a href="{{ route('ads.show', $ad->id) }}" class="hover:text-blue-600 transition-colors">{{ $ad->title }}</a>
                                            </div>
                                            <div class="text-sm text-gray-500 mt-1">{{ $ad->category->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">
                                        @if($ad->price)
                                            {{ number_format($ad->price, 0, ',', ' ') }} 〒
                                        @else
                                            <span class="text-gray-500">Договорная</span>
                                        @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($ad->status === 'approved')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                    Активно
                                </span>
                            @elseif($ad->status === 'pending')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                            <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                                    На модерации
                                </span>
                            @elseif($ad->status === 'rejected')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                    Отклонено
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $ad->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                        <a href="{{ route('ads.edit', $ad->id) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors font-medium">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Редактировать
                                        </a>
                                        <form action="{{ route('ads.destroy', $ad->id) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить это объявление?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors font-medium">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Удалить
                                            </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
            </div>
        
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $ads->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-xl font-bold text-gray-900 mb-2">У вас пока нет объявлений</h3>
            <p class="text-gray-600 mb-6">Создайте первое объявление и начните продавать</p>
            <a href="{{ route('ads.create') }}" class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all shadow-md hover:shadow-lg">
                Разместить первое объявление
            </a>
        </div>
    @endif
</div>
@endsection
