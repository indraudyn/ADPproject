<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Centralized cache manager for Parwa detail page data.
 * 
 * Uses Laravel's Cache facade (database driver) with a 5-minute TTL.
 * Provides methods to get/set cached sections, versions, and ceritas,
 * as well as targeted invalidation when data changes.
 */
class ParwaCacheService
{
    /**
     * Cache TTL in seconds (5 minutes).
     */
    const TTL = 300;

    /**
     * Cache key prefix to allow bulk invalidation.
     */
    const PREFIX = 'parwa_detail_';

    // ════════════════════════════════════════════════════
    //  CACHE KEY BUILDERS
    // ════════════════════════════════════════════════════

    /**
     * Build the cache key for sections of a specific book + version.
     */
    public static function sectionsKey(string $bookName, ?string $version = null): string
    {
        $vKey = ($version && $version !== 'all') ? md5($version) : 'all';
        return self::PREFIX . 'sections_' . md5($bookName) . '_' . $vKey;
    }

    /**
     * Build the cache key for the list of active versions for a specific book.
     */
    public static function versionsKey(string $bookName): string
    {
        return self::PREFIX . 'versions_' . md5($bookName);
    }

    /**
     * Build the cache key for all available versions (global).
     */
    public static function allVersionsKey(): string
    {
        return self::PREFIX . 'all_versions';
    }

    /**
     * Build the cache key for local ceritas of a specific parwa.
     */
    public static function ceritasKey(int $parwaId): string
    {
        return self::PREFIX . 'ceritas_' . $parwaId;
    }

    /**
     * Build the cache key for content of a specific book, section, and version.
     */
    public static function contentBySectionKey(string $bookName, string $section, ?string $version = null): string
    {
        $vKey = ($version && $version !== 'all') ? md5($version) : 'all';
        return self::PREFIX . 'content_b_' . md5($bookName) . '_s_' . md5($section) . '_' . $vKey;
    }

    // ════════════════════════════════════════════════════
    //  CACHE GETTERS (with remember pattern)
    // ════════════════════════════════════════════════════

    /**
     * Get sections from cache or execute the callback to fetch and cache them.
     *
     * @param  string        $bookName
     * @param  string|null   $version
     * @param  callable      $fetchCallback  Must return array of sections
     * @return array
     */
    public static function rememberSections(string $bookName, ?string $version, callable $fetchCallback): array
    {
        $key = self::sectionsKey($bookName, $version);

        return Cache::remember($key, self::TTL, function () use ($fetchCallback, $key) {
            Log::debug("ParwaCacheService: Cache MISS for sections [{$key}]. Fetching from backend...");
            return $fetchCallback();
        });
    }

    /**
     * Get specific content of a section from cache or execute the callback.
     *
     * @param  string        $bookName
     * @param  string        $section
     * @param  string|null   $version
     * @param  callable      $fetchCallback
     * @return mixed
     */
    public static function rememberContentBySection(string $bookName, string $section, ?string $version, callable $fetchCallback)
    {
        $key = self::contentBySectionKey($bookName, $section, $version);

        return Cache::remember($key, self::TTL, function () use ($fetchCallback, $key) {
            Log::debug("ParwaCacheService: Cache MISS for content [{$key}]. Fetching from backend...");
            return $fetchCallback();
        });
    }

    /**
     * Get active versions for a book from cache or execute the callback.
     *
     * @param  string    $bookName
     * @param  callable  $fetchCallback  Must return array of version strings
     * @return array
     */
    public static function rememberVersions(string $bookName, callable $fetchCallback): array
    {
        $key = self::versionsKey($bookName);

        return Cache::remember($key, self::TTL, function () use ($fetchCallback, $key) {
            Log::debug("ParwaCacheService: Cache MISS for versions [{$key}]. Fetching from backend...");
            return $fetchCallback();
        });
    }

    /**
     * Get all versions (global) from cache or execute the callback.
     *
     * @param  callable  $fetchCallback  Must return array
     * @return array
     */
    public static function rememberAllVersions(callable $fetchCallback): array
    {
        return Cache::remember(self::allVersionsKey(), self::TTL, function () use ($fetchCallback) {
            Log::debug('ParwaCacheService: Cache MISS for all versions. Fetching from backend...');
            return $fetchCallback();
        });
    }

    /**
     * Get local ceritas for a parwa from cache.
     *
     * @param  int       $parwaId
     * @param  callable  $fetchCallback
     * @return mixed
     */
    public static function rememberCeritas(int $parwaId, callable $fetchCallback)
    {
        $key = self::ceritasKey($parwaId);

        return Cache::remember($key, self::TTL, function () use ($fetchCallback, $key) {
            Log::debug("ParwaCacheService: Cache MISS for ceritas [{$key}]. Querying database...");
            return $fetchCallback();
        });
    }

    // ════════════════════════════════════════════════════
    //  CACHE INVALIDATION
    // ════════════════════════════════════════════════════

    /**
     * Invalidate all cached data for a specific book (sections + versions).
     * Call this when cerita/parwa data for a specific book is created/updated/deleted.
     *
     * @param  string|null  $bookName  The backend book name (e.g. "Adi Parva")
     * @param  int|null     $parwaId   The local parwa ID (for ceritas cache)
     */
    public static function invalidateForBook(?string $bookName = null, ?int $parwaId = null): void
    {
        Log::info('ParwaCacheService: Invalidating cache', [
            'book' => $bookName,
            'parwa_id' => $parwaId,
        ]);

        // Clear all versions (global)
        Cache::forget(self::allVersionsKey());

        if ($bookName) {
            // Clear sections for this book (all version variants)
            Cache::forget(self::sectionsKey($bookName, null));
            Cache::forget(self::versionsKey($bookName));

            // Also clear content blocks for this book via DB wildcard query
            try {
                $bookHash = md5($bookName);
                \Illuminate\Support\Facades\DB::table('cache')
                    ->where('key', 'like', '%' . self::PREFIX . 'content_b_' . $bookHash . '_%')
                    ->delete();
            } catch (\Exception $e) {
                // Silent
            }

            // Clear the old per-version section check cache keys
            // (the ones already used in ParwaController before this service)
            $cachePattern = 'parwa_version_has_sections_v3_';
            // We can't iterate all keys in database cache driver easily,
            // so we clear specific known ones when a version list is available.
            try {
                $allVersions = Cache::get(self::allVersionsKey(), []);
                foreach ($allVersions as $ver) {
                    Cache::forget($cachePattern . md5($bookName . '_' . $ver));
                    Cache::forget(self::sectionsKey($bookName, $ver));
                }
            } catch (\Exception $e) {
                // Silent
            }
        }

        if ($parwaId) {
            Cache::forget(self::ceritasKey($parwaId));
        }
    }

    /**
     * Invalidate ALL parwa-related caches.
     * Call this on logout or when a broad data change happens.
     */
    public static function invalidateAll(): void
    {
        Log::info('ParwaCacheService: Invalidating ALL parwa caches');

        // Clear the global versions cache
        Cache::forget(self::allVersionsKey());

        // For the database cache driver, we flush all parwa_ prefixed keys.
        // Since Laravel's database cache doesn't support tag-based clearing easily,
        // we use the cache_locks + cache table directly.
        try {
            \Illuminate\Support\Facades\DB::table('cache')
                ->where('key', 'like', '%' . self::PREFIX . '%')
                ->delete();

            // Also clear the old per-version check cache keys
            \Illuminate\Support\Facades\DB::table('cache')
                ->where('key', 'like', '%parwa_version_has_sections%')
                ->delete();
        } catch (\Exception $e) {
            Log::warning('ParwaCacheService: Failed to bulk-clear cache from DB: ' . $e->getMessage());
            // Fallback: try flushing all cache (less targeted but works)
            // Cache::flush(); // Uncomment if needed
        }
    }
}
