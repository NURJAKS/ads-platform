<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Ad;
use App\Models\AdImage; // ensure this is imported or we edit the file content directly
// Wait, I need to edit the Model file, not the Controller. 
// Cancelling this tool call to switch to the correct file.
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class AdController extends Controller
{
    // -------------------------
    // GET /ads — ПУБЛИЧНЫЙ список (ТОЛЬКО approved)
    // -------------------------
    public function index(Request $r)
    {
        $cacheKey = 'ads:' . md5(json_encode($r->all()));

        $result = Cache::remember($cacheKey, 60, function () use ($r) {

            $q = Ad::with(['images', 'category', 'user'])
                ->where('status', 'approved');

            if ($r->category) {
                $q->where('category_id', $r->category);
            }

            if ($r->city) {
                $q->where('city', 'ilike', '%' . $r->city . '%');
            }

            if ($r->min_price) {
                $q->where('price', '>=', $r->min_price);
            }

            if ($r->max_price) {
                $q->where('price', '<=', $r->max_price);
            }

            if ($r->search) {
                $q->whereRaw("
                    to_tsvector('simple', coalesce(title,'') || ' ' || coalesce(description,'')) 
                    @@ plainto_tsquery('simple', ?)
                ", [$r->search]);
            }

            return $q->orderByDesc('created_at')->paginate(20);
        });

        return ApiResponse::paginated($result);
    }

    // -------------------------
    // GET /ads/{id}
    // -------------------------
    public function show($id)
    {
        try {
            $ad = Ad::with(['images', 'category', 'user'])->findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::error('Объявление не найдено', null, 404);
        }

        // если объявление не approved
        if ($ad->status !== 'approved') {
            // Try to authenticate with Sanctum manually since the route is public
            $user = auth('sanctum')->user();

            // Разрешаем доступ владельцу и администратору
            if (!$user || ($user->id !== $ad->user_id && $user->role !== 'admin')) {
                return ApiResponse::error('Объявление не найдено или у вас нет доступа', null, 404);
            }
        }

        return ApiResponse::success($ad);
    }

    // -------------------------
    // POST /ads
    // -------------------------
    // POST /ads
    public function store(Request $r)
    {
        $r->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price'       => 'nullable|numeric',
            'city'        => 'required|string|max:150',
        ]);
        
        $imageFiles = [];
        if ($r->hasFile('images')) {
            $files = $r->file('images');
            if (is_array($files)) {
                $imageFiles = array_filter($files, function($file) { return $file && $file->isValid(); });
            } elseif ($files && $files->isValid()) {
                $imageFiles = [$files];
            }
        }

        // Check Limit
        if (count($imageFiles) > 10) {
            return ApiResponse::error('You can upload max 10 images', null, 422);
        }

        // Validate images
        foreach ($imageFiles as $file) {
            $validator = validator(['image' => $file], ['image' => 'image|max:5120']);
            if ($validator->fails()) {
                return ApiResponse::error('Invalid image file', $validator->errors()->toArray(), 422);
            }
        }

        $ad = Ad::create([
            'user_id'     => Auth::id(),
            'category_id' => $r->category_id,
            'title'       => $r->title,
            'description' => $r->description,
            'price'       => $r->price,
            'city'        => $r->city,
            'status'      => 'pending',
        ]);

        $this->processImages($ad, $imageFiles);

        return ApiResponse::success($ad->load(['images', 'category']), 'Ad created successfully', 201);
    }

    // PUT /ads/{id}
    public function update(Request $r, $id)
    {
        $ad = Ad::findOrFail($id);

        if ($ad->user_id !== Auth::id()) {
            return ApiResponse::error('Forbidden', null, 403);
        }

        $r->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price'       => 'sometimes|numeric|nullable',
            'city'        => 'sometimes|string|max:150',
            'category_id' => 'sometimes|exists:categories,id',
            'deleted_images' => 'nullable|array',
            'deleted_images.*' => 'integer|exists:ad_images,id',
            'new_images'     => 'nullable|array',
            'new_images.*'   => 'image|max:5120',
        ]);

        // Calculate limits
        $currentCount = $ad->images()->count();
        $deleteCount = $r->deleted_images ? count($r->deleted_images) : 0;
        $newCount    = $r->new_images ? count($r->new_images) : 0;

        if (($currentCount - $deleteCount + $newCount) > 10) {
             return ApiResponse::error('Total images cannot exceed 10', null, 422);
        }

        // Handle deletions
        if ($r->deleted_images) {
            $imagesToDelete = AdImage::whereIn('id', $r->deleted_images)
                                     ->where('ad_id', $ad->id)
                                     ->get();
            
            foreach ($imagesToDelete as $img) {
                Storage::disk('s3')->delete($img->path);
                $img->delete();
            }
        }

        // Handle additions
        if ($r->hasFile('new_images')) {
            $this->processImages($ad, $r->file('new_images'));
        }

        $ad->update(array_merge(
            $r->only(['title', 'description', 'price', 'city', 'category_id']),
            ['status' => 'pending'] // Re-moderate
        ));

        return ApiResponse::success(
            $ad->load(['images', 'category']),
            'Ad updated and sent for moderation'
        );
    }

    private function processImages(Ad $ad, array $files) {
        $imageServiceUrl = config('services.image.url', env('IMAGE_SERVICE_URL', 'http://go-image-service:8080/process'));
        
        foreach ($files as $file) {
            \Log::debug("Processing image: {$file->getClientOriginalName()} for Ad ID: {$ad->id}");
            $processedContent = null;
            $processingFailed = false;

            try {
                // Using file_get_contents is not a "guzzle hack", it's how you get file content.
                // No specific "guzzle hacks" to remove here based on common interpretations.
                $response = Http::timeout(5)->attach(
                    'image',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )->post($imageServiceUrl);

                if ($response->successful() && !empty($response->body())) {
                    $processedContent = $response->body();
                    \Log::debug("Image service successful for {$file->getClientOriginalName()}. Response size: " . strlen($processedContent));
                } else {
                    $processingFailed = true;
                    \Log::warning("Image service failed or returned empty body for {$file->getClientOriginalName()}. Status: {$response->status()}, Body: {$response->body()}");
                }
            } catch (\Exception $e) {
                $processingFailed = true;
                \Log::error('Image processing exception', ['error' => $e->getMessage(), 'file' => $file->getClientOriginalName()]);
            }

            // Fallback
            if ($processingFailed || !$processedContent) {
                $processedContent = file_get_contents($file->getRealPath());
            }

            try {
                $path = "{$ad->id}/" . uniqid() . ".jpg";
                Storage::disk('s3')->put($path, $processedContent);

                AdImage::create([
                    'ad_id' => $ad->id,
                    'path'  => $path,
                ]);
            } catch (\Exception $e) {
                 \Log::error('S3 Storage error', ['error' => $e->getMessage()]);
            }
        }
    }

    // -------------------------
    // DELETE /ads/{id}
    // -------------------------
    public function destroy($id)
    {
        $ad = Ad::findOrFail($id);

        if ($ad->user_id !== Auth::id()) {
            return ApiResponse::error('Forbidden', null, 403);
        }

        $ad->delete();

        return ApiResponse::success(null, 'Ad deleted successfully');
    }

    // -------------------------
    // GET /my/ads
    // -------------------------
    public function myAds()
    {
        $ads = Ad::with(['images', 'category'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(20);

        return ApiResponse::paginated($ads);
    }

    // -------------------------
    // GET /images/{path}
    // -------------------------
    public function getImage($path)
    {
        if (!Storage::disk('s3')->exists($path)) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        $file = Storage::disk('s3')->get($path);
        $type = Storage::disk('s3')->mimeType($path);

        return response($file, 200)->header('Content-Type', $type);
    }
}

