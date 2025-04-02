<?php

namespace App\Console\Commands;

use App\Models\LanggananWifi;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateLanggananWifiStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'langganan-wifi:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memperbarui status langganan WiFi menjadi nonaktif jika sudah melewati tanggal berakhir';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pembaruan status langganan WiFi...');

        // Ambil semua langganan WiFi yang masih aktif tapi sudah melewati tanggal berakhir
        $expiredSubscriptions = LanggananWifi::where('status', 'aktif')
            ->where('tanggal_berakhir', '<', Carbon::today())
            ->get();

        $count = 0;

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->status = 'nonaktif';
            $subscription->save();
            $count++;

            $this->info("Langganan WiFi untuk {$subscription->nama_pelanggan} telah dinonaktifkan.");
        }

        $this->info("Pembaruan selesai. {$count} langganan WiFi telah dinonaktifkan.");

        return Command::SUCCESS;
    }
}
