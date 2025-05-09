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
                $radius = $request->input('radius', 5); // Default radius 5 km

                $query->whereRaw(
                    "ST_Distance_Sphere(
                        POINT(?, ?),
                        POINT(
                            JSON_UNQUOTE(JSON_EXTRACT(coordinates, '$.lng')),
                            JSON_UNQUOTE(JSON_EXTRACT(coordinates, '$.lat'))
                        )
                    ) <= ? * 1000",
                    [$longitude, $latitude, $radius]
                )->orderByRaw(
                    "ST_Distance_Sphere(
                        POINT(?, ?),
                        POINT(
                            JSON_UNQUOTE(JSON_EXTRACT(coordinates, '$.lng')),
                            JSON_UNQUOTE(JSON_EXTRACT(coordinates, '$.lat'))
                        )
                    )",
                    [$longitude, $latitude]
                );
            }
        };
    }
}
