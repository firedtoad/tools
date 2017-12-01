<?php

function import_json($key,$data) {
    global $redis;
    $value=null;
    $type=$data['type'];
    $value=$data['value'];
    if(is_array($value)&&count($value))
    {
        foreach ($value as $k=>&$v)
        {
            $v=hex2bin($v);
        }
    }
    // String
    if ($type == Redis::REDIS_STRING) {
        $value = $redis->set($key,$value);
    }
    // Hash
    else if ($type == Redis::REDIS_HASH) {
        $value = $redis->hMset($key,$value);
    }
    // List
    else if ($type == Redis::REDIS_LIST) {
        foreach ($value as $k=>$v) {
            $redis->lPush($key, $v);
        }
    }
    // Set
    else if ($type == Redis::REDIS_SET) {
        foreach ($value as $k=>$v) {
            $redis->sAdd($key, $v);
        }
    }
    // ZSet
    else if ($type == Redis::REDIS_ZSET) {
        foreach ($value as $k=>$v) {
            $redis->zAdd($key, $v,$k);
        }
    }
}
// $ip='172.18.40.3';
$ip='127.0.0.1';
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
// $rank=$cluster->zrange('z-2:rank:14',0,-1,1);
// print_r($rank);
$import=file_get_contents('127.0.0.1.json');
$importData=json_decode($import,true);
foreach ($hosts as $hk=>$hv)
{
    $redis->flushAll($hv);
}

// print_r($importData);
foreach ($importData as $key=>$value)
{
    $value=json_decode($value,TRUE);
    import_json($key,$value);
}

