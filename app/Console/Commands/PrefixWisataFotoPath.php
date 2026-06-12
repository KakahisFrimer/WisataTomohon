<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PrefixWisataFotoPath extends Command
{
    protected $signature = 'app:prefix-foto-wisata';
    protected $description = 'Tambahkan prefix wisata/ pada semua foto__tmpt di tbl__wisata';

    public function handle()
    {
        $places = DB::table('tbl__wisata')->get();
        $updatedCount = 0;

        foreach ($places as $place) {
            if (empty($place->foto__tmpt) || $place->foto__tmpt === '[]') {
                continue;
            }

            $photos = json_decode($place->foto__tmpt, true);
            $newPhotos = [];

            foreach ($photos as $photo) {
                // Tambahkan "wisata/" di depan jika belum ada
                if (!str_starts_with($photo, 'wisata/')) {
                    $photo = 'wisata/' . ltrim($photo, '/');
                }
                $newPhotos[] = $photo;
            }

            DB::table('tbl__wisata')
                ->where('id__tmpt', $place->id__tmpt)
                ->update(['foto__tmpt' => json_encode($newPhotos)]);

            $updatedCount++;
        }

        $this->info("✅ Selesai! {$updatedCount} record berhasil diupdate dengan prefix wisata/.");
    }
}
