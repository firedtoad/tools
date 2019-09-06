<?php
function buildBaseString($baseURI, $method, $params){
    $r = array();
    ksort($params);
    foreach($params as $key=>$value){
        $r[] = "$key=" . rawurlencode($value);
    }

    return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
}

function buildAuthorizationHeader($oauth){
    $r = 'Authorization: OAuth ';
    $values = array();
    foreach($oauth as $key=>$value)
        $values[] = "$key=\"" . rawurlencode($value) . "\"";

    $r .= implode(', ', $values);
    return $r;
}

function params($str,$sep1='&',$sep2='=')
{
    $ret=[];
    $exp=explode($sep1, $str);
    foreach ($exp as $k=>$v)
    {
        $tmp=explode($sep2, $v);
        if(count($tmp)==2)
        {
            $ret[$tmp[0]]=$tmp[1];
        }
    }
    return $ret;
}

$
// Add request, authorize, etc to end of URL based on what call you're making
$url = "http://127.0.0.1:8088/oauth/applogin/VerifySPAccount";

$consumer_key = 1234546;
$consumer_secret = "123456";
$data=[
    'entryPoint'=>60007,
    'clientIp'=>'182.149.116.2',
    'channelId'=>208,
    'uid'=>134428,
    'accessToken'=>'df81775f702114c6b1bbfb1af2a11744',
    'devicePlatform'=>'android',
    'deviceUUID'=>'966f5941-62b5-38ae-ad9e-679affef324b',
];

$oauth = array( 'oauth_consumer_key' => $consumer_key,
                'oauth_nonce' => intval(microtime(true)).dechex(mt_rand()),
                'oauth_signature_method' => 'HMAC-SHA1',
                'oauth_timestamp' => intval(microtime(true)),
                'oauth_version' => '1.0',
);
$oauth=array_merge($oauth,$data);
// echo http_build_query($data);
$base_info = buildBaseString($url, 'GET', $oauth);
echo $base_info,"\n";
$composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode('');
// echo $composite_key;
$oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));

$oauth['oauth_signature'] = $oauth_signature;

$oauth=array_merge($oauth,$data);

echo $url.='?'.http_build_query($oauth),"\n";

$header = array(buildAuthorizationHeader($oauth));
print_r($oauth);

$options = array( 
//     CURLOPT_HTTPHEADER => $header,
//                   CURLOPT_POST=>TRUE,
//                   CURLOPT_POSTFIELDS=>http_build_query($oauth),
                  CURLOPT_HEADER => false,
                  CURLOPT_URL => $url,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_SSL_VERIFYPEER => false);

$feed = curl_init();
curl_setopt_array($feed, $options);
$json = curl_exec($feed);
curl_close($feed);
echo $json;
$return_data = json_decode($json);

print_r($return_data);