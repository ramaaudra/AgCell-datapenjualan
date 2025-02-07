<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_hps', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelanggan');
            $table->string('no_telepon');
            $table->string('merk_hp');
            $table->string('model_hp');
            $table->text('jenis_kerusakan');
            $table->text('keterangan')->nullable();
            $table->decimal('biaya_service', 10, 2)->nullable();
            $table->enum('status', ['menunggu', 'proses', 'selesai', 'diambil']);
            $table->date('tanggal_masuk');
            $table->date('tanggal_selesai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_hps');
    }
};
