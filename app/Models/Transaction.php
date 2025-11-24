<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'wallet_id',
        'type',
        'amount',
        'status',
        'description',
        'sender_wallet_id',
        'receiver_wallet_id',
        'created_by',
        'completed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    const TYPE_DEPOSIT = 'deposit';
    const TYPE_WITHDRAW = 'withdraw';
    const TYPE_TRANSFER = 'transfer';

    const STATUS_PENDING  = 'pending';
    const STATUS_SUCCESS  = 'success';
    const STATUS_FAILED   = 'failed';

    // Model event: tự sinh UUID khi tạo
    protected static function booted()
    {
        static::creating(function ($transaction) {
            if (!$transaction->transaction_code) {
                $transaction->transaction_code = (string) Str::uuid();
            }
        });
    }

    // Scope: lọc transaction completed
    public function scopeActive($query)
    {
        return $query->where('status', 'completed');
    }

    // Relationships
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function senderWallet()
    {
        return $this->belongsTo(Wallet::class, 'sender_wallet_id');
    }

    public function receiverWallet()
    {
        return $this->belongsTo(Wallet::class, 'receiver_wallet_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
