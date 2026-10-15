@extends('layouts.admin')

@section('title', 'Админ панель')
@section('page-title', 'Админ панель')

@section('content')
    <div class="space-y-6">
        <div class="bg-white p-6 rounded-lg shadow flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Здравствуйте, {{ auth()->user()->name }}</h2>
                <p class="text-gray-500 mt-1">Управление платформой объявлений</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.ads.index') }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Модерация</a>
                <a href="{{ route('admin.logs') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Логи</a>
                <a href="{{ route('ads.index') }}" class="px-4 py-2 bg-white border text-gray-700 rounded hover:bg-gray-50">К сайту</a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-gray-500 text-sm">Всего объявлений</p>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['ads_total'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-gray-500 text-sm">На модерации</p>
                <p class="text-3xl font-bold text-amber-600">{{ $stats['ads_pending'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-gray-500 text-sm">Одобрено</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['ads_approved'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-gray-500 text-sm">Отклонено</p>
                <p class="text-3xl font-bold text-red-600">{{ $stats['ads_rejected'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-gray-500 text-sm">Пользователи</p>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['users_total'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <p class="text-gray-500 text-sm">Администраторы</p>
                <p class="text-3xl font-bold text-purple-600">{{ $stats['users_admins'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-gray-800">Последние pending объявления</h3>
                <a href="{{ route('admin.ads.index', ['status' => 'pending']) }}" class="text-indigo-600 text-sm hover:underline">Все pending</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Объявление</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Пользователь</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($latestPending as $ad)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-500">{{ $ad->id }}</td>
                                <td class="px-4 py-2">
                                    <div class="font-medium text-gray-900">{{ $ad->title }}</div>
                                    <div class="text-xs text-gray-500">{{ $ad->category->name ?? '—' }}</div>
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $ad->user->name ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-500">{{ $ad->created_at->format('d.m.Y H:i') }}</td>
                                <td class="px-4 py-2 text-sm">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('ads.show', $ad->id) }}" target="_blank" class="text-indigo-600 hover:underline">Открыть</a>
                                        <form action="{{ route('admin.ads.approve', $ad->id) }}" method="POST" onsubmit="return confirm('Одобрить?');">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:underline">Одобрить</button>
                                        </form>
                                        <form action="{{ route('admin.ads.reject', $ad->id) }}" method="POST" onsubmit="return confirm('Отклонить?');">
                                            @csrf
                                            <input type="hidden" name="reason" value="Отклонено администратором">
                                            <button type="submit" class="text-red-600 hover:underline">Отклонить</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">Нет объявлений в статусе pending</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
