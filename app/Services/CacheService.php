<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Centralized Cache Management Service
 * 
 * Provides consistent caching strategy across the application
 */
class CacheService
{
    /**
     * Default cache duration in seconds (1 hour)
     */
    const DEFAULT_TTL = 3600;

    /**
     * Short cache duration (5 minutes) - untuk data yang sering berubah
     */
    const SHORT_TTL = 300;

    /**
     * Long cache duration (24 hours) - untuk data yang jarang berubah
     */
    const LONG_TTL = 86400;

    /**
     * Remember data with caching
     *
     * @param string $key
     * @param callable $callback
     * @param int|null $ttl
     * @return mixed
     */
    public static function remember(string $key, callable $callback, ?int $ttl = null)
    {
        $ttl = $ttl ?? self::DEFAULT_TTL;
        
        try {
            return Cache::remember($key, $ttl, $callback);
        } catch (\Exception $e) {
            Log::warning("Cache remember failed for key: {$key}", [
                'error' => $e->getMessage()
            ]);
            // Fallback: execute callback directly
            return $callback();
        }
    }

    /**
     * Get cached data
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        try {
            return Cache::get($key, $default);
        } catch (\Exception $e) {
            Log::warning("Cache get failed for key: {$key}", [
                'error' => $e->getMessage()
            ]);
            return $default;
        }
    }

    /**
     * Put data into cache
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $ttl
     * @return bool
     */
    public static function put(string $key, $value, ?int $ttl = null): bool
    {
        $ttl = $ttl ?? self::DEFAULT_TTL;
        
        try {
            return Cache::put($key, $value, $ttl);
        } catch (\Exception $e) {
            Log::warning("Cache put failed for key: {$key}", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Forget cached data
     *
     * @param string $key
     * @return bool
     */
    public static function forget(string $key): bool
    {
        try {
            return Cache::forget($key);
        } catch (\Exception $e) {
            Log::warning("Cache forget failed for key: {$key}", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Flush cache by pattern
     *
     * @param string $pattern
     * @return void
     */
    public static function flushPattern(string $pattern): void
    {
        try {
            // For file cache, we need to manually iterate
            // This is a simple implementation
            $keys = Cache::get('cache_keys', []);
            foreach ($keys as $key) {
                if (str_contains($key, $pattern)) {
                    Cache::forget($key);
                }
            }
        } catch (\Exception $e) {
            Log::warning("Cache flush pattern failed for: {$pattern}", [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate cache key for outlet-specific data
     *
     * @param string $prefix
     * @param int $outletId
     * @param array $params
     * @return string
     */
    public static function outletKey(string $prefix, int $outletId, array $params = []): string
    {
        $paramString = empty($params) ? '' : '_' . md5(json_encode($params));
        return "{$prefix}_outlet_{$outletId}{$paramString}";
    }

    /**
     * Generate cache key for user-specific data
     *
     * @param string $prefix
     * @param int $userId
     * @param array $params
     * @return string
     */
    public static function userKey(string $prefix, int $userId, array $params = []): string
    {
        $paramString = empty($params) ? '' : '_' . md5(json_encode($params));
        return "{$prefix}_user_{$userId}{$paramString}";
    }

    /**
     * Generate cache key for date range queries
     *
     * @param string $prefix
     * @param string|null $startDate
     * @param string|null $endDate
     * @param array $params
     * @return string
     */
    public static function dateRangeKey(string $prefix, ?string $startDate, ?string $endDate, array $params = []): string
    {
        $dateString = ($startDate ?? 'all') . '_' . ($endDate ?? 'all');
        $paramString = empty($params) ? '' : '_' . md5(json_encode($params));
        return "{$prefix}_date_{$dateString}{$paramString}";
    }

    /**
     * Clear all outlet-related caches
     *
     * @param int $outletId
     * @return void
     */
    public static function clearOutletCache(int $outletId): void
    {
        self::flushPattern("outlet_{$outletId}");
        Log::info("Cleared cache for outlet: {$outletId}");
    }

    /**
     * Clear all user-related caches
     *
     * @param int $userId
     * @return void
     */
    public static function clearUserCache(int $userId): void
    {
        self::flushPattern("user_{$userId}");
        Log::info("Cleared cache for user: {$userId}");
    }
}
