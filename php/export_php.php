<?php

function export_json($key) {
    global $redis;
    $value=null;
    $type = $redis->type($key);
    // String
    if ($type == Redis::REDIS_STRING) {
        $value = $redis->get($key);
    }
    // Hash
    else if ($type == Redis::REDIS_HASH) {
        $value = $redis->hGetAll($key);
    }
    // List
    else if ($type == Redis::REDIS_LIST) {
        $size  = $redis->lLen($key);
        $value = array();

        for ($i = 0; $i < $size; ++$i) {
            $value[] = $redis->lIndex($key, $i);
        }
    }
    // Set
    else if ($type == Redis::REDIS_SET) {
        $value = $redis->sMembers($key);
    }
    // ZSet
    else if ($type == Redis::REDIS_ZSET) {
        $value = $redis->zRange($key, 0, -1,1);
    }
    if(is_array($value)&&count($value))
    {
        foreach ($value as $k=>&$v)
        {   
            $v=bin2hex($v);
        }
    }
    $ret=[
        'type'=>$type,
        'value'=>$value,
    ];
    return json_encode($ret);
}
$ip='127.0.0.1';
$st_port=7001;
$ed_port=7006;
if(isset($argv[1]))
{
    $ip=$argv[1];
}else{
    exit('need_host');
}

$ports=range($st_port,$ed_port);
$hosts=[]; 
foreach ($ports as $pk=>$pv)
{
    $hosts[]=$ip.':'.$pv;
}
$redis = new RedisCluster(NULL, $hosts);
$keys=$redis->keys("*");
$vals =[];
foreach ($keys as $key) {
    $vals[$key] = export_json($key);
}
file_put_contents($ip.".json",json_encode($vals));
