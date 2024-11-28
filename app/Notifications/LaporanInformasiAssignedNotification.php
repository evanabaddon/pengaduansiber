<?php

namespace App\Notifications;

use App\Models\LaporanInformasi;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification as BaseNotification;

class LaporanInformasiAssignedNotification extends BaseNotification
{
    use Queueable;

    public function __construct(
        protected LaporanInformasi $laporanInformasi,
        protected string $assignedLevel
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $message = match($this->assignedLevel) {
            'subdit' => 'Subdit anda mendapatkan laporan baru',
            'unit' => 'Unit anda mendapatkan laporan baru', 
            'penyidik' => 'Anda telah ditugaskan sebagai Penyidik',
            default => 'Anda mendapatkan laporan baru'
        };

        return Notification::make()
            ->success()
            ->title($message)
            ->icon('heroicon-o-bell-alert')
            ->body("Pelapor: {$this->laporanInformasi->pelapors->nama}\nPerkara: {$this->laporanInformasi->perkara}")
            ->actions([
                \Filament\Notifications\Actions\Action::make('Lihat Detail')
                    ->button()
                    ->url(route('filament.admin.resources.laporan-informasis.view', ['record' => $this->laporanInformasi->id]))
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }
}
