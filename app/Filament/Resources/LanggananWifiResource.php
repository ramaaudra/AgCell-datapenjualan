<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LanggananWifiResource\Pages;
use App\Filament\Resources\LanggananWifiResource\RelationManagers;
use App\Models\LanggananWifi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LanggananWifiResource extends Resource
{
    protected static ?string $model = LanggananWifi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    //navigation label
    protected static ?string $navigationLabel = 'Langganan Wifi';

    //navigation group
    protected static ?string $navigationGroup = 'Wifi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_pelanggan')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Masukkan nama lengkap pelanggan'),
                Forms\Components\TextInput::make('alamat')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Masukkan alamat lengkap pelanggan'),
                Forms\Components\TextInput::make('no_telepon')
                    ->tel()
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: 08123456789'),
                Forms\Components\DatePicker::make('tanggal_mulai')
                    ->required()
                    ->placeholder('Pilih tanggal mulai langganan'),
                Forms\Components\DatePicker::make('tanggal_berakhir')
                    ->required()
                    ->placeholder('Pilih tanggal berakhir langganan'),
                Forms\Components\TextInput::make('biaya_bulanan')
                    ->required()
                    ->numeric()
                    ->placeholder('Masukkan biaya bulanan dalam Rupiah'),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                    ])
                    ->placeholder('Pilih status langganan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_pelanggan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_telepon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_berakhir')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('biaya_bulanan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sisa_hari')
                    ->badge()
                    ->label('Sisa Waktu')
                    ->getStateUsing(function ($record) {
                        $today = now();
                        $end = \Carbon\Carbon::parse($record->tanggal_berakhir);
                        $days = intval($today->diffInDays($end, false));
                        return $days < 0 ? 'Sudah Berakhir' : $days . ' Hari';
                    })
                    ->color(function ($record) {
                        $today = now();
                        $end = \Carbon\Carbon::parse($record->tanggal_berakhir);
                        $days = intval($today->diffInDays($end, false));
                        return match (true) {
                            $days < 0 => 'danger',
                            $days <= 7 => 'warning',
                            $days <= 30 => 'info',
                            default => 'success',
                        };
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->icon(fn(string $state): string => match ($state) {
                        'aktif' => 'heroicon-o-check-circle',
                        'nonaktif' => 'heroicon-o-x-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'aktif' => 'success',
                        'nonaktif' => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => strtoupper($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListLanggananWifis::route('/'),
            'create' => Pages\CreateLanggananWifi::route('/create'),
            'edit' => Pages\EditLanggananWifi::route('/{record}/edit'),
        ];
    }
}
