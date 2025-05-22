<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\TextInput::make('schedule_latitude')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('schedule_longitude')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('schedule_start_time')
                    ->required(),
                Forms\Components\TextInput::make('schedule_end_time')
                    ->required(),
                Forms\Components\TextInput::make('latitude')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('longitude')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('start_time')
                    ->required(),
                Forms\Components\TextInput::make('end_time')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('created_at')
                ->label('Tanggal')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('user.name')
                ->label('Pegawai')
                ->sortable(),
            Tables\Columns\TextColumn::make('start_time')
                ->label('Waktu Datang'),
            Tables\Columns\TextColumn::make('end_time')
                ->label('Waktu Pulang'),
            Tables\Columns\TextColumn::make('work_duration')
                ->label('Durasi Kerja')
                ->getStateUsing(function ($record) {
                    return $record->workDuration();
                }),
            Tables\Columns\TextColumn::make('is_late')
                ->label('Status')
                ->badge()
                ->getStateUsing(function ($record) {
                    return $record->isLate() ? 'Terlambat' : 'Tepat Waktu';
                })
                ->color(fn(string $state): string => match ($state) {
                    'Tepat Waktu' => 'success',
                    'Terlambat' => 'danger',
                })
                ->description(function (Attendance $record) {
                    return 'Durasi: ' . $record->workDuration();
                }),
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
