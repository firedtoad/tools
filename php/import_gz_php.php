<?php
ini_set('memory_limit', '1024M');
define('SLOTS',16384);
require 'crc16.php';
function import_json($redis,$data) {
    $redis->multi(Redis::PIPELINE);
    foreach ($data as $dk=>$dv)
    {
        $key=$dv['key'];

        $type=$dv['type'];
        $value=$dv['value'];
        if(is_array($value)&&count($value))
        {
            foreach ($value as $k=>&$v)
            {
                if(!is_string($v))
                {
                    continue;
                }
                $v=hex2bin($v);
                if($v)
                {
                    $v=gzuncompress($v);
                }
            }
        }
        
        if ($type == Redis::REDIS_STRING) {
             $redis->set($key,$value);
        }
        else if ($type == Redis::REDIS_HASH) {
             $redis->hMset($key,$value);
        }
        else if ($type == Redis::REDIS_LIST) {
            foreach ($value as $k=>$v) {
                $redis->lPush($key, $v);
            }
        }
        else if ($type == Redis::REDIS_SET) {
            foreach ($value as $k=>$v) {
                $redis->sAdd($key, $v);
            }
        }
        else if ($type == Redis::REDIS_ZSET) {
            foreach ($value as $k=>$v) {
                $redis->zAdd($key, $v,$k);
            }
        }
     }
     $ret=$redis->exec();
    $err=$redis->getlasterror();
    print_r($err);
}
// $ip='172.18.40.3';
$ip='172.18.10.1';
$st_port=7001;
$ed_port=7006;
if(isset($argv[1]))
{
    $ip=$argv[1];
}else{
//     exit('need_host');
}

$ports=range($st_port,$ed_port);
$hosts=[];
foreach ($ports as $pk=>$pv)
{
    $hosts[]=$ip.':'.$pv;
}
$redis = new RedisCluster(NULL, $hosts);
$masters=$redis->_masters();
$slots=$redis->cluster($masters[0],'slots');
// $rank=$cluster->zrange('z-2:rank:14',0,-1,1);
// print_r($rank);
$import=file_get_contents('127.0.0.1gz_.json');
$importData=json_decode($import,true);
foreach ($hosts as $hk=>$hv)
{
    $redis->flushAll($hv);
}
$count=1000;
$index=0;
$rslots=[];

foreach ($slots as $k=>$v)
{
    $lslots=[$v[0],$v[1],$v[2][0],$v[2][1]];
    $rslots[]=$lslots;
}
// print_r($rslots);
// print_r($redis);
// ReflectionClass::export($redis);

$servers=[];
foreach ($rslots as $k=>$v)
{
    $servers[$v[2].':'.$v[3]]=[$v[2],$v[3],[]];
}

foreach ($importData as $k=>$v)
{
    foreach ($v as $kv=>&$vv)
    {   
        $vv['key']=$kv;
        $slot=crc16($kv)%SLOTS;
        foreach ($rslots as $ks=>$vs)
        {
            if($vs[0]<=$slot&&$vs[1]>=$slot)
            {
                $servers[$vs[2].':'.$vs[3]][2][]=$vv;
                break;
            }
        }
    }
}
foreach ($servers as $k=>$v)
{
    $r=new Redis();
    $r->connect($v[0],$v[1]);
    import_json($r, $v[2]);
}


