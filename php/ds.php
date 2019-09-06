<?php
$dir='D:/work/confignew/xml/';
$dir='G:/work/server/config/xml/';

// DT_INT8=1,
// DT_INT16=2,
// DT_INT32=3,
// DT_INT64=4,
// DT_INT=5,
// DT_UINT8=6,
// DT_UINT16=7,
// DT_UINT32=8,
// DT_UINT64=9,
// DT_STRING=10,
function gettypeof($type)
{
    $arr=[
        'INT8'=>'DT_INT8',
        'INT16'=>'DT_INT16',
        'INT32'=>'DT_INT32',
        'INT'=>'DT_INT',
        'int'=>'DT_INT',
        'UINT8'=>'DT_UINT8',
        'UINT16'=>'DT_UINT16',
        'UINT32'=>'DT_UINT32',
        'UINT64'=>'DT_UINT64',
        'std::string'=>'DT_STRING',
        'string'=>'DT_STRING',
    ];
    if(isset($arr[$type]))
    {
        return $arr[$type];
    }
    echo $type;
}

function p($file)
{
    $str=file_get_contents($file);
    $str=str_replace('\r', '', $str);
    $mc=[];
    if(preg_match_all('/\s+struct.*?\};/s', $str,$mc))
    {
        if(isset($mc[0][0]))
        {
            $s=$mc[0][0];
            $s=preg_replace('/\t/', '    ', $s);
            $s=preg_replace('/\[\d+\]/', '', $s);
            $s=str_replace(' string ', 'std::string ', $s);
            $s=str_replace('char ', 'std::string ', $s);
            $s=preg_replace('/^ {4}/sm', '', $s);
            $def='';
            if(preg_match_all('/^ {0,}([\w:]+).*?(\w+)/sm', $s,$mc))
            {
                if(count($mc[2]))
                {
                    $count=count($mc[2]);
                    $name=$mc[2][0];
                    $short=str_replace('Struct', '', $name);
                    
                    $def.='std::array<Type,'.($count-1).'> type'.$short."={{\n";
                    $defs=[];
                    for($i=1;$i<$count;++$i)
                    {
                        $defs[]=sprintf('    Type{%s,"%s",offsetof(%s,%s)}',gettypeof($mc[1][$i]),$mc[2][$i],$name,$mc[2][$i]);
                    }
                    $def.=join(",\n",$defs);
                    $def.="\n}};\n";
                    $s.="\nextern std::array<Type,".($count-1).'> type'.$short.";";
                }
            }
            return [$s,$def,$count];
        }   
    }
    return ['','',0];
//     exit();
}
function main($dir)
{
    $pt=$dir.'/*';
    $files=glob($pt);
$dh=
'#ifndef DS_H_
#define DS_H_
#include<cstdint>
#include<string>
#include<array>
using INT8=int8_t;
using INT16=int16_t;
using INT32=int32_t;
using INT64=int64_t;
using INT=int32_t;
using UINT8=uint8_t;
using UINT16=uint16_t;
using UINT32=uint32_t;
using UINT64=uint64_t;
';
$defs='#include "DS.h"
#include "Config.h"
';

    $ds=
'
void init()
{
';
    foreach ($files as $k=>$v) 
    {
        $bname=basename($v,'.h');
        list($s,$def,$count)=p($v);
        $dh.=$s;
        $defs.=$def; 
        $ds.=sprintf('    Config<%sStruct,%d> cfg%s("conf/%s.xml",type%s);'."\n",$bname,$count-1,$bname,$bname,$bname);
    }
    
    $dh.=
'
void init();
#endif
';
    $ds.=
'
}
';
//     echo $defs;
//     echo $ds;
    file_put_contents('DS.h', $dh);
    file_put_contents('DS.cpp', $defs.$ds);
}
main($dir);