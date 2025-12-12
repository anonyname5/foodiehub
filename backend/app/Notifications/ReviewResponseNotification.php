<?php

namespace App\Notifications;

use App\Models\Review;
use App\Models\ReviewResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewResponseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $review;
    public $response;

    /**
     * Create a new notification instance.
     */
    public function __construct(Review $review, ReviewResponse $response)
    {
        $this->review = $review;
        $this->response = $response;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        // Add email if user has email notifications enabled
        if ($notifiable->email_notifications ?? true) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $restaurant = $this->review->restaurant;
        $owner = $this->response->user;
        
        return (new MailMessage)
                    ->subject("Restaurant Owner Responded to Your Review")
                    ->greeting("Hello {$notifiable->name},")
                    ->line("The owner of {$restaurant->name} has responded to your review.")
                    ->line("Response: \"{$this->response->response}\"")
                    ->action('View Response', route('restaurants.show', $restaurant->id) . '#review-' . $this->review->id)
                    ->line('Thank you for using FoodieHub!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'review_response',
            'review_id' => $this->review->id,
            'restaurant_id' => $this->review->restaurant_id,
            'restaurant_name' => $this->review->restaurant->name,
            'owner_name' => $this->response->user->name,
            'message' => "The owner of {$this->review->restaurant->name} responded to your review",
            'url' => route('restaurants.show', $this->review->restaurant_id) . '#review-' . $this->review->id,
        ];
    }
}
