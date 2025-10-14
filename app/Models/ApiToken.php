<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Resources\UuidTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiToken extends Model
{
    use UuidTrait;
    
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'token',
        'user_id',
        'expires_at',
        'last_used_at',
    ];

    protected $casts = [
        'expires_at' => 'date' 
    ];

    // protected $appends = ["isexpired"];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function is_expired() {
        return $this->expires_at->isPast();
    }
    

}
