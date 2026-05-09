<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'max_albums',
        'billing_cycle',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_albums' => 'integer',
    ];

    /**
     * Obtener todas las suscripciones de este plan
     */
    public function artistSubscriptions()
    {
        return $this->hasMany(ArtistSubscription::class);
    }

    /**
     * Obtener el nombre amigable del plan
     */
    public function getDisplayNameAttribute()
    {
        return match($this->max_albums) {
            1 => 'Básico (1 álbum)',
            5 => 'Estándar (hasta 5 álbumes)',
            default => 'Premium (más de 10 álbumes)',
        };
    }
}
