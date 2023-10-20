<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $id, $body, $custom_subject, $sender;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $custom_subject, string $body, User $sender)
    {
        $this->custom_subject = $custom_subject;
        $this->body = $body;
        $this->sender = $sender;
        $this->id = uuid_create(1);
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            from: new Address($this->sender->email, $this->sender->name),
            replyTo: [
                new Address($this->sender->email, $this->sender->name),
            ],
            subject: $this->custom_subject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.user',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
