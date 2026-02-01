<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DynamicTableService
{
    /**
     * Get all observation tables (static + dynamic + site-specific)
     */
    public static function getAllObservationTables()
    {
        // Static tables (existing ones)
        $staticTables = [
            'batanes_tbl', 'baua_tbl', 'fuyot_tbl', 'magapit_tbl', 'palaui_tbl', 
            'quirino_tbl', 'mariano_tbl', 'madupapa_tbl', 'wangag_tbl', 'toyota_tbl', 
            'manga_tbl', 'quibal_tbl', 'madre_tbl', 'tumauini_tbl', 'bangan_tbl', 
            'salinas_tbl', 'dupax_tbl', 'casecnan_tbl', 'dipaniong_tbl', 'roque_tbl'
        ];
        
        // Get all dynamic tables from the database
        $dynamicTables = self::getDynamicTables();
        
        // Get all site-specific tables
        $siteSpecificTables = self::getSiteSpecificTables();
        
        // Merge and return unique tables that actually exist
        $allTables = array_unique(array_merge($staticTables, $dynamicTables, $siteSpecificTables));
        
        // Filter to only include tables that actually exist
        return array_filter($allTables, function($table) {
            return Schema::hasTable($table);
        });
    }
    
    /**
     * Get dynamically created tables
     */
    private static function getDynamicTables()
    {
        try {
            // Get all tables that end with _tbl but are not in the static list
            // Exclude _site_tbl tables as they are handled separately
            $allTables = DB::select('SHOW TABLES');
            $tableNames = [];
            
            foreach ($allTables as $table) {
                $tableName = array_values((array)$table)[0];
                if (str_ends_with($tableName, '_tbl') && !str_ends_with($tableName, '_site_tbl')) {
                    $tableNames[] = $tableName;
                }
            }
            
            return $tableNames;
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Get all site-specific tables (_site_tbl)
     */
    private static function getSiteSpecificTables()
    {
        try {
            // Get all tables that end with _site_tbl
            $allTables = DB::select('SHOW TABLES');
            $tableNames = [];
            
            foreach ($allTables as $table) {
                $tableName = array_values((array)$table)[0];
                if (str_ends_with($tableName, '_site_tbl')) {
                    $tableNames[] = $tableName;
                }
            }
            
            return $tableNames;
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Get table name for a protected area code
     */
    public static function getTableNameForProtectedArea($code)
    {
        // Special cases for existing protected areas
        $specialCases = [
            'BPLS' => 'batanes_tbl',
            'FSNP' => 'fuyot_tbl',
            'QPL' => 'quirino_tbl',
            'PIPLS' => 'palaui_tbl',
            'BWFR' => 'buaa_tbl',
            'WWFR' => 'wangag_tbl',
            'MPL' => 'magapit_tbl',
            'MADUPAPA' => 'madupapa_tbl',
            'SANMARIANO' => 'mariano_tbl',
            'NSMNP' => 'madre_tbl',
            'TWNP' => 'tumauini_tbl',
            'BHNP' => 'bangan_tbl',
            'SNM' => 'salinas_tbl',
            'DWFR' => 'dupax_tbl',
            'CPL' => 'casecnan_tbl',
            'DNP' => 'dipaniong_tbl',
        ];
        
        if (isset($specialCases[$code])) {
            return $specialCases[$code];
        }
        
        // For dynamic tables, use the same logic as ProtectedAreaController::createSafeTableName
        $safeName = strtolower($code);
        $safeName = preg_replace('/[^a-z0-9]/', '', $safeName);
        return $safeName . '_tbl';
    }
    
    /**
     * Get dynamic model class for a table
     */
    public static function getModelForTable($tableName)
    {
        // Check if it's a known static model
        $modelMap = [
            'batanes_tbl' => 'App\\Models\\BmsSpeciesObservation',
            'fuyot_tbl' => 'App\\Models\\FuyotObservation',
            'quirino_tbl' => 'App\\Models\\QuirinoObservation',
            'palaui_tbl' => 'App\Models\PalauiObservation',
            'buaa_tbl' => 'App\Models\BauaObservation',
            'wangag_tbl' => 'App\Models\WangagObservation',
            'magapit_tbl' => 'App\Models\MagapitObservation',
            'madupapa_tbl' => 'App\\Models\\MadupapaObservation',
            'mariano_tbl' => 'App\\Models\\MarianoObservation',
            'madre_tbl' => 'App\\Models\\MadreObservation',
            'tumauini_tbl' => 'App\\Models\\TumauiniObservation',
            'bangan_tbl' => 'App\\Models\\BanganObservation',
            'salinas_tbl' => 'App\\Models\\SalinasObservation',
            'dupax_tbl' => 'App\\Models\\DupaxObservation',
            'casecnan_tbl' => 'App\\Models\\CasecnanObservation',
            'dipaniong_tbl' => 'App\\Models\\DipaniongObservation',
            'toyota_tbl' => 'App\\Models\\ToyotaObservation',
            'roque_tbl' => 'App\\Models\\SanRoqueObservation',
            'manga_tbl' => 'App\\Models\\MangaObservation',
            'quibal_tbl' => 'App\\Models\\QuibalObservation',
        ];
        
        if (isset($modelMap[$tableName])) {
            return $modelMap[$tableName];
        }
        
        // For dynamic tables, return a generic dynamic model
        return 'App\\Models\\DynamicObservation';
    }
    
    /**
     * Create a dynamic model instance for a table
     */
    public static function createDynamicModel($tableName)
    {
        $modelClass = self::getModelForTable($tableName);
        
        if ($modelClass === 'App\\Models\\DynamicObservation') {
            // Create a simple dynamic model class
            $dynamicModel = new class extends \App\Models\BaseObservation {
                protected static $tableName;
                
                public static function setTableName($name)
                {
                    static::$tableName = $name;
                }
                
                public function getTable()
                {
                    return static::$tableName ?: parent::getTable();
                }
            };
            
            $dynamicModel::setTableName($tableName);
            return $dynamicModel;
        }
        
        return new $modelClass;
    }
    
    /**
     * Drop observation table for a protected area
     */
    public static function dropObservationTable($code)
    {
        try {
            $tableName = self::getTableNameForProtectedArea($code);
            
            if (Schema::hasTable($tableName)) {
                Schema::dropIfExists($tableName);
                \Log::info("Successfully dropped observation table: {$tableName}");
                return true;
            } else {
                \Log::info("Observation table {$tableName} does not exist, skipping drop.");
                return false;
            }
        } catch (\Exception $e) {
            \Log::error("Failed to drop observation table for {$code}: " . $e->getMessage());
            return false;
        }
    }
}
