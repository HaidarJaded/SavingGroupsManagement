<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Subscriber extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'last_name',
        'full_name',
        'phone',
        'rank',
        'saving_group_id',
    ];
    protected static function boot(): void
    {
        parent::boot();
        static::creating(static function ($subscriber) {
            // Make full name=name+' '+last_name in Model's triggers
            $subscriber->full_name = $subscriber->name . ' ' . $subscriber->last_name;
        });
    }
    public function saving_group(): BelongsTo
    {
        return $this->belongsTo(SavingGroup::class);
    }
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
