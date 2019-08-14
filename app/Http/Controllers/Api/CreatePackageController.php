<?php

namespace Cmpsr\Http\Controllers\Api;

use Cmpsr\Package;
use Illuminate\Http\JsonResponse;
use Cmpsr\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Routing\UrlGenerator;
use Cmpsr\Http\Requests\Api\CreatePackageRequest;
use Illuminate\Http\Response;

class CreatePackageController extends Controller
{
    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $storage;

    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $publicStorage;

    /**
     * @var \Illuminate\Contracts\Routing\UrlGenerator
     */
    protected $url;

    /**
     * @return void
     */
    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
        $this->storage = Storage::disk('packages');
        $this->publicStorage = Storage::disk('public');
    }

    /**
     * @param  CreatePackageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(CreatePackageRequest $request): JsonResponse
    {
        $hash = $request->dataHash();

        if ($this->storage->put("{$hash}/composer.json", $request->data())) {
            Artisan::call("composer:install {$hash}");

            if ($this->publicStorage->exists("{$hash}.zip")) {
                $package = Package::query()->create(['hash' => $hash]);
            }
        }

        return response()->json(
            array_filter([
                'hash' => $hash,
                'url' => $package->url ?? null,
                'status' => (bool) ($package ?? false),
            ])
        );
    }
}
