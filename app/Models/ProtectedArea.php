<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Import all observation models
use App\Models\BanganObservation;
use App\Models\BauaObservation;
use App\Models\BmsSpeciesObservation;
use App\Models\CasecnanObservation;
use App\Models\DipaniongObservation;
use App\Models\DupaxObservation;
use App\Models\FuyotObservation;
use App\Models\MagapitObservation;
use App\Models\MadreObservation;
use App\Models\MadupapaObservation;
use App\Models\MangaObservation;
use App\Models\MarianoObservation;
use App\Models\PalauiObservation;
use App\Models\QuirinoObservation;
use App\Models\SalinasObservation;
use App\Models\SanRoqueObservation;
use App\Models\ToyotaObservation;
use App\Models\TumauiniObservation;
use App\Models\WangagObservation;

class ProtectedArea extends Model
{
    protected $fillable = [
        'code',
        'name',
    ];

    public function speciesObservations()
    {
        return $this->hasMany(BmsSpeciesObservation::class);
    }

    // Get all observations across all tables for this protected area
    public function getAllSpeciesObservations()
    {
        $observationModels = [
            'BPLS' => BmsSpeciesObservation::class,
            'BWFR' => BauaObservation::class,
            'FSNP' => FuyotObservation::class,
            'MPL' => [MagapitObservation::class, MarianoObservation::class, MadupapaObservation::class],
            'PIPLS' => PalauiObservation::class,
            'QPL' => QuirinoObservation::class,
            'WWFR' => WangagObservation::class,
            'TOYOTA' => ToyotaObservation::class,
            'SANROQUE' => SanRoqueObservation::class,
            'MANGA' => MangaObservation::class,
            'QUIBAL' => QuibalObservation::class,
            'NSMNP' => MadreObservation::class,
            'TWNP' => TumauiniObservation::class,
            'BHNP' => BanganObservation::class,
            'SNM' => SalinasObservation::class,
            'DWFR' => DupaxObservation::class,
            'CPL' => CasecnanObservation::class,
            'DNP' => DipaniongObservation::class,
            'PPLS' => [ToyotaObservation::class, SanRoqueObservation::class, MangaObservation::class, QuibalObservation::class],
        ];

        $observations = collect();
        
        foreach ($observationModels as $areaCode => $modelClass) {
            if ($this->code === $areaCode) {
                if (is_array($modelClass)) {
                    // Handle multiple models for PPLS
                    foreach ($modelClass as $singleModel) {
                        try {
                            $observations = $observations->merge($singleModel::get());
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                } else {
                    try {
                        $observations = $observations->merge($modelClass::get());
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        }

        return $observations;
    }

    // Get total observation count across all tables including site-specific tables
    public function getTotalObservationsCount()
    {
        // First try the hardcoded mappings (for existing areas)
        $observationModels = [
            'BPLS' => BmsSpeciesObservation::class,
            'BWFR' => BauaObservation::class,
            'FSNP' => FuyotObservation::class,
            'MPL' => [MagapitObservation::class, MarianoObservation::class, MadupapaObservation::class],
            'PIPLS' => PalauiObservation::class,
            'QPL' => QuirinoObservation::class,
            'WWFR' => WangagObservation::class,
            'TOYOTA' => ToyotaObservation::class,
            'SANROQUE' => SanRoqueObservation::class,
            'MANGA' => MangaObservation::class,
            'QUIBAL' => QuibalObservation::class,
            'NSMNP' => MadreObservation::class,
            'TWNP' => TumauiniObservation::class,
            'BHNP' => BanganObservation::class,
            'SNM' => SalinasObservation::class,
            'DWFR' => DupaxObservation::class,
            'CPL' => CasecnanObservation::class,
            'DNP' => DipaniongObservation::class,
            'PPLS' => [ToyotaObservation::class, SanRoqueObservation::class, MangaObservation::class, QuibalObservation::class],
        ];

        $totalCount = 0;
        
        // Check hardcoded mappings first
        foreach ($observationModels as $areaCode => $modelClass) {
            if ($this->code === $areaCode) {
                if (is_array($modelClass)) {
                    // Handle multiple models for PPLS
                    foreach ($modelClass as $singleModel) {
                        try {
                            $totalCount += $singleModel::count();
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                } else {
                    try {
                        $totalCount += $modelClass::count();
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        }
        
        // If no hardcoded mapping found, try dynamic table approach
        if ($totalCount === 0) {
            try {
                $tableName = strtolower($this->code) . '_tbl';
                if (Schema::hasTable($tableName)) {
                    $totalCount += DB::table($tableName)->count();
                }
            } catch (\Exception $e) {
                // Table doesn't exist or error occurred
            }
        }
        
        // IMPORTANT: Add site-specific tables count
        try {
            // Get all site-specific tables for this protected area
            $siteTables = DB::select("SHOW TABLES LIKE '%_site_tbl'");
            $totalSiteCount = 0;
            
            foreach ($siteTables as $table) {
                $tableName = array_values((array)$table)[0];
                
                // Check if this site table belongs to this protected area
                try {
                    $count = DB::table($tableName)
                        ->where('protected_area_id', $this->id)
                        ->count();
                    $totalSiteCount += $count;
                } catch (\Exception $e) {
                    // Table might not have the expected structure
                    continue;
                }
            }
            
            $totalCount += $totalSiteCount;
        } catch (\Exception $e) {
            // Error getting site tables
        }

        return $totalCount;
    }

    // Scope to get protected areas with their total observation counts
    public function scopeWithTotalObservationsCount($query)
    {
        return $query->select('protected_areas.*')
            ->selectRaw('0 as species_observations_count'); // Temporary placeholder
    }

    // Get the actual observation count after query
    public function getSpeciesObservationsCountAttribute()
    {
        return $this->getTotalObservationsCount();
    }
}
