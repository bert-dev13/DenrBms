<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteName extends Model
{
    protected $fillable = ['name', 'protected_area_id'];

    private const STATION_CODE_TO_SITE_NAME = [
        'TOYOTA-S1' => 'PPLS Site 1 – Toyota Project, Cabasan, Peñablanca, Cagayan',
        'SANROQUE-S1' => 'PPLS Site 2 – Sitio Spring, San Roque, Peñablanca, Cagayan',
        'MANGA-S1' => 'PPLS Site 3 – Sitio Danna, Manga, Peñablanca, Cagayan',
        'QUIBAL-S1' => 'PPLS Site 4 – Sitio Abukay, Quibal, Peñablanca, Cagayan',
        'R2-MPL-BMS-T - S1' => 'MPL SITE 1 – San Mariano, Lal-lo, Cagayan',
        'R2-MPL-BMS-T - S2' => 'MPL SITE 2 – Sitio Madupapa, Sta. Ana, Gattaran, Cagayan',
        'R2-BWFR-BMS' => 'PPLS Site 1 – Toyota Project, Cabasan, Peñablanca, Cagayan',
        'R2-WWFR-BMS-S1' => 'PPLS Site 1 – Toyota Project, Cabasan, Peñablanca, Cagayan',
    ];

    /**
     * Get the protected area that owns the site name
     */
    public function protectedArea()
    {
        return $this->belongsTo(ProtectedArea::class);
    }

    /**
     * Get site name by station code
     */
    public static function findByStationCode(string $stationCode): ?self
    {
        $siteName = self::STATION_CODE_TO_SITE_NAME[$stationCode] ?? null;
        
        if ($siteName) {
            return static::where('name', $siteName)->first();
        }

        return null;
    }

    /**
     * Primary station code used by this site (if known).
     */
    public function getStationCodeAttribute(): ?string
    {
        foreach (self::STATION_CODE_TO_SITE_NAME as $code => $siteName) {
            if ($siteName === $this->name) {
                return $code;
            }
        }

        return null;
    }

    /**
     * All known station codes mapped to this site name.
     */
    public function getAllStationCodesAttribute(): array
    {
        $codes = [];

        foreach (self::STATION_CODE_TO_SITE_NAME as $code => $siteName) {
            if ($siteName === $this->name) {
                $codes[] = $code;
            }
        }

        return $codes;
    }
}
