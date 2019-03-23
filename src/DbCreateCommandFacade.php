<?php

namespace Tonysm\Dbcreatecommand;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Tonysm\Dbcreatecommand\Skeleton\SkeletonClass
 */
class DbCreateCommandFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'dbcreatecommand';
    }
}
