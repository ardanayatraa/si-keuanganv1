<?php

namespace App\Mail;

use App\Models\Utang;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UtangReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public Utang $utang;
      public $label;

    /**
     * Create a new message instance.
     */
    public function __construct(Utang $utang, $label)
{
    $this->utang = $utang;
    $this->label = $label;
}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🔔 Pengingat Utang Jatuh Tempo",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reminder.utang',
            with: [
               'nama'      => $this->utang->pengguna->username,
            'jumlah'    => $this->utang->jumlah,
            'due_date'  => $this->utang->tanggal_jatuh_tempo->format('d-m-Y'),
            'deskripsi' => $this->utang->deskripsi,
            'label'     => $this->label,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int,\Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
