@extends('layouts.admin')

@section('title', 'Moderation')
@section('page-title', 'Ads Moderation')

@section('content')

<div x-data="adsModeration()" x-init="fetchAds()" class="bg-white rounded shadow p-6">

    {{-- Tabs --}}
    <div class="flex space-x-4 border-b mb-6">
        <button @click="currentStatus = 'pending'; fetchAds()" 
                :class="{'border-b-2 border-blue-500 text-blue-600': currentStatus === 'pending', 'text-gray-500': currentStatus !== 'pending'}"
                class="pb-2 font-medium focus:outline-none">
            Pending
        </button>
        <button @click="currentStatus = 'approved'; fetchAds()" 
                :class="{'border-b-2 border-green-500 text-green-600': currentStatus === 'approved', 'text-gray-500': currentStatus !== 'approved'}"
                class="pb-2 font-medium focus:outline-none">
            Approved
        </button>
        <button @click="currentStatus = 'rejected'; fetchAds()" 
                :class="{'border-b-2 border-red-500 text-red-600': currentStatus === 'rejected', 'text-gray-500': currentStatus !== 'rejected'}"
                class="pb-2 font-medium focus:outline-none">
            Rejected
        </button>
    </div>

    {{-- Loading --}}
    <div x-show="loading" class="text-center py-10 text-gray-500">
        Loading...
    </div>

    {{-- Table --}}
    <div x-show="!loading" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="ad in ads" :key="ad.id">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="ad.id"></td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900" x-text="ad.title"></div>
                            <div class="text-sm text-gray-500 truncate w-48" x-text="ad.description"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="ad.user?.name || 'Unknown'"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900" x-text="formatPrice(ad.price)"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="new Date(ad.created_at).toLocaleDateString()"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <template x-if="currentStatus === 'pending'">
                                <div class="flex space-x-2">
                                    <button @click="approveAd(ad.id)" class="text-green-600 hover:text-green-900 border border-green-600 rounded px-2 py-1 text-xs hover:bg-green-50">Approve</button>
                                    <button @click="openRejectModal(ad.id)" class="text-red-600 hover:text-red-900 border border-red-600 rounded px-2 py-1 text-xs hover:bg-red-50">Reject</button>
                                </div>
                            </template>
                            <template x-if="currentStatus !== 'pending'">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                      :class="{'bg-green-100 text-green-800': ad.status === 'approved', 'bg-red-100 text-red-800': ad.status === 'rejected'}">
                                    <span x-text="ad.status"></span>
                                </span>
                            </template>
                        </td>
                    </tr>
                </template>
                <template x-if="ads.length === 0">
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            No ads found in this status.
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    {{-- Pagination (Simplified) --}}
    <div x-show="pagination.total > 0" class="mt-4 flex justify-between items-center text-sm text-gray-600">
        <div>
            Showing <span x-text="pagination.from || 0"></span> to <span x-text="pagination.to || 0"></span> of <span x-text="pagination.total"></span>
        </div>
        <div class="flex space-x-2">
            <button @click="changePage(pagination.prev_page_url)" :disabled="!pagination.prev_page_url" class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50">Prev</button>
            <button @click="changePage(pagination.next_page_url)" :disabled="!pagination.next_page_url" class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50">Next</button>
        </div>
    </div>


    {{-- Reject Modal --}}
    <div x-show="rejectModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
         style="display: none;"
         x-transition.opacity>
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
            <h3 class="text-lg font-bold mb-4">Reject Ad</h3>
            <p class="mb-2 text-sm text-gray-600">Please provide a reason for rejection:</p>
            <textarea x-model="rejectReason" class="w-full border rounded p-2 focus:ring-2 focus:ring-red-500 mb-4" rows="3" placeholder="Reason..."></textarea>
            
            <div class="flex justify-end space-x-2">
                <button @click="rejectModalOpen = false" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                <button @click="submitReject()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Reject Ad</button>
            </div>
        </div>
    </div>

</div>

<script>
    function adsModeration() {
        return {
            currentStatus: 'pending',
            ads: [],
            pagination: {},
            loading: false,
            rejectModalOpen: false,
            rejectReason: '',
            targetAdId: null,

            async fetchAds(url = null) {
                this.loading = true;
                const endpoint = url || `/api/v1/admin/ads?status=${this.currentStatus}`;
                
                try {
                    // Using AXIOS which should handle XSRF if configured, or Fetch with headers
                    const response = await axios.get(endpoint);
                    if(response.data.success) {
                        this.ads = response.data.data.data;
                        this.pagination = response.data.data; // Store full pagination object
                    } else {
                        alert('Error fetching ads');
                    }
                } catch (error) {
                    console.error(error);
                    alert('Failed to load ads. Ensure you are logged in.');
                } finally {
                    this.loading = false;
                }
            },

            changePage(url) {
                if(url) this.fetchAds(url);
            },

            formatPrice(price) {
                return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price);
            },

            async approveAd(id) {
                if(!confirm('Approve this ad?')) return;

                try {
                    await axios.post(`/api/v1/admin/ads/${id}/approve`);
                    this.fetchAds(); // Refresh list
                    alert('Ad Approved!');
                } catch (error) {
                    console.error(error);
                    alert('Error approving ad');
                }
            },

            openRejectModal(id) {
                this.targetAdId = id;
                this.rejectReason = '';
                this.rejectModalOpen = true;
            },

            async submitReject() {
                if(!this.rejectReason) {
                    alert('Reason is required');
                    return;
                }

                try {
                    await axios.post(`/api/v1/admin/ads/${this.targetAdId}/reject`, {
                        reason: this.rejectReason
                    });
                    this.rejectModalOpen = false;
                    this.fetchAds(); // Refresh list
                    alert('Ad Rejected');
                } catch (error) {
                    console.error(error);
                    alert('Error rejecting ad');
                }
            }
        }
    }
</script>
@endsection
