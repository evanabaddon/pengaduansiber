<?php

namespace App\Filament\Resources;
use Filament\Forms;
use App\Models\Role;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
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

    public static function shouldRegisterNavigation(): bool
    {
        $panelId = Filament::getCurrentPanel()->getId();

        return in_array($panelId, [
            'admin',
        ]);
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
                Forms\Components\Select::make('subdit_id')
                    ->relationship('subdit', 'name')
                    ->searchable()
                    ->preload()
                    ->live(),
                Forms\Components\Select::make('unit_id')
                    ->relationship('unit', 'name', fn (Builder $query, Get $get) => 
                        $query->when(
                            $get('subdit_id'),
                            fn (Builder $query, $subdit_id) => 
                                $query->where('subdit_id', $subdit_id)
                        )
                    )
                    ->searchable()
                    ->preload()
                    ->disabled(fn (Get $get): bool => ! $get('subdit_id'))
                    ->dehydrated(fn ($state) => filled($state)),
                TextInput::make('name'),
                TextInput::make('email')
                    ->email(),
                TextInput::make('password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->revealable()
                    ->default(fn ($record) => $record?->password),
                    
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('email', '!=', 'admin@admin.com'))
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),
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
            ])
            ->actions([
                // view
                Tables\Actions\ViewAction::make(),
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
