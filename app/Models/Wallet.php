<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
     use HasFactory;

    protected $fillable = [
        'user_id',
        'wallet_number',
        'balance',
        'currency',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Scope: lọc ví active
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Model event: tự sinh wallet_number 12 chữ số trước khi tạo
    protected static function booted()
    {
        static::creating(function ($wallet) {
            if (!$wallet->wallet_number) {
                do {
                    $number = '';
                    for ($i = 0; $i < 12; $i++) {
                        $number .= mt_rand(0, 9);
                    }
                } while (self::where('wallet_number', $number)->exists());

                $wallet->wallet_number = $number;
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
