<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Letter;

class LetterStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $letter;
    public $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(Letter $letter, string $status)
    {
        $this->letter = $letter;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->getSubject();
        $greeting = "Halo {$notifiable->name},";

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($this->getMainMessage());

        if ($this->status === 'approved') {
            $mailMessage->action('Download Surat', route('letters.show', $this->letter));
        } else {
            $mailMessage->action('Lihat Detail', route('letters.show', $this->letter));
        }

        return $mailMessage->line('Terima kasih telah menggunakan layanan RT 02.');
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'letter_id' => $this->letter->id,
            'letter_type' => $this->letter->type,
            'letter_number' => $this->letter->number,
            'status' => $this->status,
            'title' => $this->getSubject(),
            'message' => $this->getMainMessage(),
            'action_url' => route('letters.show', $this->letter)
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    private function getSubject(): string
    {
        $letterType = ucfirst($this->letter->type);

        switch ($this->status) {
            case 'approved':
                return "Surat {$letterType} Anda Telah Disetujui";
            case 'rejected':
                return "Surat {$letterType} Anda Ditolak";
            default:
                return "Update Status Surat {$letterType}";
        }
    }

    private function getMainMessage(): string
    {
        $letterType = ucfirst($this->letter->type);

        switch ($this->status) {
            case 'approved':
                return "Pengajuan surat {$letterType} Anda dengan keperluan '{$this->letter->purpose}' telah disetujui oleh admin RT. Anda dapat mendownload surat resmi melalui tombol di bawah ini.";
            case 'rejected':
                return "Pengajuan surat {$letterType} Anda dengan keperluan '{$this->letter->purpose}' telah ditolak oleh admin RT. Silakan hubungi admin untuk informasi lebih lanjut.";
            default:
                return "Status surat {$letterType} Anda telah diperbarui menjadi: {$this->status}.";
        }
    }
}
