<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Review extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'restaurant_id',
        'overall_rating',
        'food_rating',
        'service_rating',
        'ambiance_rating',
        'value_rating',
        'title',
        'comment',
        'visit_date',
        'photos',
        'recommend',
        'helpful_count',
        'is_verified',
        'status',
        'approved_at',
        'rejected_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'overall_rating' => 'decimal:2',
        'food_rating' => 'decimal:2',
        'service_rating' => 'decimal:2',
        'ambiance_rating' => 'decimal:2',
        'value_rating' => 'decimal:2',
        'visit_date' => 'date',
        'photos' => 'array',
        'recommend' => 'boolean',
        'is_verified' => 'boolean',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Get the user that wrote the review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the restaurant that was reviewed.
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Get the images for the review.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->ordered();
    }

    /**
     * Scope a query to order by newest first.
     */
    public function scopeNewest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to order by oldest first.
     */
    public function scopeOldest($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Scope a query to order by highest rating first.
     */
    public function scopeHighestRated($query)
    {
        return $query->orderBy('overall_rating', 'desc');
    }

    /**
     * Scope a query to order by lowest rating first.
     */
    public function scopeLowestRated($query)
    {
        return $query->orderBy('overall_rating', 'asc');
    }

    /**
     * Scope a query to filter by rating.
     */
    public function scopeByRating($query, $rating)
    {
        return $query->where('overall_rating', $rating);
    }

    /**
     * Scope a query to filter by minimum rating.
     */
    public function scopeByMinRating($query, $rating)
    {
        return $query->where('overall_rating', '>=', $rating);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Update restaurant rating stats when a review is created, updated, or deleted
        static::created(function ($review) {
            $review->restaurant->updateRatingStats();
        });

        static::updated(function ($review) {
            $review->restaurant->updateRatingStats();
        });

        static::deleted(function ($review) {
            $review->restaurant->updateRatingStats();
        });
    }
}
