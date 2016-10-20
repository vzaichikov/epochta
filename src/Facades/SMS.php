<?php

namespace Enniel\Epochta\Facades;

use Illuminate\Support\Facades\Facade;

class SMS extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Enniel\Epochta\SMS::class;
    }
}
