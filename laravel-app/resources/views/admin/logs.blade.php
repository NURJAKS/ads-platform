@extends('layouts.admin')

@section('title', 'Moderation Logs')
@section('page-title', 'Moderation History')

@section('content')

<div x-data="logsViewer()" x-init="fetchLogs()" class="bg-white rounded shadow p-6">

    <div x-show="loading" class="text-center py-10 text-gray-500">
        Loading logs...
    </div>

    <div x-show="!loading" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ad Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="log in logs" :key="log.id">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="log.id"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="log.admin?.name || 'Unknown'"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span x-text="log.ad?.title || 'Deleted Ad'"></span>
                            <span class="text-xs text-gray-400 block" x-text="'ID: ' + log.ad_id"></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center space-x-2">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800" x-text="log.old_status"></span>
                                <span>&rarr;</span>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                      :class="{'bg-green-100 text-green-800': log.new_status === 'approved', 'bg-red-100 text-red-800': log.new_status === 'rejected'}">
                                    <span x-text="log.new_status"></span>
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 italic" x-text="log.comment || '-'"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="new Date(log.created_at).toLocaleString()"></td>
                    </tr>
                </template>
                <template x-if="logs.length === 0">
                     <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            No logs found.
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

     {{-- Pagination --}}
    <div x-show="pagination.total > 0" class="mt-4 flex justify-between items-center text-sm text-gray-600">
        <div>
            Showing <span x-text="pagination.from || 0"></span> to <span x-text="pagination.to || 0"></span> of <span x-text="pagination.total"></span>
        </div>
        <div class="flex space-x-2">
            <button @click="changePage(pagination.prev_page_url)" :disabled="!pagination.prev_page_url" class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50">Prev</button>
            <button @click="changePage(pagination.next_page_url)" :disabled="!pagination.next_page_url" class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50">Next</button>
        </div>
    </div>

</div>

<script>
    function logsViewer() {
        return {
            logs: [],
            pagination: {},
            loading: false,

            async fetchLogs(url = null) {
                this.loading = true;
                const endpoint = url || '/api/v1/admin/moderation/logs';
                
                try {
                    const response = await axios.get(endpoint);
                    if(response.data.success) {
                        this.logs = response.data.data.data;
                        this.pagination = response.data.data;
                    }
                } catch (error) {
                    console.error(error);
                } finally {
                    this.loading = false;
                }
            },

            changePage(url) {
                if(url) this.fetchLogs(url);
            }
        }
    }
</script>
@endsection
