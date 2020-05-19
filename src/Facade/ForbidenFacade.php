<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2020/5/19
 * Time: 15:16
 */
namespace Tangzhixing1218\Listen\Facade;


use Tangzhixing1218\Listen\ForbiddenTool;

class ForbidenFacade extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ForbiddenTool::class;
    }
}
