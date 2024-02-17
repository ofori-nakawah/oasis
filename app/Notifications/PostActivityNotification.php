<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostActivityNotification extends Notification
{
    use Queueable;

    public $post;
    public $event;
    public $message;
    public $ref_id;
    public $category;
    public $status;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($post, $event, $message, $ref_id, $category, $status)
    {
        $this->post = $post;
        $this->event = $event;
        $this->message = $message;
        $this->ref_id = $ref_id;
        $this->category = $category;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

//    /**
//     * Get the mail representation of the notification.
//     *
//     * @param  mixed  $notifiable
//     * @return \Illuminate\Notifications\Messages\MailMessage
//     */
//    public function toMail($notifiable)
//    {
//        return (new MailMessage)
//                    ->line('The introduction to the notification.')
//                    ->action('Notification Action', url('/'))
//                    ->line('Thank you for using our application!');
//    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'post' => $this->post,
            'event' => $this->event,
            'ref_id' => $this->ref_id,
            'category' => $this->category,
            'status' => $this->status,
            'message' => $this->message
        ];
    }
}
