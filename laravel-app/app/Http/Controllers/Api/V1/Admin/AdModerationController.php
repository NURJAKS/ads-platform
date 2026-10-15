<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use App\Models\Ad;
use App\Models\AdModerationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdModerationController extends Controller
{
    // GET /api/v1/admin/ads?status=pending
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return ApiResponse::error('Invalid status. Allowed values: pending, approved, rejected', null, 422);
        }

        $ads = Ad::with(['user', 'category', 'images'])
            ->where('status', $status)
            ->latest()
            ->paginate(20);

        return ApiResponse::paginated($ads);
    }

    // POST /api/v1/admin/ads/{id}/approve
    public function approve($id)
    {
        $ad = Ad::findOrFail($id);

        if ($ad->status !== 'pending') {
            return ApiResponse::error('Only pending ads can be approved', null, 422);
        }

        $oldStatus = $ad->status;

        $ad->update([
            'status' => 'approved'
        ]);

        AdModerationLog::create([
            'ad_id'      => $ad->id,
            'admin_id'   => Auth::id(),
            'old_status' => $oldStatus,
            'new_status' => 'approved',
            'comment'    => null,
        ]);

        return ApiResponse::success([
            'id' => $ad->id,
            'status' => $ad->status
        ], 'Ad approved successfully');
    }

    // POST /api/v1/admin/ads/{id}/reject
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $ad = Ad::findOrFail($id);

        if ($ad->status !== 'pending') {
            return ApiResponse::error('Only pending ads can be rejected', null, 422);
        }

        $oldStatus = $ad->status;

        $ad->update([
            'status' => 'rejected'
        ]);

        AdModerationLog::create([
            'ad_id'      => $ad->id,
            'admin_id'   => Auth::id(),
            'old_status' => $oldStatus,
            'new_status' => 'rejected',
            'comment'    => $request->reason,
        ]);

        return ApiResponse::success([
            'id' => $ad->id,
            'status' => $ad->status
        ], 'Ad rejected successfully');
    }

    // GET /api/v1/admin/moderation/logs
    public function logs()
    {
        $logs = AdModerationLog::with(['admin', 'ad'])
            ->latest()
            ->paginate(20);

        return ApiResponse::paginated($logs);
    }
}
