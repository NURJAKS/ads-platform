<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        // Validate file
        $request->validate([
            'image' => 'required|image|max:5120', // 5 MB
        ]);

        $file = $request->file('image');

        // Send file to Go-image service
        $response = Http::attach(
            'image',
            file_get_contents($file->getRealPath()),
            $file->getClientOriginalName()
        )->post(config('services.image.url', env('IMAGE_SERVICE_URL')));

        if ($response->failed()) {
            return ApiResponse::error(
                'Image service failed',
                [
                    'status' => $response->status(),
                    'body'   => $response->body()
                ],
                500
            );
        }

        // ALWAYS get binary content correctly
        $processed = (string) $response->getBody();

        $filename = 'uploads/' . uniqid() . '.jpg';

        Storage::disk('s3')->put($filename, $processed);

        return ApiResponse::success([
            'url'  => Storage::disk('s3')->url($filename),
            'path' => $filename
        ], 'Image uploaded successfully');
    }
}
