<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeightTracking extends Model
{
    use HasFactory;

    protected $table = 'weight_tracking';

    protected $fillable = [
        'user_id',
        'weight',
        'weight_date',
        'notes',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'weight_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

