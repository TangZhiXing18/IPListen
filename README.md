<h1 align="center"> listen </h1>

<p align="center"> .</p>


## Installing

```shell
$ composer require tangzhixing1218/listen -vvv
```

## Usage

TODO

## 配置
在laravel config/service.php下配置

    'listen'=>[
        'max_num'=>10,限流次数
        'ip_expire'=>30,限流分钟数
        
        //这个是监听ip，一般配置成会被人恶意攻击能达到
        的次数，除了静止访问，还会记录进危险名单
        
        'request_total_num'=>30,请求最大总次数
        'request_one_num'=>20,单个接口请求次数
        'black_expire'=>60*5,//超过次数静止访问的时间
    ],
    
    //静止访问的IP
    'black_array'=>[
        '127.0.0.1','未知IP'
    ]
## 使用说明
    方法一般使用在中间件里面
    
    ListenFacade::start();//限流 成功返回true 否则抛出异常
    BlackFacade::start();//ip监听 成功返回true 否则抛出异常
    BlackFacade::risk_ip;//返回危险IP的数组
    ForbidenFacade::start();//按black_array配置，禁止配置的ip访问，
    成功返回true,否则抛出异常
    
MIT
