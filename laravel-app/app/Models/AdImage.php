<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AdImage extends Model
{
    protected $fillable = [
        'ad_id',
        'path'
    ];

    protected $appends = ['url'];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function getUrlAttribute()
    {
        if (!$this->path) {
            return null;
        }

        // Return valid public URL via our proxy
        // Force port 8000 for local development if not set
        $baseUrl = config('app.url');
        if (!str_contains($baseUrl, ':')) {
             $baseUrl .= ':8000';
        }
        return $baseUrl . '/api/v1/images/' . $this->path;
    }
}
