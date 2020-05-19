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

        $this->app->singleton(BlackListIp::class, function(){
            return new BlackListIp(config('services.listen.request_total_num'),config('services.listen.black_expire')
                ,config('services.listen.request_one_num'));
        });

        $this->app->singleton(ForbiddenTool::class, function(){
            return new ForbiddenTool(config('services.black_array'));
        });

        $this->app->alias(ListenIp::class, 'listen');
    }

    public function provides()
    {
        return [ListenIp::class, 'listen',BlackListIp::class,ForbiddenTool::class];
    }
}
