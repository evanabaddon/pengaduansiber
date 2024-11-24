<?php

namespace App\Notifications;

use App\Models\Laporan;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification as BaseNotification;

class LaporanAssignedNotification extends BaseNotification
{
    use Queueable;

    public function __construct(
        protected Laporan $laporan,
        protected string $assignedRole
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        // Cek apakah user yang menerima notifikasi sesuai dengan role yang ditugaskan
        if (
            ($this->assignedRole === 'subdit' && !$notifiable->hasRole('subdit')) ||
            ($this->assignedRole === 'unit' && !$notifiable->hasRole('unit')) ||
            ($this->assignedRole === 'penyidik' && !$notifiable->hasRole('penyidik'))
        ) {
            return [];
        }

        $message = match($this->assignedRole) {
            'subdit' => 'Subdit anda telah mendapatkan laporan baru',
            'unit' => 'Unit anda telah mendapatkan laporan baru', 
            'penyidik' => 'Anda telah ditugaskan sebagai Penyidik',
            default => 'Anda mendapatkan laporan baru'
        };

        return Notification::make()
            ->success()
            ->title($message)
            ->icon('heroicon-o-bell-alert')
            ->body("Pelapor: {$this->laporan->pelapors->nama}\nPerkara: {$this->laporan->perkara}")
            ->actions([
                \Filament\Notifications\Actions\Action::make('Lihat Detail')
                    ->button()
                    ->url(route('filament.admin.resources.laporans.view', ['record' => $this->laporan->id]))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
