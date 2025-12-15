<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdModerationLog extends Model
{
    protected $fillable = [
        'ad_id',
        'admin_id',
        'old_status',
        'new_status',
        'comment',
    ];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
