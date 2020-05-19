<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2020/5/19
 * Time: 13:54
 */
namespace Tangzhixing1218\Listen;
use Illuminate\Support\Facades\Redis;
use Tangzhixing1218\Listen\Exception\BlackListException;

class BlackListIp
{
    protected $request_total_num;
    protected $request_one_num;
    protected $black_expire;
    public function __construct(int $request_total_num,$black_expire,$request_one_num)
    {
        $this->request_total_num = $request_total_num;
        $this->black_expire = $black_expire;
        $this->request_one_num = $request_one_num;
    }

    /**
     * @throws BlackListException
     * 启动服务
     */
    public function start()
    {
        //获取客户端ip地址
        $ip = $this->getIp();

        //获取url信息
        $self = $_SERVER["REQUEST_URI"];//获取网页地址
        //获取访问次数
        $num = Redis::hget('black:'.$ip,$self);
        $total_num = Redis::get('black_total:'.$ip);
        //判断访问次数
        if($num>=$this->request_one_num || $total_num>=$this->request_total_num) {
            //将该ip加入风险ip名单
            $len = Redis::llen('risk_ip');
            $risk_arr = Redis::LRANGE('risk_ip',0,$len);
            if(!in_array($ip,$risk_arr)){
                Redis::rpush('risk_ip',$ip);
            }
            throw new BlackListException('由于您的访问次数远远超过正常的访问量，现对您进行限制访问'.$this->black_expire.'秒，如有疑问请联系运营商');
        }
        //增加访问次数
        if(!$num){
            //单接口访问次数
            Redis::hset('black:'.$ip,$self,1);
            Redis::expire('black:'.$ip,$this->black_expire);
        }else{
            //单接口访问次数+1
            Redis::HINCRBY('black:'.$ip,$self,1);

        }

        if(!$total_num){
            //总访问次数
            Redis::set('black_total:'.$ip,1);
            Redis::expire('black_total:'.$ip,$this->black_expire);
        }else{
            //总次数+1
            Redis::INCRBY('black_total:'.$ip,1);
        }

        return true;

    }

    public function risk_ip($num,$page)
    {
        $pagenum = ($page-1)*$num;
        $num = $num * $page;
        $risk_result = Redis::LRANGE('risk_ip',$pagenum,$num);
        return $risk_result;
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
