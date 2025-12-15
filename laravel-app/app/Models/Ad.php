<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class Ad extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'price',
        'city',
        'status'
    ];
    
    protected static function boot()
{
    parent::boot();

    static::deleting(function ($ad) {
        foreach ($ad->images as $img) {
            // удалить файл из MinIO
            Storage::disk('s3')->delete($img->path);

            // удалить запись
            $img->delete();
        }
    });
}

    public function moderationLogs()
    {
    return $this->hasMany(\App\Models\AdModerationLog::class);
    }
	
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(AdImage::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
