<?php


namespace Modules\ProviderManagement\Services;

class ProviderFilterService
{
    public function applyAdditionalFilters($request)
    {
        return function ($query) use ($request) {
            if ($request->has('name')) {
                $query->where('company_name', 'like', '%' . $request->input('name') . '%');
            }
        };
    }
}
