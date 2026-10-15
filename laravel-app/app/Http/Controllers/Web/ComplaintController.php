<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    /**
     * Создать жалобу
     */
    public function store(Request $request, $id)
    {
        $ad = Ad::findOrFail($id);

        // Нельзя жаловаться на свое объявление
        if ($ad->user_id === Auth::id()) {
            return redirect()->back()->with('error', 'Нельзя жаловаться на свое объявление');
        }

        $validated = $request->validate([
            'reason'  => 'required|string|in:spam,fraud,inappropriate,other',
            'comment' => 'nullable|string|max:1000',
        ]);

        Complaint::create([
            'user_id'  => Auth::id(),
            'ad_id'    => $ad->id,
            'reason'   => $validated['reason'],
            'comment'  => $validated['comment'] ?? null,
            'status'   => 'pending',
        ]);

        return redirect()->back()->with('success', 'Жалоба отправлена. Спасибо за ваше обращение!');
    }
}
