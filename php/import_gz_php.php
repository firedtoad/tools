<?php
ini_set('memory_limit', '1024M');
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
}
$ip='172.18.40.3';
// $ip='172.18.10.1';
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
// $rank=$cluster->zrange('z-2:rank:14',0,-1,1);
// print_r($rank);
$import=file_get_contents('172.18.10.1.json');
$importData=json_decode($import,true);
foreach ($hosts as $hk=>$hv)
{
    $redis->flushAll($hv);
}
$count=10000;
$index=0;
// exit();
foreach ($importData as $k=>$v)
{
    $config=$masters[$index++];
    $r=new Redis();
    $r->connect($config[0],$config[1]);
    foreach ($v as $kv=>&$vv)
    {
        $vv['key']=$kv;
    }
    $skeys=array_chunk($v, $count);
    foreach ($skeys as $key=>$value)
    {
        import_json($r,$value);
    }
}


