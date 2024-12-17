<?php

namespace App\Notifications;

use App\Models\Pengaduan;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification as BaseNotification;

class PengaduanAssignedNotification extends BaseNotification
{
    use Queueable;

    public function __construct(protected Pengaduan $pengaduan, protected string $assignedLevel) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return Notification::make()
            ->success()
            ->title('Pengaduan baru telah ditugaskan kepada Anda')
            ->icon('heroicon-o-bell-alert')
            ->body("Pelapor: {$this->pengaduan->pelapors->nama}\nPerkara: {$this->pengaduan->perkara}")
            ->actions([
                \Filament\Notifications\Actions\Action::make('Lihat Detail')
                    ->button()
                    ->url(route('filament.admin.resources.pengaduans.view', ['record' => $this->pengaduan->id]))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
