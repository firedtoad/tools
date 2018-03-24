<?php
ini_set('memory_limit','2048M');
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
        $value=$redis->lrange($key,0,-1);
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

function getTypes($redis,$keys)
{
    $redis->multi(Redis::PIPELINE);
    foreach ($keys as $k=>$v)
    {
        $redis->type($v);
    }
    $ret=$redis->exec();
    $data=array_combine($keys, $ret);
    return $data;
}

function getData($redis,$keyTypes)
{
    $redis->multi(Redis::PIPELINE);
    foreach ($keyTypes as $key=>$type)
    {
        if ($type == Redis::REDIS_STRING) {
            $redis->get($key);
        }
        else if ($type == Redis::REDIS_HASH) {
             $redis->hGetAll($key);
        }
        else if ($type == Redis::REDIS_LIST) {
            $redis->lrange($key,0,-1);
        }
        else if ($type == Redis::REDIS_SET) {
             $redis->sMembers($key);
        }
        else if ($type == Redis::REDIS_ZSET) {
             $redis->zRange($key, 0, -1,1);
        }
    }
    $ret=$redis->exec();
    $keys=array_keys($keyTypes);
    $data=array_combine($keys, $ret);
    foreach ($data as $k=>&$v)
    {
        if(is_array($v)&&count($v))
        {
            foreach ($v as $kv=>&$vv)
            {   
                if(!is_string($vv))
                {
                    continue;
                }
                $vv=gzcompress($vv);
                $vv=bin2hex($vv);
            }
        }else{
        }
        $type=$keyTypes[$k];
        $v=[    
                'type'=>$type,
                'value'=>$v,
           ];
    }
    return $data;
}
$ip='172.18.10.1';
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
$masters=$redis->_masters();
$key=NULL;

$servers=[];
foreach ($masters as $mk=>$mv)
{
    $ip_port=$mv[0].':'.$mv[1];
    $r=new Redis();
    $r->connect($mv[0],$mv[1]);
    $keys=[];
    $it=NULL;
    do{
        $lkeys=$r->scan($it,"*",10000);
        $keys=array_merge($keys,$lkeys);
    }while($it>0);
    $servers[$ip_port]=[$r,$keys];
}



$count=10000;
$i=0;
$sdata=[];
foreach ($servers as $k=>$v)
{
    $r=$v[0];
    $keys=$v[1];
    $vals=[];
    $skeys=array_chunk($keys, $count);
    foreach ($skeys as $sk=>$sv)
    {
        $keyTypes=getTypes($r,$sv);
        $data=getData($r,$keyTypes);
        $vals=array_merge($vals,$data);
    }
    $sdata[$k]=$vals;
}

file_put_contents(date('Ymd').$ip."gz_.json",json_encode($sdata));
