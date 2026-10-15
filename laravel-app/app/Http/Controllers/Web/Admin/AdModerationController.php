<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdModerationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdModerationController extends Controller
{
    /**
     * Список объявлений для модерации
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            $status = 'pending';
        }

        $ads = Ad::with(['user', 'category', 'images'])
            ->where('status', $status)
            ->latest()
            ->paginate(20);

        return view('admin.ads.index', compact('ads', 'status'));
    }

    /**
     * Одобрить объявление
     */
    public function approve($id)
    {
        $ad = Ad::findOrFail($id);

        if ($ad->status !== 'pending') {
            return redirect()->back()->with('error', 'Можно одобрять только объявления со статусом "pending"');
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

        return redirect()->back()->with('success', 'Объявление одобрено');
    }

    /**
     * Отклонить объявление
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $ad = Ad::findOrFail($id);

        if ($ad->status !== 'pending') {
            return redirect()->back()->with('error', 'Можно отклонять только объявления со статусом "pending"');
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

        return redirect()->back()->with('success', 'Объявление отклонено');
    }

    /**
     * История модерации
     */
    public function logs()
    {
        $logs = AdModerationLog::with(['admin', 'ad'])
            ->latest()
            ->paginate(20);

        return view('admin.logs', compact('logs'));
    }
}

