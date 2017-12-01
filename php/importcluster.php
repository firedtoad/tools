<?php

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
$cluster = new RedisCluster(NULL, $hosts);
// $cluster->del('z-1:rank:21');
// $rank=$cluster->zrange('z-1:rank:15',0,49,true);
// // var_dump($rank);
// foreach ($rank as $k=>$v)
// {
//     $cluster->zadd('z-1:rank:21',$v,$k);
// }

foreach ($hosts as $hk=>$hv)
{
    $cluster->flushAll($hv);
}
$import=file_get_contents('127.0.0.1dump');
$importData=json_decode($import,true);
$data=[];
foreach ($importData as $ik=>$iv)
{
    if($iv['ttl']<0)
    {
        $iv['ttl']=0;
    }
//     echo $iv['v'],"\n";
    $ret=$cluster->restore($ik,$iv['ttl'],hex2bin($iv['v']));
}
