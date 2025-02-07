<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Kategori;
use App\Models\Produk;

class ProdukFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Produk::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'image' => $this->faker->imageUrl(),
            'nama_produk' => $this->faker->sentence(1),
            'qty_stok' => $this->faker->numberBetween(0, 10000),
            'harga_beli' => $this->faker->numberBetween(0, 10000),
            'harga_jual_toko' => $this->faker->numberBetween(0, 10000),
            'kategori_id' => Kategori::factory(),
        ];
    }
}
