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

            if ($request->has('latitude') && $request->has('longitude')) {
                $latitude = $request->input('latitude');
                $longitude = $request->input('longitude');
                $radius = $request->input('radius', 5);

                $query->whereRaw(
                    "ST_Distance_Sphere(
                        POINT(?, ?),
                        POINT(
                            JSON_UNQUOTE(JSON_EXTRACT(coordinates, '$.longitude')),
                            JSON_UNQUOTE(JSON_EXTRACT(coordinates, '$.latitude'))
                        )
                    ) <= ? * 1000",
                    [$longitude, $latitude, $radius]
                )->orderByRaw(
                    "ST_Distance_Sphere(
                        POINT(?, ?),
                        POINT(
                            JSON_UNQUOTE(JSON_EXTRACT(coordinates, '$.longitude')),
                            JSON_UNQUOTE(JSON_EXTRACT(coordinates, '$.latitude'))
                        )
                    )",
                    [$longitude, $latitude]
                );
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
