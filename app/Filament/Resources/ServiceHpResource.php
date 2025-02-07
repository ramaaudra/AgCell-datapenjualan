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
                Forms\Components\TextInput::make('nama_pelanggan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('no_telepon')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('merk_hp')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('model_hp')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('jenis_kerusakan')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('biaya_service')
                    ->numeric()
                    ->default(null),
                Forms\Components\Select::make('status')
                    ->required()
                ->options([
                    'menunggu' => 'Menunggu',
                    'proses' => 'Proses',
                    'selesai' => 'Selesai',
                    'diambil' => 'Diambil'
                ]),
                Forms\Components\DatePicker::make('tanggal_masuk')
                    ->required(),
                Forms\Components\DatePicker::make('tanggal_selesai'),
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
