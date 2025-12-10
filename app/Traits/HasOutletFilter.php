<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasOutletFilter
{
    /**
     * Apply outlet filter to query based on user access
     *
     * @param Builder $query
     * @param string $outletColumn Column name for outlet_id (default: 'outlet_id')
     * @return Builder
     */
    protected function applyOutletFilter(Builder $query, string $outletColumn = 'outlet_id'): Builder
    {
        $user = auth()->user();

        // Super admin can see all outlets
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        // Filter by user's accessible outlets
        $outletIds = $user->outlets->pluck('id_outlet')->toArray();
        
        if (empty($outletIds)) {
            // If user has no outlet access, return empty result
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn($outletColumn, $outletIds);
    }

    /**
     * Get user's accessible outlet IDs
     *
     * @return array
     */
    protected function getUserOutletIds(): array
    {
        $user = auth()->user();

        // Super admin has access to all outlets
        if ($user->hasRole('super_admin')) {
            return \App\Models\Outlet::pluck('id_outlet')->toArray();
        }

        return $user->outlets->pluck('id_outlet')->toArray();
    }

    /**
     * Get user's accessible outlets
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getUserOutlets()
    {
        $user = auth()->user();

        // Super admin has access to all outlets
        if ($user->hasRole('super_admin')) {
            return \App\Models\Outlet::all();
        }

        return $user->outlets;
    }

    /**
     * Check if user has access to specific outlet
     *
     * @param int $outletId
     * @return bool
     */
    protected function hasOutletAccess(int $outletId): bool
    {
        $user = auth()->user();

        // Super admin has access to all outlets
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->hasAccessToOutlet($outletId);
    }

    /**
     * Get selected outlet ID from request or session
     *
     * @param \Illuminate\Http\Request|null $request
     * @return int|null
     */
    protected function getSelectedOutlet($request = null)
    {
        $request = $request ?? request();
        
        // Get from request parameter first
        if ($request->filled('outlet_id')) {
            $outletId = $request->outlet_id;
            session(['selected_outlet_id' => $outletId]);
            return $outletId;
        }

        // Get from session
        if (session()->has('selected_outlet_id')) {
            return session('selected_outlet_id');
        }

        // Get first accessible outlet
        $outlets = $this->getAccessibleOutlets();
        if ($outlets->isNotEmpty()) {
            $outletId = $outlets->first()->id_outlet;
            session(['selected_outlet_id' => $outletId]);
            return $outletId;
        }

        return null;
    }

    /**
     * Get user's accessible outlets collection
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getAccessibleOutlets()
    {
        return $this->getUserOutlets();
    }

    /**
     * Get user's accessible outlet IDs
     *
     * @return array
     */
    protected function getAccessibleOutletIds(): array
    {
        return $this->getUserOutletIds();
    }

    /**
     * Check if current user is super admin
     *
     * @return bool
     */
    protected function isSuperAdmin(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole('super_admin');
    }

    /**
     * Validate outlet access and throw 403 if unauthorized
     *
     * @param int $outletId
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function validateOutletAccess(int $outletId): void
    {
        if (!$this->isSuperAdmin()) {
            $accessibleIds = $this->getAccessibleOutletIds();
            if (!in_array($outletId, $accessibleIds)) {
                abort(403, 'Anda tidak memiliki akses ke outlet ini.');
            }
        }
    }
}
