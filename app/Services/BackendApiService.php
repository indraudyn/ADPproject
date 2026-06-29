<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BackendApiService
{
    protected string $baseUrl;
    protected string $prefix;

    public function __construct()
    {
        // Get API configurations from .env, with defaults if not set
        $this->baseUrl = rtrim(env('BACKEND_API_URL', 'https://astadasaparwa-backend-production.up.railway.app'), '/');
        $this->prefix = trim(env('BACKEND_API_PREFIX', 'api'), '/');
    }

    /**
     * Build the full API URL for a path.
     */
    protected function getUrl(string $path): string
    {
        if ($this->prefix) {
            return "{$this->baseUrl}/{$this->prefix}/" . ltrim($path, '/');
        }
        return "{$this->baseUrl}/" . ltrim($path, '/');
    }

    /**
     * Create a pre-configured HTTP request with headers and authentication token if present in session.
     */
    protected function request(?string $token = null)
    {
        $request = Http::acceptJson()->timeout(15);

        if (!$token) {
            $token = session('jwt_token');
        }

        if ($token) {
            $request = $request->withToken($token);
        } else {
            Log::debug('BackendApiService: JWT token missing for backend request.');
        }

        return $request;
    }

    protected function adminToken(): ?string
    {
        // Try getting cached token first
        $cachedToken = Cache::get('backend_admin_jwt_token');
        if ($cachedToken) {
            return $cachedToken;
        }

        // Check if there is a configured static token in env
        $envToken = env('BACKEND_ADMIN_JWT_TOKEN');
        // Ignore the known expired token from the template
        $expiredToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MTMsImVtYWlsIjoiYWRtaW4uYWRwMThAZ21haWwuY29tIiwicm9sZSI6ImFkbWluIiwiaWF0IjoxNzgwMzE3MzIxLCJleHAiOjE3ODA0MDM3MjF9.9Ij-wuz1Dw6i0Ujti7ey2A5OE6bnqMdBAIGNJbrKFas';
        if ($envToken && $envToken !== $expiredToken) {
            return $envToken;
        }

        // Fallback: Dynamically login as Admin to get a fresh token
        $adminEmail = env('BACKEND_ADMIN_EMAIL', 'admin.adp18@gmail.com');
        $adminPassword = env('BACKEND_ADMIN_PASSWORD', 'admin123_');

        if ($adminEmail && $adminPassword) {
            try {
                Log::info('BackendApiService: Meminta token admin baru dari backend...');
                $response = $this->login($adminEmail, $adminPassword);
                if ($response->successful()) {
                    $token = $response->json('token');
                    if ($token) {
                        // Cache token for 23 hours (usually expires in 24 hours)
                        Cache::put('backend_admin_jwt_token', $token, now()->addHours(23));
                        Log::info('BackendApiService: Berhasil mendapatkan dan mencache token admin baru.');
                        return $token;
                    }
                } else {
                    Log::warning('BackendApiService: Gagal login admin otomatis. Status: ' . $response->status());
                }
            } catch (\Exception $e) {
                Log::warning('BackendApiService: Gagal mendapatkan token admin secara otomatis: ' . $e->getMessage());
            }
        }

        return null;
    }

    public function hasAdminToken(): bool
    {
        return !empty($this->adminToken());
    }

    // ==========================================
    // AUTH ENTITIES
    // ==========================================

    /**
     * Authenticate a user with email and password.
     */
    public function login(string $email, string $password)
    {
        return $this->request()->post($this->getUrl('/auth/login'), [
            'email' => $email,
            'password' => $password,
        ]);
    }

    /**
     * Register a new user on the Node.js backend.
     */
    public function register(string $name, string $email, string $password)
    {
        return $this->request()->post($this->getUrl('/auth/register'), [
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);
    }

    // ==========================================
    // USER PROFILE
    // ==========================================

    /**
     * Get the authenticated user's profile details.
     */
    public function getProfile()
    {
        return $this->request()->get($this->getUrl('/user/profile'));
    }

    /**
     * Update the authenticated user's profile details.
     */
    public function updateProfile(array $data)
    {
        return $this->request()->put($this->getUrl('/user/update'), $data);
    }

    // ==========================================
    // PARWA ENTITIES
    // ==========================================

    /**
     * Fetch parwas with pagination.
     */
    public function getParwas(int $page = 1, int $limit = 10)
    {
        return $this->request()->get($this->getUrl('/parwa'), [
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    /**
     * Fetch categories of parwa.
     */
    public function getCategories()
    {
        return $this->request()->get($this->getUrl('/parwa/categories'));
    }

    /**
     * Search parwa by a query string.
     */
    public function searchParwa(string $query)
    {
        return $this->request()->get($this->getUrl('/parwa/search'), [
            'q' => $query,
        ]);
    }

    /**
     * Fetch section content for reading.
     */
    public function getContentBySection(string $bookName, string $sectionName, ?string $version = null)
    {
        $params = [];
        if ($version) {
            $params['version'] = $version;
        }
        return $this->request()->get($this->getUrl("/parwa/content/{$bookName}/{$sectionName}"), $params);
    }

    /**
     * Fetch sections for a specific book.
     */
    public function getSectionsByBook(string $bookName, ?string $version = null)
    {
        $params = [];
        if ($version) {
            $params['version'] = $version;
        }
        return $this->request()->get($this->getUrl("/parwa/sections/{$bookName}"), $params);
    }

    /**
     * Fetch versions.
     */
    public function getVersions()
    {
        return $this->request()->get($this->getUrl('/parwa/versions'));
    }

    /**
     * Concurrently check which versions have sections for a given book.
     * Dramatically speeds up the active version filtering.
     */
    public function checkActiveVersions(string $bookName, array $allVersions): array
    {
        $activeVersions = [];
        $token = session('jwt_token');

        $responses = \Illuminate\Support\Facades\Http::pool(function (\Illuminate\Http\Client\Pool $pool) use ($bookName, $allVersions, $token) {
            $requests = [];
            foreach ($allVersions as $ver) {
                $req = $pool->as($ver)->acceptJson()->timeout(15);
                if ($token) {
                    $req = $req->withToken($token);
                }
                $requests[] = $req->get($this->getUrl("/parwa/sections/" . rawurlencode($bookName)), [
                    'version' => $ver
                ]);
            }
            return $requests;
        });

        foreach ($responses as $ver => $response) {
            if ($response instanceof \Illuminate\Http\Client\Response && $response->successful()) {
                $data = $response->json();
                if (!empty($data['data'])) {
                    $activeVersions[] = $ver;
                }
            }
        }

        return $activeVersions;
    }

    /**
     * Fetch a parwa by ID.
     */
    public function getParwaById(string $id)
    {
        return $this->request()->get($this->getUrl("/parwa/{$id}"));
    }

    // ==========================================
    // CERITA (STORIES) ENTITIES
    // ==========================================

    /**
     * Fetch stories from the backend.
     */
    public function getCeritas(int $page = 1, int $limit = 8)
    {
        return $this->request()->get($this->getUrl('/cerita'), [
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    /**
     * Fetch a specific story by ID.
     */
    public function getCeritaById(string $id)
    {
        return $this->request()->get($this->getUrl("/cerita/{$id}"));
    }

    /**
     * Fetch all stories uploaded by the current authenticated user.
     */
    public function getUserUploads()
    {
        return $this->request()->get($this->getUrl('/parwa/user/uploads'));
    }

    /**
     * Fetch all user uploaded stories for admin (Admin/Narasumber only).
     */
    public function getAdminUploads()
    {
        return $this->request()->get($this->getUrl('/parwa/admin/uploads'));
    }

    // ==========================================
    // ADMIN ENTITIES (USERS)
    // ==========================================

    /**
     * Fetch all users from the backend API (Admin only).
     */
    public function getAdminUsers(int $page = 1, int $limit = 100)
    {
        return $this->request()->get($this->getUrl('/admin/users'), [
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    /**
     * Update a user's profile/role on the backend API (Admin only).
     */
    public function updateAdminUser(string $id, array $data)
    {
        return $this->request()->put($this->getUrl("/admin/users/{$id}"), $data);
    }

    /**
     * Delete a user on the backend API (Admin only).
     */
    public function deleteAdminUser(string $id)
    {
        return $this->request()->delete($this->getUrl("/admin/users/{$id}"));
    }

    // ==========================================
    // PARWA / CERITA MANAGEMENT
    // ==========================================

    /**
     * Create a new parwa story on the backend.
     */
    public function createParwa(array $data, bool $useAdminToken = false)
    {
        $token = $useAdminToken ? $this->adminToken() : null;
        $response = $this->request($token)->post($this->getUrl('/parwa'), $data);

        if ($useAdminToken && !$response->successful() && in_array($response->status(), [401, 403], true)) {
            Log::info('BackendApiService: Admin token invalid/expired (status ' . $response->status() . '). Menghapus cache token.');
            Cache::forget('backend_admin_jwt_token');
        }

        return $response;
    }

    /**
     * Delete a parwa story on the backend.
     */
    public function deleteParwa(string $id, bool $useAdminToken = false)
    {
        $token = $useAdminToken ? $this->adminToken() : null;
        $response = $this->request($token)->delete($this->getUrl("/parwa/{$id}"));

        if ($useAdminToken && !$response->successful() && in_array($response->status(), [401, 403], true)) {
            Log::info('BackendApiService: Admin token invalid/expired (status ' . $response->status() . '). Menghapus cache token.');
            Cache::forget('backend_admin_jwt_token');
        }

        return $response;
    }

    /**
     * Update a parwa story on the backend.
     */
    public function updateParwa(string $id, array $data, bool $useAdminToken = false)
    {
        $token = $useAdminToken ? $this->adminToken() : null;
        $response = $this->request($token)->put($this->getUrl("/parwa/{$id}"), $data);

        if ($useAdminToken && !$response->successful() && in_array($response->status(), [401, 403], true)) {
            Log::info('BackendApiService: Admin token invalid/expired (status ' . $response->status() . '). Menghapus cache token.');
            Cache::forget('backend_admin_jwt_token');
        }

        return $response;
    }
}
