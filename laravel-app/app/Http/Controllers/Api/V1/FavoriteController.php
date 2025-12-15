<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use App\Models\Favorite;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Добавить объявление в избранное
     */
    public function store($id)
    {
        $ad = Ad::findOrFail($id);

        Favorite::firstOrCreate([
            'user_id' => Auth::id(),
            'ad_id'   => $ad->id,
        ]);

        return ApiResponse::success([
            'ad_id' => $ad->id,
        ], 'Added to favorites');
    }

    /**
     * Удалить объявление из избранного
     */
    public function destroy($id)
    {
        Favorite::where('user_id', Auth::id())
            ->where('ad_id', $id)
            ->delete();

        return ApiResponse::success([
            'ad_id' => $id,
        ], 'Removed from favorites');
    }

    /**
     * Получить список всех избранных объявлений пользователя
     */
    public function index()
    {
        $favorites = Favorite::with([
            'ad.images',
            'ad.category',
            'ad.user',
        ])
        ->where('user_id', Auth::id())
        ->get()
        ->pluck('ad'); // вернуть только объекты объявлений

        return ApiResponse::success($favorites);
    }

    /**
     * Получить список избранных объявлений пользователя (альтернативный метод)
     */
    public function myFavorites()
    {
        $userId = Auth::id();

        $favorites = Favorite::where('user_id', $userId)
            ->with(['ad.images', 'ad.category', 'ad.user'])
            ->get()
            ->map(fn($fav) => $fav->ad);

        return ApiResponse::success($favorites);
    }
}

