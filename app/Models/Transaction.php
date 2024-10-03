<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'saving_group_id',
        'subscriber_id',
        'amount',
        'status',
        'payment_date',
        'description',
    ];
    public function savingGroup()
    {
        return $this->belongsTo(SavingGroup::class);
    }

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }
}
