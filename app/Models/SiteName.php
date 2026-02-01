<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteName extends Model
{
    protected $fillable = ['name', 'protected_area_id', 'station_code'];

    /**
     * Get the protected area that owns the site name
     */
    public function protectedArea()
    {
        return $this->belongsTo(ProtectedArea::class);
    }

    /**
     * Get the station code for this site (from database or fallback to hardcoded mapping)
     */
    public function getStationCodeAttribute()
    {
        // First try to get from database
        if ($this->attributes['station_code'] ?? null) {
            return $this->attributes['station_code'];
        }

        // Fallback to hardcoded mappings for existing sites
        $mappings = [
            'PPLS Site 1 – Toyota Project, Cabasan, Peñablanca, Cagayan' => 'TOYOTA-S1',
            'PPLS Site 2 – Sitio Spring, San Roque, Peñablanca, Cagayan' => 'SANROQUE-S1',
            'PPLS Site 3 – Sitio Danna, Manga, Peñablanca, Cagayan' => 'MANGA-S1',
            'PPLS Site 4 – Sitio Abukay, Quibal, Peñablanca, Cagayan' => 'QUIBAL-S1',
            'MPL SITE 1 – San Mariano, Lal-lo, Cagayan' => 'R2-MPL-BMS-T - S1',
            'MPL SITE 2 – Sitio Madupapa, Sta. Ana, Gattaran, Cagayan' => 'R2-MPL-BMS-T - S2',
        ];

        return $mappings[$this->name] ?? null;
    }

    /**
     * Get site name by station code
     */
    public static function findByStationCode($stationCode)
    {
        $mappings = [
            'TOYOTA-S1' => 'PPLS Site 1 – Toyota Project, Cabasan, Peñablanca, Cagayan',
            'SANROQUE-S1' => 'PPLS Site 2 – Sitio Spring, San Roque, Peñablanca, Cagayan',
            'MANGA-S1' => 'PPLS Site 3 – Sitio Danna, Manga, Peñablanca, Cagayan',
            'QUIBAL-S1' => 'PPLS Site 4 – Sitio Abukay, Quibal, Peñablanca, Cagayan',
            'R2-MPL-BMS-T - S1' => 'MPL SITE 1 – San Mariano, Lal-lo, Cagayan',
            'R2-MPL-BMS-T - S2' => 'MPL SITE 2 – Sitio Madupapa, Sta. Ana, Gattaran, Cagayan',
            'R2-BWFR-BMS' => 'PPLS Site 1 – Toyota Project, Cabasan, Peñablanca, Cagayan', // Baua observations
            'R2-WWFR-BMS-S1' => 'PPLS Site 1 – Toyota Project, Cabasan, Peñablanca, Cagayan', // Wangag observations
        ];

        $siteName = $mappings[$stationCode] ?? null;
        
        if ($siteName) {
            return static::where('name', $siteName)->first();
        }

        return null;
    }
}
