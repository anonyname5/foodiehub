<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Restaurant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'cuisine',
        'description',
        'address',
        'phone',
        'hours',
        'price_range',
        'location',
        'latitude',
        'longitude',
        'main_image',
        'images',
        'features',
        'average_rating',
        'review_count',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hours' => 'array',
        'images' => 'array',
        'features' => 'array',
        'average_rating' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    /**
     * Get the reviews for the restaurant.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the users who favorited this restaurant.
     */
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')
                    ->withTimestamps();
    }

    /**
     * Get the images for the restaurant.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->ordered();
    }

    /**
     * Get the primary image for the restaurant.
     */
    public function primaryImage(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->primary();
    }

    /**
     * Scope a query to only include active restaurants.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by cuisine.
     */
    public function scopeByCuisine($query, $cuisine)
    {
        return $query->where('cuisine', $cuisine);
    }

    /**
     * Scope a query to filter by price range.
     */
    public function scopeByPriceRange($query, $priceRange)
    {
        return $query->where('price_range', $priceRange);
    }

    /**
     * Scope a query to filter by location.
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    /**
     * Scope a query to filter by minimum rating.
     */
    public function scopeByMinRating($query, $rating)
    {
        return $query->where('average_rating', '>=', $rating);
    }

    /**
     * Scope a query to search restaurants.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('cuisine', 'like', "%{$search}%")
              ->orWhere('location', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Update the restaurant's average rating and review count.
     */
    public function updateRatingStats()
    {
        $this->average_rating = $this->reviews()->avg('overall_rating') ?? 0.00;
        $this->review_count = $this->reviews()->count();
        $this->save();
    }

    /**
     * Get the rating breakdown for the restaurant.
     */
    public function getRatingBreakdownAttribute(): array
    {
        $breakdown = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = $this->reviews()->where('overall_rating', $i)->count();
            $breakdown[$i] = $count;
        }
        return $breakdown;
    }
}
