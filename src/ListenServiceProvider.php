<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2020/5/18
 * Time: 15:19
 */
namespace Tangzhixing1218\Listen;

class ListenServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(ListenIp::class, function(){
            return new ListenIp(config('services.listen.max_num'),config('services.listen.black_expire')
            ,config('services.listen.ip_expire'));
        });

        $this->app->alias(ListenIp::class, 'listen');
    }

    public function provides()
    {
        return [ListenIp::class, 'listen'];
    }
}
