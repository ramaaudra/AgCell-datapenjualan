<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanPenjualanResource\Pages;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\ProdukPenjualan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanPenjualanResource extends Resource
{
    protected static ?string $model = Penjualan::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'Laporan Penjualan';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'laporan-penjualan';

    public static function getNavigationGroup(): ?string
    {
        return 'Laporan';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Form fields are not needed for a report resource
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Penjualan::query()
            )
            ->defaultSort('tanggal', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pembeli')
                    ->label('Pembeli')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Total (Rp)')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('orderProducts.count')
                    ->label('Jumlah Item')
                    ->counts('orderProducts')
                    ->sortable(),
                Tables\Columns\TextColumn::make('produk_terjual')
                    ->label('Produk Terjual')
                    ->getStateUsing(function (Penjualan $record) {
                        $products = $record->orderProducts()->with('produk')->get()
                            ->map(function ($item) {
                                return $item->produk->nama_produk . ' (' . $item->quantity . ')';
                            })->join(', ');

                        return $products ?: '-';
                    }),
            ])
            ->filters([
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    }),
                Tables\Filters\SelectFilter::make('kategori')
                    ->label('Kategori Produk')
                    ->options(Kategori::pluck('nama', 'id'))
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['value'])) {
                            return $query->whereHas('orderProducts.produk', function ($query) use ($data) {
                                $query->where('kategori_id', $data['value']);
                            });
                        }

                        return $query;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('detail')
                    ->label('Detail')
                    ->url(fn(Penjualan $record): string => route('filament.admin.resources.penjualans.edit', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
            ])
            ->bulkActions([
                // No bulk actions needed for reports
            ])
            ->emptyStateHeading('Belum ada data penjualan')
            ->emptyStateDescription('Data penjualan akan muncul di sini setelah Anda membuat penjualan baru.')
            ->emptyStateIcon('heroicon-o-document-chart-bar');
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
            'index' => Pages\ListLaporanPenjualan::route('/'),
        ];
    }
}
