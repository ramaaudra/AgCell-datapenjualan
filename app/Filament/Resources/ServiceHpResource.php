<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceHpResource\Pages;
use App\Filament\Resources\ServiceHpResource\RelationManagers;
use App\Models\ServiceHp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceHpResource extends Resource
{
    protected static ?string $model = ServiceHp::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    //navigation label
    protected static ?string $navigationLabel = 'Service HP';

    //navigation group
    protected static ?string $navigationGroup = 'Service';

    //model label


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pelanggan')
                    ->description('Data pelanggan yang melakukan service')
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
                            ->helperText('Nomor yang bisa dihubungi untuk informasi service'),
                    ]),

                Forms\Components\Section::make('Detail Perangkat')
                    ->description('Informasi perangkat yang akan diservice')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('merk_hp')
                            ->label('Merk HP')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Samsung, iPhone, Xiaomi')
                            ->helperText('Merk/brand dari perangkat'),
                        Forms\Components\TextInput::make('model_hp')
                            ->label('Model HP')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Galaxy A52, iPhone 13')
                            ->helperText('Model spesifik dari perangkat'),
                        Forms\Components\Textarea::make('jenis_kerusakan')
                            ->label('Jenis Kerusakan')
                            ->required()
                            ->placeholder('Jelaskan jenis kerusakan HP secara detail')
                            ->helperText('Deskripsikan kerusakan dengan jelas untuk diagnosis yang tepat')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan Tambahan')
                            ->placeholder('Tambahkan informasi lain yang relevan')
                            ->helperText('Informasi tambahan seperti password, kondisi fisik, dll')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Informasi Service')
                    ->description('Detail biaya dan status service')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('biaya_service')
                            ->label('Biaya Service (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('Masukkan biaya service')
                            ->helperText('Kosongkan jika belum ditentukan'),
                        Forms\Components\Select::make('status')
                            ->label('Status Service')
                            ->required()
                            ->options([
                                'menunggu' => 'Menunggu',
                                'proses' => 'Proses',
                                'selesai' => 'Selesai',
                                'diambil' => 'Diambil'
                            ])
                            ->placeholder('Pilih status service')
                            ->helperText('Status terkini dari proses service')
                            ->default('menunggu'),
                        Forms\Components\DatePicker::make('tanggal_masuk')
                            ->label('Tanggal Masuk')
                            ->required()
                            ->placeholder('Pilih tanggal masuk service')
                            ->default(now())
                            ->helperText('Tanggal perangkat diterima untuk service'),
                        Forms\Components\DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai')
                            ->placeholder('Pilih tanggal selesai service')
                            ->helperText('Kosongkan jika belum selesai'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_pelanggan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_telepon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('merk_hp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model_hp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('biaya_service')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->icon(fn(string $state): string => match ($state) {
                        'menunggu' => 'heroicon-o-clock',
                        'proses' => 'heroicon-o-arrow-path',
                        'selesai' => 'heroicon-o-check-circle',
                        'diambil' => 'heroicon-o-truck',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'menunggu' => 'gray',
                        'proses' => 'info',
                        'selesai' => 'success',
                        'diambil' => 'warning',
                    })
                    ->formatStateUsing(fn(string $state): string => strtoupper($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_masuk')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_selesai')
                    ->date()
                    ->sortable(),
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
            'index' => Pages\ListServiceHps::route('/'),
            'create' => Pages\CreateServiceHp::route('/create'),
            'edit' => Pages\EditServiceHp::route('/{record}/edit'),
        ];
    }
}
