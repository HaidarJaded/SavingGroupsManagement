<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscribersSavingGroups extends Model
{
    use HasFactory;
    protected $fillable = [
        'subscriber_id',
        'saving_group_id',
        'subscriber_code',
    ];
    protected static function boot(): void
    {
        parent::boot();
        static::creating(static function ($subscribersSavingGroups) {
            // Generate subscriber_code automatically from 6 chars 

            $subscribersSavingGroups->subscriber_code = static::generateUniqueCode();
        });
    }
    public static function generateUniqueCode(): string
    {
        $code = '';
        do {
            $code = str()->random(6);
        } while (self::where('subscriber_code', $code)->exists());
        return $code;
    }
}
