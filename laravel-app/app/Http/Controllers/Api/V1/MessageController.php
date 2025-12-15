<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Ad;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * === [1] SEND MESSAGE ===
     */
    public function store(Request $r, $ad_id)
    {
        $r->validate([
            'message' => 'required|string',
            'receiver_id' => 'required|exists:users,id'
        ]);

        if ($r->receiver_id == Auth::id()) {
            return ApiResponse::error('You cannot send messages to yourself.', null, 422);
        }

        $ad = Ad::findOrFail($ad_id);

        $msg = Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $r->receiver_id,
            'ad_id'       => $ad->id,
            'message'     => $r->message
        ]);

        return ApiResponse::success($msg->load(['sender', 'receiver', 'ad']), 'Message sent successfully', 201);
    }

    /**
     * === [2] DIALOG LIST (ALL CHATS FOR USER) ===
     * This version is PostgreSQL safe.
     */
    public function dialogs()
    {
        $userId = Auth::id();

        // 1) Group messages to find last message per dialog
        $base = Message::selectRaw("
                ad_id,
                CASE 
                    WHEN sender_id = ? THEN receiver_id
                    ELSE sender_id
                END AS other_user_id,
                MAX(created_at) AS last_message_at
            ", [$userId])
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
            })
            ->groupBy('ad_id', 'other_user_id')
            ->orderBy('last_message_at', 'DESC')
            ->get();

        // 2) Enrich each dialog with additional data
        foreach ($base as $d) {

            // Last message text
            $d->last_message = Message::where('ad_id', $d->ad_id)
                ->where(function ($q) use ($userId, $d) {
                    $q->where(function ($x) use ($userId, $d) {
                        $x->where('sender_id', $userId)
                          ->where('receiver_id', $d->other_user_id);
                    })->orWhere(function ($x) use ($userId, $d) {
                        $x->where('sender_id', $d->other_user_id)
                          ->where('receiver_id', $userId);
                    });
                })
                ->orderBy('created_at', 'DESC')
                ->value('message');

            // Unread count
            $d->unread_count = Message::where('ad_id', $d->ad_id)
                ->where('receiver_id', $userId)
                ->whereNull('read_at')
                ->count();

            // Associated data
            $d->ad   = Ad::find($d->ad_id);
            $d->user = User::select('id', 'name', 'email', 'role')
                            ->find($d->other_user_id);
        }

        return ApiResponse::success($base);
    }

    /**
     * === [3] FULL CHAT BETWEEN TWO USERS FOR ONE AD ===
     */
    public function chat($ad_id, $other_user_id)
    {
        $userId = Auth::id();

        $messages = Message::where('ad_id', $ad_id)
            ->where(function ($q) use ($userId, $other_user_id) {
                $q->where(function ($x) use ($userId, $other_user_id) {
                    $x->where('sender_id', $userId)
                      ->where('receiver_id', $other_user_id);
                })
                ->orWhere(function ($x) use ($userId, $other_user_id) {
                    $x->where('sender_id', $other_user_id)
                      ->where('receiver_id', $userId);
                });
            })
            ->with(['sender', 'receiver', 'ad'])
            ->orderBy('created_at', 'ASC')
            ->get();

        // Mark unread as read
        Message::where('ad_id', $ad_id)
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return ApiResponse::success($messages);
    }

    /**
     * === [4] MY MESSAGES (DIALOG OVERVIEW, SAME AS dialogs) ===
     */
    public function myMessages()
    {
        return $this->dialogs();
    }
}
