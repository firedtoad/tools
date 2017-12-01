<?php
$bundle_id = 'xxxx';
$api_token = 'xxxx';


function post($url,$data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_USERAGENT, '12fsdczx.net');
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $ret=curl_exec($ch);
    return $ret;
}
function post_file($url,$data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_USERAGENT, '12fsdczx.net');
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $ret=curl_exec($ch);
    var_dump($ret);
    return $ret;
}
$data="{\"type\":\"ios\", \"bundle_id\":\"$bundle_id\", \"api_token\":\"$api_token\"}";
$data=json_decode($data,true);
$ret=post("http://api.fir.im/apps",$data);
echo $ret,"\n";
$json=json_decode($ret,true);
echo $json['cert']['icon']['key'],"\n";
echo $json['cert']['icon']['token'],"\n";
echo $json['cert']['icon']['upload_url'],"\n";
$file=new CURLFile('Icon-153.png');
$data=[
    'key'=>$json['cert']['icon']['key'],
    'token'=>$json['cert']['icon']['token'],
    'file'=>$file,
];

$ret=post_file($json['cert']['icon']['upload_url'], $data);
echo $ret;

