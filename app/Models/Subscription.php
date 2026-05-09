<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'musician_profile_id',
        'stripe_subscription_id',
        'stripe_customer_id',
        'plan_type',
        'storage_gb',
        'price',
        'status',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'next_billing_at',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'next_billing_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Relaciones
    public function musicianProfile()
    {
        return $this->belongsTo(MusicianProfile::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForMusician($query, $musicianProfileId)
    {
        return $query->where('musician_profile_id', $musicianProfileId);
    }

    // Métodos de ayuda
    public function isActive()
    {
        return $this->status === 'active' && $this->ends_at && $this->ends_at->isFuture();
    }

    public function isOnTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function getStorageUsedPercentage()
    {
        if (!$this->storage_gb || $this->storage_gb <= 0) {
            return 0;
        }

        $usedGb = $this->getStorageUsedGb();
        return min(100, ($usedGb / (float) $this->storage_gb) * 100);
    }

    public function getStorageUsedGb(): float
    {
        $bytes = $this->calculateStorageUsedBytes();
        return round($bytes / 1024 / 1024 / 1024, 3);
    }

    public function getDaysUntilExpiration()
    {
        if (!$this->ends_at) {
            return null;
        }

        // Mostrar días enteros aproximados (sin decimales largos)
        $days = now()->diffInSeconds($this->ends_at, false) / 86400;
        return max(0, (int) ceil($days));
    }

    private function calculateStorageUsedBytes(): int
    {
        $profile = $this->musicianProfile;
        if (!$profile) {
            return 0;
        }

        $profile->loadMissing(['songs', 'albums', 'merch']);

        $bytes = 0;

        // Perfil del músico (puede estar en public/images/band-logos o en storage/public)
        if (!empty($profile->image_path)) {
            $bytes += $this->fileSizeFromPublicPath('images/band-logos/' . $profile->image_path);
            $bytes += $this->fileSizeFromPublicDisk($profile->image_path);
        }

        foreach ($profile->songs as $song) {
            $bytes += $this->fileSizeFromPublicDisk($song->file_path);
            $bytes += $this->fileSizeFromPublicDisk($song->cover_path);
        }

        foreach ($profile->albums as $album) {
            $bytes += $this->fileSizeFromPublicDisk($album->cover_path);
        }

        foreach ($profile->merch as $merch) {
            $bytes += $this->fileSizeFromPublicDisk($merch->image_path);
        }

        return $bytes;
    }

    private function fileSizeFromPublicDisk(?string $path): int
    {
        if (!$path) {
            return 0;
        }

        if (!Storage::disk('public')->exists($path)) {
            return 0;
        }

        return (int) Storage::disk('public')->size($path);
    }

    private function fileSizeFromPublicPath(?string $relativePath): int
    {
        if (!$relativePath) {
            return 0;
        }

        $fullPath = public_path($relativePath);
        if (!is_file($fullPath)) {
            return 0;
        }

        return (int) filesize($fullPath);
    }

    // Planos disponibles
    public static function getPlans()
    {
        return [
            'basic' => [
                'name' => 'Básico',
                'storage_gb' => 2,
                'price' => 4.99,
                'features' => ['2 GB almacenamiento', 'Soporte básico', 'Subir música e imágenes']
            ],
            'pro' => [
                'name' => 'Pro',
                'storage_gb' => 5,
                'price' => 9.99,
                'features' => ['5 GB almacenamiento', 'Soporte prioritario', 'Análisis básicos', 'Sin publicidad']
            ],
            'premium' => [
                'name' => 'Premium',
                'storage_gb' => 10,
                'price' => 14.99,
                'features' => ['10 GB almacenamiento', 'Soporte 24/7', 'Análisis completos', 'Sin publicidad', 'Funciones avanzadas']
            ]
        ];
    }
}
