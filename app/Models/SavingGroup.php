<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SavingGroup extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'subscribers_count',
        'amount_per_day',
        'days_per_cycle',
        'total_amount',
        'current_cycle',
        'current_day',
        'start_date',
        'end_date',
    ];
    protected static function boot(): void
    {
        parent::boot();
        static::creating(static function ($savingGroup) {
            // Make total amount=days_per_cycle * amount_per_day * subscribers_count in Model's triggers
            $total_amount=$savingGroup->days_per_cycle * $savingGroup->amount_per_day * $savingGroup->subscribers_count;
            $savingGroup->total_amount = $total_amount;
            // Make end date = created at + (days_per_cycle * subscribers_count) in Model's triggers
            
            $savingGroup->end_date = Carbon::parse($savingGroup->start_date)->addDays($savingGroup->days_per_cycle * $savingGroup->subscribers_count);
        });
    }
    public function subscribers(): HasMany
    {
        return $this->hasMany(Subscriber::class);
    }
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
