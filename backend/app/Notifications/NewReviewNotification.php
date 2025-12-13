<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class NewReviewNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $review;

    /**
     * Create a new notification instance.
     */
    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        // Add email if user has email notifications enabled AND mail is configured
        if (($notifiable->email_notifications ?? true) && $this->isMailConfigured()) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Check if mail is properly configured
     */
    private function isMailConfigured(): bool
    {
        try {
            // Check if mail default driver is configured
            $mailDriver = config('mail.default');
            if (empty($mailDriver)) {
                return false;
            }
            
            // Check if mailer configuration exists for the driver
            $mailerConfig = config("mail.mailers.{$mailDriver}");
            if (empty($mailerConfig)) {
                return false;
            }
            
            // Check if from address is configured
            $fromAddress = config('mail.from.address');
            if (empty($fromAddress)) {
                return false;
            }
            
            return true;
        } catch (\Exception $e) {
            // If there's any error checking config, assume mail is not configured
            Log::warning('Mail configuration check failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $restaurant = $this->review->restaurant;
        $reviewer = $this->review->user;
        
        return (new MailMessage)
                    ->subject("New Review for {$restaurant->name}")
                    ->greeting("Hello {$notifiable->name},")
                    ->line("{$reviewer->name} has left a new review for your restaurant: {$restaurant->name}")
                    ->line("Rating: {$this->review->overall_rating}/5")
                    ->line("Review: \"{$this->review->title}\"")
                    ->action('View Review', route('restaurants.show', $restaurant->id) . '#review-' . $this->review->id)
                    ->line('Thank you for using FoodieHub!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_review',
            'review_id' => $this->review->id,
            'restaurant_id' => $this->review->restaurant_id,
            'restaurant_name' => $this->review->restaurant->name,
            'reviewer_name' => $this->review->user->name,
            'rating' => $this->review->overall_rating,
            'title' => $this->review->title,
            'message' => "{$this->review->user->name} left a new review for {$this->review->restaurant->name}",
            'url' => route('restaurants.show', $this->review->restaurant_id) . '#review-' . $this->review->id,
        ];
    }
}
