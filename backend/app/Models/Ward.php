<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ward extends Model
{
    use HasFactory;

    protected $table = 'wards';
    protected $fillable = ['constituency_id', 'ward_number', 'name', 'name_kn', 'geom', 'boundry'];

    public function constituency(): BelongsTo
    {
        return $this->belongsTo(Constituency::class, 'constituency_id');
    }

    /**
     * Find Ward by Lat & Lng using spatial polygon lookup with nearest fallback.
     */
    public static function findWardByLatLng($lat, $lng)
    {
        $lat = (float) $lat;
        $lng = (float) $lng;

        if (!$lat || !$lng) return null;

        try {
            // 1. Try MySQL ST_Contains on geom spatial column
            $ward = self::with('constituency.corporation')
                ->whereRaw("ST_Contains(geom, ST_GeomFromText(?))", ["POINT({$lng} {$lat})"])
                ->first();
            if ($ward) return $ward;

            // 2. Try ST_GeomFromGeoJSON on boundry column
            $ward = self::with('constituency.corporation')
                ->whereNotNull('boundry')
                ->whereRaw("ST_Contains(ST_GeomFromGeoJSON(boundry), ST_GeomFromText(?))", ["POINT({$lng} {$lat})"])
                ->first();
            if ($ward) return $ward;

            // 3. Fallback to nearest Ward by distance
            $ward = self::with('constituency.corporation')
                ->whereNotNull('geom')
                ->orderByRaw("ST_Distance(geom, ST_GeomFromText(?)) ASC", ["POINT({$lng} {$lat})"])
                ->first();
            if ($ward) return $ward;
        } catch (\Throwable $e) {
            return self::with('constituency.corporation')->first();
        }

        return self::with('constituency.corporation')->first();
    }
}
