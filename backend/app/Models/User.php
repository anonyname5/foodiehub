<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'bio',
        'avatar',
        'is_public',
        'email_notifications',
        'is_admin',
        'role',
        'is_active',
        'last_login_at',
        'banned_at',
        'location',
        'restaurant_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_public' => 'boolean',
        'email_notifications' => 'boolean',
        'is_admin' => 'boolean',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'banned_at' => 'datetime',
    ];

    /**
     * Get the reviews written by the user.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the restaurants favorited by the user.
     */
    public function favoriteRestaurants(): BelongsToMany
    {
        return $this->belongsToMany(Restaurant::class, 'favorites')
                    ->withTimestamps();
    }

    /**
     * Get the user's average rating given.
     */
    public function getAverageRatingAttribute(): float
    {
        return $this->reviews()->avg('overall_rating') ?? 0.0;
    }

    /**
     * Get the user's total reviews count.
     */
    public function getTotalReviewsAttribute(): int
    {
        return $this->reviews()->count();
    }

    /**
     * Get the user's favorite cuisines.
     */
    public function getFavoriteCuisinesAttribute(): array
    {
        $cuisines = $this->reviews()
            ->join('restaurants', 'reviews.restaurant_id', '=', 'restaurants.id')
            ->selectRaw('restaurants.cuisine, COUNT(*) as count')
            ->groupBy('restaurants.cuisine')
            ->orderByDesc('count')
            ->limit(5)
            ->pluck('restaurants.cuisine')
            ->toArray();

        return $cuisines;
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->is_admin || $this->role === 'admin' || $this->role === 'super_admin';
    }

    /**
     * Check if user is a super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is a restaurant owner
     */
    public function isRestaurantOwner(): bool
    {
        return $this->restaurant_id !== null || $this->role === 'restaurant_owner';
    }

    /**
     * Get the restaurant owned by this user
     */
    public function ownedRestaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    /**
     * Check if user account is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(): void
    {
        $this->last_login_at = now();
        $this->save();
    }

    /**
     * Scope to get only admin users
     */
    public function scopeAdmins($query)
    {
        return $query->where(function($q) {
            $q->where('is_admin', true)
              ->orWhere('role', 'admin')
              ->orWhere('role', 'super_admin');
        });
    }

    /**
     * Scope to get only active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get users by role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }
}
