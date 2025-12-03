<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'set_number',
        'reps',
        'weight',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
    ];

    public $timestamps = false;

    public function session(): BelongsTo
    {
        return $this->belongsTo(WorkoutSession::class, 'session_id');
    }
}

