<?php

namespace Keenops\Mpesa;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Keenops\LaravelMpesa\Skeleton\SkeletonClass
 */
class MpesaFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-mpesa';
    }
}
