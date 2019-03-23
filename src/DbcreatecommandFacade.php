<?php

namespace Tonysm\Dbcreatecommand;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Tonysm\Dbcreatecommand\Skeleton\SkeletonClass
 */
class DbcreatecommandFacade extends Facade
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
