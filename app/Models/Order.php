<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'item_type',
        'item_id',
        'item_name',
        'quantity',
        'amount',
        'commission',
        'musician_earnings',
        'stripe_session_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
