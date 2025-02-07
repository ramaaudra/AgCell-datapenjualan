<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceHp extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_pelanggan',
        'no_telepon',
        'merk_hp',
        'model_hp',
        'jenis_kerusakan',
        'keterangan',
        'biaya_service',
        'status',
        'tanggal_masuk',
        'tanggal_selesai'
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_selesai' => 'date',
        'biaya_service' => 'decimal:2'
    ];
}
