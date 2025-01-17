<?php

namespace App\Observers;

use App\Models\ProdukPenjualan;

class ProdukPenjualanObserver
{
    /**
     * Handle the ProdukPenjualan "created" event.
     *
     * @param  \App\Models\ProdukPenjualan  $produkPenjualan
     * @return void
     */
    public function created(ProdukPenjualan $produkPenjualan)
    {
        $produk = $produkPenjualan->produk;
        if ($produk) {
            $produk->qty_stok -= $produkPenjualan->quantity;
            $produk->save();
        }
    }

    /**
     * Handle the ProdukPenjualan "deleted" event.
     *
     * @param  \App\Models\ProdukPenjualan  $produkPenjualan
     * @return void
     */
    public function deleted(ProdukPenjualan $produkPenjualan)
    {
        $produk = $produkPenjualan->produk;
        if ($produk) {
            $produk->qty_stok += $produkPenjualan->quantity;
            $produk->save();
        }
    }

    /**
     * Handle the ProdukPenjualan "forceDeleted" event.
     *
     * @param  \App\Models\ProdukPenjualan  $produkPenjualan
     * @return void
     */
    

    /**
     * Handle the ProdukPenjualan "updated" event.
     *
     * @param  \App\Models\ProdukPenjualan  $produkPenjualan
     * @return void
     */
    public function updated(ProdukPenjualan $produkPenjualan)
    {
        $produk = $produkPenjualan->produk;
        if ($produk) {
            $originalQuantity = $produkPenjualan->getOriginal('quantity');
            $newQuantity = $produkPenjualan->quantity;

            // Update stock based on the difference between original and new quantity
            $produk->qty_stok += $originalQuantity - $newQuantity;
            $produk->save();
        }
    }
}
