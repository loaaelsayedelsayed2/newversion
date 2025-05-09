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

                $query->orderByRaw(
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
