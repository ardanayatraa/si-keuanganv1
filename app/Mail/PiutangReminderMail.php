<?php

namespace App\Mail;

use App\Models\Piutang;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PiutangReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public Piutang $piutang;

    /**
     * Create a new message instance.
     */
    public function __construct(Piutang $piutang)
    {
        $this->piutang = $piutang;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🔔 Pengingat Piutang Jatuh Tempo",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reminder.piutang',
            with: [
                'nama'     => $this->piutang->pengguna->username,
                'jumlah'   => $this->piutang->jumlah,
                'due_date' => $this->piutang->tanggal_jatuh_tempo->format('d M Y'),
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
