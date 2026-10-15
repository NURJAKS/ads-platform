@extends('layouts.admin')

@section('title', 'Moderation Logs')
@section('page-title', 'История модерации')

@section('content')

<div class="bg-white rounded shadow p-6">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Администратор</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Объявление</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действие</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Комментарий</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($logs as $log)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $log->admin->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($log->ad)
                                <a href="{{ route('ads.show', $log->ad_id) }}" class="text-blue-600 hover:text-blue-900" target="_blank">
                                    {{ $log->ad->title }}
                                </a>
                                <span class="text-xs text-gray-400 block">ID: {{ $log->ad_id }}</span>
                            @else
                                <span class="text-gray-400 italic">Удаленное объявление</span>
                                <span class="text-xs text-gray-400 block">ID: {{ $log->ad_id }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center space-x-2">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $log->old_status }}
                                </span>
                                <span>&rarr;</span>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $log->new_status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $log->new_status === 'approved' ? 'Одобрено' : 'Отклонено' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 italic">{{ $log->comment ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            Записей в журнале модерации не найдено
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>

@endsection
