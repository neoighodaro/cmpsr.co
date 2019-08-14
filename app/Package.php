<?php

namespace Cmpsr;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read string $hash
 * @property-read string $url
 */
class Package extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'hash'
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'hash';
    }

    /**
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return app('url')->asset('packages/' . $this->attributes['hash'] . '.zip');
    }
}
