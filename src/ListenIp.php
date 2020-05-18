<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2020/5/18
 * Time: 14:30
 */
namespace Tangzhixing1218\Listen;
use Illuminate\Support\Facades\Redis;
use Tangzhixing1218\Listen\Exception\BlackListException;

class ListenIp
{
    protected $request_max_num;
    protected $black_expire;
    protected $ip_expire;
    public function __construct(int $request_max_num,$black_expire,$ip_expire)
    {
        $this->request_max_num = $request_max_num;
        $this->black_expire = $black_expire;
        $this->ip_expire = $ip_expire;
    }

    public function start()
    {
        //获取客户端ip地址
        $ip = $this->getIp();
        //判断该ip是否在黑名单
        if(Redis::get($ip.'blacklist')){
            throw new BlackListException('该ip存在恶意攻击,被加入黑名单，请联系服务商接触限制');
        }

        //获取url信息
        $self = $_SERVER["REQUEST_URI"];//获取网页地址
//        $methon = $_SERVER['REQUEST_METHOD'];//客户端请求方法
        //判断当前ip是否访问过该url
        $request_num = Redis::hget($ip,$self);
        if($request_num){
            //达到访问上限,加入黑名单,且拒绝访问
            if($request_num>=$this->request_max_num){
                Redis::set($ip.'blacklist',1);
                Redis::expire($ip.'blacklist',$this->black_expire);
                throw new BlackListException('该ip存在恶意攻击,被加入黑名单，请联系服务商接触限制');
            }
            //访问次数+1
            Redis::HINCRBY($ip,$self,1);
        }else{
            Redis::hset($ip,$self,1);
            Redis::expire($ip,$this->ip_expire);
        }
        //访问成功
        return true;

    }

    /**
     * @return string
     * 获取客户端ip地址
     */
    public function getIp(){
        $ip='未知IP';
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            return $this->is_ip($_SERVER['HTTP_CLIENT_IP'])?$_SERVER['HTTP_CLIENT_IP']:$ip;
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            return $this->is_ip($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$ip;
        }else{
            return $this->is_ip($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:$ip;
        }
    }

    /**
     * @param $str
     * @return bool|false|int
     * 验证ip
     */
    public function is_ip($str){
        $ip=explode('.',$str);
        for($i=0;$i<count($ip);$i++){
            if($ip[$i]>255){
                return false;
            }
        }
        return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/',$str);
    }
}
