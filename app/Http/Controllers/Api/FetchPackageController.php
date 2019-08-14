<?php

namespace Cmpsr\Http\Controllers\Api;

use Cmpsr\Package;
use Illuminate\Http\JsonResponse;
use Cmpsr\Http\Controllers\Controller;

class FetchPackageController extends Controller
{
    /**
     * @param  Package $package
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Package $package): JsonResponse
    {
        return response()->json([
            'url' => $package->url,
            'hash' => $package->hash,
        ]);
    }
}
