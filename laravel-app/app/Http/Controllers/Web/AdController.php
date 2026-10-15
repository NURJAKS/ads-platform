<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Category;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class AdController extends Controller
{
    /**
     * Список объявлений (публичный, только approved)
     */
    public function index(Request $request)
    {
        $query = Ad::with(['images', 'category', 'user'])
            ->where('status', 'approved');

        // Фильтры
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('city')) {
            $query->where('city', 'ilike', '%' . $request->city . '%');
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('search')) {
            $query->whereRaw("
                to_tsvector('simple', coalesce(title,'') || ' ' || coalesce(description,'')) 
                @@ plainto_tsquery('simple', ?)
            ", [$request->search]);
        }

        $ads = $query->orderByDesc('created_at')->paginate(20);
        $categories = Category::orderBy('name')->get();

        return view('ads.index', compact('ads', 'categories'));
    }

    /**
     * Показать объявление
     */
    public function show($id)
    {
        $ad = Ad::with(['images', 'category', 'user'])->findOrFail($id);

        // Если не approved, проверяем доступ
        if ($ad->status !== 'approved') {
            if (!Auth::check() || (Auth::id() !== $ad->user_id && Auth::user()->role !== 'admin')) {
                abort(404);
            }
        }

        $isFavorite = false;
        if (Auth::check()) {
            $isFavorite = Favorite::where('user_id', Auth::id())
                ->where('ad_id', $ad->id)
                ->exists();
        }

        return view('ads.show', compact('ad', 'isFavorite'));
    }

    /**
     * Форма создания объявления
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('ads.create', compact('categories'));
    }

    /**
     * Сохранить объявление
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price'       => 'nullable|numeric',
            'city'        => 'required|string|max:150',
            'images'      => 'nullable|array|max:10',
            'images.*'    => 'image|max:5120',
        ]);

        $ad = Ad::create([
            'user_id'     => Auth::id(),
            'category_id' => $validated['category_id'],
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'price'       => $validated['price'] ?? null,
            'city'        => $validated['city'],
            'status'      => 'pending',
        ]);

        // Обработка изображений
        if ($request->hasFile('images')) {
            $this->processImages($ad, $request->file('images'));
        }

        return redirect()->route('ads.show', $ad->id)
            ->with('success', 'Объявление создано и отправлено на модерацию');
    }

    /**
     * Форма редактирования
     */
    public function edit($id)
    {
        $ad = Ad::with(['images', 'category'])->findOrFail($id);

        // Проверка прав
        if ($ad->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $categories = Category::orderBy('name')->get();
        return view('ads.edit', compact('ad', 'categories'));
    }

    /**
     * Обновить объявление
     */
    public function update(Request $request, $id)
    {
        $ad = Ad::findOrFail($id);

        // Проверка прав
        if ($ad->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price'       => 'sometimes|numeric|nullable',
            'city'        => 'sometimes|string|max:150',
            'category_id' => 'sometimes|exists:categories,id',
            'deleted_images' => 'nullable|array',
            'deleted_images.*' => 'integer|exists:ad_images,id',
            'new_images'     => 'nullable|array|max:10',
            'new_images.*'   => 'image|max:5120',
        ]);

        // Проверка лимита изображений
        $currentCount = $ad->images()->count();
        $deleteCount = $request->deleted_images ? count($request->deleted_images) : 0;
        $newCount = $request->hasFile('new_images') ? count($request->file('new_images')) : 0;

        if (($currentCount - $deleteCount + $newCount) > 10) {
            return back()->withErrors(['images' => 'Всего изображений не может быть больше 10']);
        }

        // Удаление изображений
        if ($request->deleted_images) {
            foreach ($ad->images()->whereIn('id', $request->deleted_images)->get() as $img) {
                Storage::disk('s3')->delete($img->path);
                $img->delete();
            }
        }

        // Добавление новых изображений
        if ($request->hasFile('new_images')) {
            $this->processImages($ad, $request->file('new_images'));
        }

        // Обновление данных
        $ad->update(array_merge(
            $request->only(['title', 'description', 'price', 'city', 'category_id']),
            ['status' => 'pending'] // Отправляем на повторную модерацию
        ));

        return redirect()->route('ads.show', $ad->id)
            ->with('success', 'Объявление обновлено и отправлено на модерацию');
    }

    /**
     * Удалить объявление
     */
    public function destroy($id)
    {
        $ad = Ad::findOrFail($id);

        if ($ad->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $ad->delete();

        return redirect()->route('ads.index')
            ->with('success', 'Объявление удалено');
    }

    /**
     * Мои объявления
     */
    public function myAds()
    {
        $ads = Ad::with(['images', 'category'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('profile.ads', compact('ads'));
    }

    /**
     * Избранное
     */
    public function favorites()
    {
        $favorites = Favorite::where('user_id', Auth::id())
            ->with(['ad.images', 'ad.category', 'ad.user'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('profile.favorites', compact('favorites'));
    }

    /**
     * Получить изображение
     */
    public function getImage($path)
    {
        if (!Storage::disk('s3')->exists($path)) {
            abort(404);
        }

        $file = Storage::disk('s3')->get($path);
        $type = Storage::disk('s3')->mimeType($path);

        return response($file, 200)->header('Content-Type', $type);
    }

    /**
     * Обработка изображений
     */
    private function processImages(Ad $ad, array $files)
    {
        // Используем имя сервиса из docker-compose для внутренней связи
        $imageServiceUrl = env('IMAGE_SERVICE_URL', 'http://go-image-service:8080/process');

        foreach ($files as $file) {
            $processedContent = null;
            $processingFailed = false;

            try {
                $response = Http::timeout(5)->attach(
                    'image',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )->post($imageServiceUrl);

                if ($response->successful() && !empty($response->body())) {
                    $processedContent = $response->body();
                } else {
                    $processingFailed = true;
                }
            } catch (\Exception $e) {
                $processingFailed = true;
                \Log::error('Image processing exception', ['error' => $e->getMessage()]);
            }

            // Fallback - используем оригинал
            if ($processingFailed || !$processedContent) {
                $processedContent = file_get_contents($file->getRealPath());
            }

            try {
                $path = "{$ad->id}/" . uniqid() . ".jpg";
                Storage::disk('s3')->put($path, $processedContent);

                $ad->images()->create([
                    'path' => $path,
                ]);
            } catch (\Exception $e) {
                \Log::error('S3 Storage error', ['error' => $e->getMessage()]);
            }
        }
    }
}
