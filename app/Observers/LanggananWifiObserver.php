<?php

namespace App\Observers;

use App\Models\LanggananWifi;
use Carbon\Carbon;

class LanggananWifiObserver
{
    /**
     * Handle the LanggananWifi "saving" event.
     */
    public function saving(LanggananWifi $langgananWifi): void
    {
        // Jika tanggal berakhir sudah lewat, ubah status menjadi nonaktif
        if ($langgananWifi->tanggal_berakhir && Carbon::parse($langgananWifi->tanggal_berakhir)->isPast()) {
            $langgananWifi->status = 'nonaktif';
        }
    }

    /**
     * Handle the LanggananWifi "retrieved" event.
     */
    public function retrieved(LanggananWifi $langgananWifi): void
    {
        // Jika tanggal berakhir sudah lewat dan status masih aktif, ubah status menjadi nonaktif
        if (
            $langgananWifi->tanggal_berakhir &&
            Carbon::parse($langgananWifi->tanggal_berakhir)->isPast() &&
            $langgananWifi->status === 'aktif'
        ) {
            $langgananWifi->status = 'nonaktif';
            $langgananWifi->save();
        }
    }
}
