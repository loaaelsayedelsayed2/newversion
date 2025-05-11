<?php

namespace Modules\ProviderManagement\Services;

class ProviderFilterService
{
    public function applyAdditionalFilters($request)
    {
        dd('jgsgs');
        return function ($query) use ($request) {
            if ($request->has('name')) {
                $query->where('company_name', 'like', '%' . $request->input('name') . '%');
            }

            if ($request->has('rating')) {
                $query->orderBy('avg_rating', 'desc');
            }

            if ($request->has('favorites_only')) {
                $customerUserId = $request->input('customer_user_id');
                $query->whereHas('favorites', function ($q) use ($customerUserId) {
                    $q->where('customer_user_id', $customerUserId);
                });
            }
        };
    }
}
