<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProtectedArea;

class SiteNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get protected areas
        $ppls = ProtectedArea::where('code', 'PPLS')->first();
        $mpl = ProtectedArea::where('code', 'MPL')->first();

        $siteNames = [
            [
                'name' => 'PPLS Site 1 – Toyota Project, Cabasan, Peñablanca, Cagayan',
                'protected_area_id' => $ppls ? $ppls->id : null,
            ],
            [
                'name' => 'PPLS Site 2 – Sitio Spring, San Roque, Peñablanca, Cagayan',
                'protected_area_id' => $ppls ? $ppls->id : null,
            ],
            [
                'name' => 'PPLS Site 3 – Sitio Danna, Manga, Peñablanca, Cagayan',
                'protected_area_id' => $ppls ? $ppls->id : null,
            ],
            [
                'name' => 'PPLS Site 4 – Sitio Abukay, Quibal, Peñablanca, Cagayan',
                'protected_area_id' => $ppls ? $ppls->id : null,
            ],
            [
                'name' => 'MPL SITE 1 – San Mariano, Lal-lo, Cagayan',
                'protected_area_id' => $mpl ? $mpl->id : null,
            ],
            [
                'name' => 'MPL SITE 2 – Sitio Madupapa, Sta. Ana, Gattaran, Cagayan',
                'protected_area_id' => $mpl ? $mpl->id : null,
            ],
        ];

        // Clear existing site names to avoid duplicates
        \DB::table('site_names')->delete();

        foreach ($siteNames as $site) {
            \DB::table('site_names')->insert([
                'name' => $site['name'],
                'protected_area_id' => $site['protected_area_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
