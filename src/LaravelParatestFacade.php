<?php

namespace Tonysm\LaravelParatest;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Tonysm\LaravelParatest\Skeleton\SkeletonClass
 */
class LaravelParatestFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-paratest';
    }
}
