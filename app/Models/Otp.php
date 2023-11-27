<?php

namespace App\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'otp',
        'expire_at',
        'valid',
    ];

    protected $casts = [
        'expire_at' => 'datetime:Y-m-d H:i:s',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function scopeLatest(Builder $query) : void
    {
        $query->orderBy('expire_at', 'desc');
    }

    public function scopeValid(Builder $query) : void
    {
        $query->where('valid', true);
    }

    public function scopeInvalid(Builder $query) : void
    {
        $now = Carbon::now();

        $query->where('expire_at', '<', $now)
            ->orWhere('valid', false);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
