<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HelpfulVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'review_id',
    ];

    /**
     * Get the user who voted.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the review that was voted on.
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }
}
