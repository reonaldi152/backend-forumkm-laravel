<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class GeneralMailer extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $subject;
    public $view;
    public $data;

    /**
     * Create a new message instance.
     *
     * @param string $subject The subject of the email
     * @param string $view The Blade view file for the email content
     * @param array $data Data to be passed to the Blade view
     */
    public function __construct(string $subject, string $view, array $data = [])
    {
        $this->subject = $subject;
        $this->view = $view;
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: $this->view,
            with: $this->data,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
