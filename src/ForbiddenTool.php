<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2020/5/19
 * Time: 15:07
 */
namespace Tangzhixing1218\Listen;
use Tangzhixing1218\Listen\Exception\BlackListException;

class ForbiddenTool
{
    protected $black_array;

    public function __construct($black_array)
    {
        $this->black_array = $black_array;
    }

    public function start()
    {
        $ip = $this->getIp();
        if(in_array($ip,$this->black_array)){
            throw new BlackListException('您已被服务商停止访问,请联系服务商解决');
        }
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
