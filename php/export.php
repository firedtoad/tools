<?php

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
$cluster = new RedisCluster(NULL, $hosts);
// $redis=new Redis();
// $redis->dump();
// $redis->connect();
$keys=$cluster->keys("*");
// mkdir('dump');
$data=[];
foreach ($keys as $k => $v) {
    
    $data[$v]=['ttl'=>$cluster->ttl($v),'v'=>bin2hex($cluster->dump($v))];
}
file_put_contents($ip."dump",json_encode($data));

