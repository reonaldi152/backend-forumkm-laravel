<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('username')
                    ->label('Username')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                
                Forms\Components\Select::make('roles')
                    ->label('Roles')
                    ->options([
                        'user' => 'user',
                        'admin' => 'admin',
                    ])
                    ->required(),
                Forms\Components\Select::make('gender')
                    ->label('Gender')
                    ->options([
                        'Laki-Laki' => 'Laki-Laki',
                        'Perempuan' => 'Perempuan',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->label('Telepon')
                    ->tel()
                    ->maxLength(20)
                    ->nullable(),
                Forms\Components\DatePicker::make('birth_date')->label('Birth Date')->required(),
                Forms\Components\TextInput::make('otp_register')
                    ->label('OTP Register')
                    ->maxLength(6)
                    ->nullable(),
                Forms\Components\FileUpload::make('photo')->label('User Photo'),
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(fn($context) => $context === 'create')
                    ->minLength(8)
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->hiddenOn('edit')
                    ->revealable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('NAMA')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('EMAIL')->searchable(),
                Tables\Columns\TextColumn::make('username')->label('USERNAME')->searchable(),
                Tables\Columns\TextColumn::make('phonr')->label('PHONE')->searchable(),
                Tables\Columns\TextColumn::make('roles')->label('ROLES'),
                Tables\Columns\TextColumn::make('otp_register')->label('OTP REGISTER'),
                Tables\Columns\ImageColumn::make('photo')->label('PHOTO'),
                Tables\Columns\TextColumn::make('gender')->label('GENDER'),
                Tables\Columns\TextColumn::make('birth_date')->label('BIRTH DATE'),
                Tables\Columns\TextColumn::make('email_verified_at')->label('EMAIL VERIFIED'),
                Tables\Columns\TextColumn::make('created_at')->label('CREATED AT'),
                Tables\Columns\TextColumn::make('updated_at')->label('UPDATED AT'),


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
