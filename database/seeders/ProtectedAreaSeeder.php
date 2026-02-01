<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProtectedArea;

class ProtectedAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $protectedAreas = [
            ['code' => 'BPLS', 'name' => 'Batanes Protected Landscapes And Seascapes (BPLS)'],
            ['code' => 'FSNP', 'name' => 'Fuyot Spring National Park'],
            ['code' => 'QPL', 'name' => 'Quirino Protected Landscape'],
            ['code' => 'PIPLS', 'name' => 'Palaui Island Protected Landscape and Seascape'],
            ['code' => 'BWFR', 'name' => 'Baua Watershed Forest Reserve'],
            ['code' => 'WWFR', 'name' => 'Wangag Watershed Forest Reserve'],
            ['code' => 'MPL', 'name' => 'Magapit Protected Landscape'],
            ['code' => 'PPLS', 'name' => 'Peñablanca Protected Landscape and Seascape (PPLS)'],
            ['code' => 'NSMNP', 'name' => 'Northern Sierra Madre Natural Park (NSMNP)'],
            ['code' => 'TWNP', 'name' => 'Tumauini Watershed Natural Park (TWNP)'],
            ['code' => 'BHNP', 'name' => 'Bangan Hill National Park (BHNP)'],
            ['code' => 'SNM', 'name' => 'Salinas Natural Monument (SNM)'],
            ['code' => 'DWFR', 'name' => 'Dupax Watershed Forest Reserve (DWFR)'],
            ['code' => 'CPL', 'name' => 'Casecnan Protected Landscape (CPL)'],
            ['code' => 'DNP', 'name' => 'Dipaniong Natural Park (DNP) San Miguel, Echague, Isabela'],
        ];

        foreach ($protectedAreas as $area) {
            ProtectedArea::updateOrCreate(['code' => $area['code']], $area);
        }
    }
}
