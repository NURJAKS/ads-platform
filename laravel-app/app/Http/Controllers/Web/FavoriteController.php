<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Добавить в избранное
     */
    public function store($id)
    {
        $ad = Ad::findOrFail($id);

        Favorite::firstOrCreate([
            'user_id' => Auth::id(),
            'ad_id'   => $ad->id,
        ]);

        return redirect()->back()->with('success', 'Добавлено в избранное');
    }

    /**
     * Удалить из избранного
     */
    public function destroy($id)
    {
        Favorite::where('user_id', Auth::id())
            ->where('ad_id', $id)
            ->delete();

        return redirect()->back()->with('success', 'Удалено из избранного');
    }
}

