<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2020/5/18
 * Time: 17:04
 */

namespace Tangzhixing1218\Listen;


class ListenFacade extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ListenIp::class;
    }
}
