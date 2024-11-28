<?php

namespace App\Filament\Pages\Profile;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class EditProfile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static string $view = 'filament.pages.profile.edit-profile';
    protected static ?string $title = 'Edit Profil';
    protected static bool $shouldRegisterNavigation = false;
    
    public $data;

    public function mount(): void
    {
        $this->data = [
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            // 'password' => auth()->user()->plain_password ?? ''
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema($this->getFormFields())
            ->statePath('data')
            ->columns(1);
    }

    protected function getFormFields(): array
    {
        $fields = [];
        
        // Field name untuk semua user
        $fields[] = TextInput::make('name')
            ->required()
            ->maxLength(255)
            ->disabled(!auth()->user()->hasRole('super_admin'))
            ->columnSpan('2');
        
        // Field email untuk semua user
        $fields[] = TextInput::make('email')
            ->email()
            ->required()
            ->maxLength(255)
            ->disabled(!auth()->user()->hasRole('super_admin'))
            ->unique(
                table: 'users', 
                column: 'email',
                ignorable: auth()->user()
            )
            ->columnSpan('2');
        
        // Field password untuk semua user
        $fields[] = TextInput::make('password')
            ->type('password')
            ->password()
            ->label('Password')
            ->revealable()
            ->columnSpan('2');
            
        // Field konfirmasi password
        $fields[] = TextInput::make('password_confirmation')
            ->type('password')
            ->password()
            ->label('Konfirmasi Password')
            ->revealable()
            ->same('password')
            ->columnSpan('2');
            
        return $fields;
    }

    public function save()
    {
        $state = $this->data;
        
        $user = auth()->user();

        // Validasi password dan konfirmasi harus sama
        if (!empty($state['password'])) {
            if ($state['password'] !== $state['password_confirmation']) {
                Notification::make()
                    ->title('Password dan konfirmasi password tidak sama')
                    ->danger()
                    ->send();
                return;
            }
            
            $user->password = Hash::make($state['password']);
        }
        
        if ($user->hasRole('super_admin')) {
            $user->name = $state['name'];
            $user->email = $state['email'];
        }
        
        $user->save();
        
        Notification::make()
            ->title('Profil berhasil diperbarui')
            ->success()
            ->send();
    }
} 