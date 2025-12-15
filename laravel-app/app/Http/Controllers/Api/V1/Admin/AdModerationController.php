<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
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
            return response()->json([
                'success' => false,
                'message' => 'Invalid status'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => Ad::with(['user', 'category'])
                ->where('status', $status)
                ->latest()
                ->paginate(20)
        ]);
    }

    // POST /api/v1/admin/ads/{id}/approve
    public function approve($id)
    {
        $ad = Ad::findOrFail($id);

        if ($ad->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending ads can be approved'
            ], 422);
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

        return response()->json([
            'success' => true,
            'message' => 'Ad approved',
            'data' => [
                'id' => $ad->id,
                'status' => $ad->status
            ]
        ]);
    }

    // POST /api/v1/admin/ads/{id}/reject
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $ad = Ad::findOrFail($id);

        if ($ad->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending ads can be rejected'
            ], 422);
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

        return response()->json([
            'success' => true,
            'message' => 'Ad rejected',
            'data' => [
                'id' => $ad->id,
                'status' => $ad->status
            ]
        ]);
    }

    // GET /api/v1/admin/moderation/logs
    public function logs()
    {
        return response()->json([
            'success' => true,
            'data' => AdModerationLog::with(['admin', 'ad'])
                ->latest()
                ->paginate(20)
        ]);
    }
}
