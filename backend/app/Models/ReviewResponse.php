<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'user_id',
        'response',
    ];

    /**
     * Get the review this response belongs to.
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    /**
     * Get the user (restaurant owner) who wrote the response.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
