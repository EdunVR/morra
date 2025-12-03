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
     * Validate if user has access to specific outlet
     *
     * @param int $outletId
     * @return bool
     */
    protected function validateOutletAccess(int $outletId): bool
    {
        $user = auth()->user();

        // Super admin has access to all outlets
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->hasAccessToOutlet($outletId);
    }

    /**
     * Throw 403 if user doesn't have access to outlet
     *
     * @param int $outletId
     * @return void
     */
    protected function authorizeOutletAccess(int $outletId): void
    {
        if (!$this->validateOutletAccess($outletId)) {
            abort(403, 'Anda tidak memiliki akses ke outlet ini.');
        }
    }
}
