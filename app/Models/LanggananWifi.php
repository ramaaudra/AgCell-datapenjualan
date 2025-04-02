<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

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

    /**
     * Memeriksa apakah langganan sudah berakhir
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->tanggal_berakhir && Carbon::parse($this->tanggal_berakhir)->isPast();
    }

    /**
     * Memperbarui status menjadi nonaktif jika sudah berakhir
     *
     * @return bool
     */
    public function updateStatusIfExpired(): bool
    {
        if ($this->isExpired() && $this->status === 'aktif') {
            $this->status = 'nonaktif';
            return $this->save();
        }

        return false;
    }
}
