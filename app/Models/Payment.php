<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'saving_group_id',
        'subscriber_id',
        'day_number',
        'cycle_number',
        'payment_date',
    ];
    use HasFactory;
    public function savingGroup()
    {
        return $this->belongsTo(SavingGroup::class);
    }

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }
}
