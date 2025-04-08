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
                Forms\Components\Section::make('Informasi Pelanggan')
                    ->description('Data pelanggan yang berlangganan WiFi')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('nama_pelanggan')
                            ->label('Nama Pelanggan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Masukkan nama lengkap pelanggan')
                            ->helperText('Nama lengkap pelanggan untuk identifikasi'),
                        Forms\Components\TextInput::make('no_telepon')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: 08123456789')
                            ->helperText('Nomor yang bisa dihubungi untuk informasi langganan'),
                        Forms\Components\TextInput::make('alamat')
                            ->label('Alamat Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Masukkan alamat lengkap pelanggan')
                            ->helperText('Alamat pemasangan WiFi')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Detail Langganan')
                    ->description('Informasi periode dan biaya langganan')
                    ->icon('heroicon-o-signal')
                    ->columns(2)
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->placeholder('Pilih tanggal mulai langganan')
                            ->default(now())
                            ->helperText('Tanggal awal berlangganan'),
                        Forms\Components\DatePicker::make('tanggal_berakhir')
                            ->label('Tanggal Berakhir')
                            ->required()
                            ->placeholder('Pilih tanggal berakhir langganan')
                            ->helperText('Tanggal akhir berlangganan'),
                        Forms\Components\TextInput::make('biaya_bulanan')
                            ->label('Biaya Bulanan (Rp)')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('Masukkan biaya bulanan')
                            ->helperText('Biaya yang ditagihkan setiap bulan'),
                        Forms\Components\Select::make('status')
                            ->label('Status Langganan')
                            ->required()
                            ->options([
                                'aktif' => 'Aktif',
                                'nonaktif' => 'Nonaktif',
                            ])
                            ->default('aktif')
                            ->placeholder('Pilih status langganan')
                            ->helperText('Status aktif/nonaktif langganan'),
                    ]),
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
