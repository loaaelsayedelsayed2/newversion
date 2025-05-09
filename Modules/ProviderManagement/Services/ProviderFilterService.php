<?php


namespace Modules\ProviderManagement\Services;

class ProviderFilterService
{
    public function applyAdditionalFilters($query, $filters = [])
    {
        if (isset($filters['name']) && !empty($filters['name'])) {
            $query->where('company_name', 'like', '%' . $filters['name'] . '%');
        }
        return $query;
    }
}
