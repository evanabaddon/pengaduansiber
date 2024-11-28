<?php

namespace App\Filament\Resources;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class UserResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'publish'
        ];
    }
    
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    // sort navigation
    protected static ?int $navigationSort = 9;

    // navigation label
    public static function getNavigationLabel(): string
    {
        return 'User';
    }

    // navigation group
    public static function getNavigationGroup(): ?string
    {
        return 'Setting';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                TextInput::make('email')
                    ->email(),
                TextInput::make('password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->revealable(),
                    
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Forms\Components\Select::make('subdit_id')
                    ->relationship('subdit', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('unit_id')
                    ->relationship('unit', 'name')
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                // role
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge(),
                // subdit
                TextColumn::make('subdit.name')
                    ->label('Subdit')
                    ->badge(),
                // unit
                TextColumn::make('unit.name')
                    ->label('Unit')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
