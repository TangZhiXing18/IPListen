<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2020/5/18
 * Time: 17:04
 */

namespace Tangzhixing1218\Listen\Facade;


use Tangzhixing1218\Listen\BlackListIp;

class BlackFacade extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BlackListIp::class;
    }
}
