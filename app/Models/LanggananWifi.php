<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LanggananWifi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_pelanggan',
        'alamat',
        'no_telepon',
        'tanggal_mulai',
        'tanggal_berakhir',
        'biaya_bulanan',
        'status'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_berakhir' => 'date',
        'biaya_bulanan' => 'decimal:2'
    ];
}
