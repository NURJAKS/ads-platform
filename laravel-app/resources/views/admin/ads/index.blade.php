@extends('layouts.admin')

@section('title', 'Moderation')
@section('page-title', 'Модерация объявлений')

@section('content')

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
@endif

<div class="bg-white rounded shadow p-6">
    {{-- Tabs --}}
    <div class="flex space-x-4 border-b mb-6">
        <a href="{{ route('admin.ads.index', ['status' => 'pending']) }}" 
           class="pb-2 font-medium focus:outline-none {{ $status === 'pending' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500' }}">
            На модерации
        </a>
        <a href="{{ route('admin.ads.index', ['status' => 'approved']) }}" 
           class="pb-2 font-medium focus:outline-none {{ $status === 'approved' ? 'border-b-2 border-green-500 text-green-600' : 'text-gray-500' }}">
            Одобренные
        </a>
        <a href="{{ route('admin.ads.index', ['status' => 'rejected']) }}" 
           class="pb-2 font-medium focus:outline-none {{ $status === 'rejected' ? 'border-b-2 border-red-500 text-red-600' : 'text-gray-500' }}">
            Отклоненные
        </a>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Объявление</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Пользователь</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Цена</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($ads as $ad)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ad->id }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($ad->images->isNotEmpty())
                                    <img class="h-10 w-10 rounded object-cover mr-3" src="{{ route('ads.image', $ad->images->first()->path) }}" alt="">
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $ad->title }}</div>
                                    <div class="text-sm text-gray-500 truncate w-48">{{ \Illuminate\Support\Str::limit($ad->description, 50) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ad->user->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ number_format($ad->price, 0, ',', ' ') }} ₽</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ad->created_at->format('d.m.Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($status === 'pending')
                                <div class="flex space-x-2">
                                    <form action="{{ route('admin.ads.approve', $ad->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Одобрить объявление?')" class="text-green-600 hover:text-green-900 border border-green-600 rounded px-2 py-1 text-xs hover:bg-green-50">
                                            Одобрить
                                        </button>
                                    </form>
                                    <button onclick="openRejectModal({{ $ad->id }})" class="text-red-600 hover:text-red-900 border border-red-600 rounded px-2 py-1 text-xs hover:bg-red-50">
                                        Отклонить
                                    </button>
                                    <a href="{{ route('ads.show', $ad->id) }}" class="text-blue-600 hover:text-blue-900 border border-blue-600 rounded px-2 py-1 text-xs hover:bg-blue-50" target="_blank">
                                        Просмотр
                                    </a>
                                </div>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $ad->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $ad->status === 'approved' ? 'Одобрено' : 'Отклонено' }}
                                </span>
                                <a href="{{ route('ads.show', $ad->id) }}" class="ml-2 text-blue-600 hover:text-blue-900 text-xs" target="_blank">
                                    Просмотр
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            Объявлений со статусом "{{ $status }}" не найдено
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $ads->links() }}
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h3 class="text-lg font-bold mb-4">Отклонить объявление</h3>
        <form id="rejectForm" method="POST" action="">
            @csrf
            <p class="mb-2 text-sm text-gray-600">Укажите причину отклонения:</p>
            <textarea name="reason" required class="w-full border rounded p-2 focus:ring-2 focus:ring-red-500 mb-4" rows="3" placeholder="Причина отклонения..."></textarea>
            
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Отмена</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Отклонить</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal(adId) {
        document.getElementById('rejectForm').action = '{{ route("admin.ads.reject", ":id") }}'.replace(':id', adId);
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejectForm').reset();
    }

    // Close modal on outside click
    document.getElementById('rejectModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeRejectModal();
        }
    });
</script>

@endsection
