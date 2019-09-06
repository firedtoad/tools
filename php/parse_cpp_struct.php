<?php
header("Content-Type:text/html;charset=UTF8");
$file_str = file_get_contents("XmlStruct.h");
$dir='G:/work/server/server/sanguo/src/';
$filter=array($dir.'gameserver',$dir.'datacenter',$dir.'Protocol');
function recusive_parse($dir,$filter)
{
    
    $files=glob($dir);
    foreach ($files as $v) {
        if(is_dir($v))
        {
             recusive_parse($v.'/*',$filter);
        }else{
            if(pathinfo($v, PATHINFO_EXTENSION)=='h')
            {
                
                if(in_array(pathinfo($v, PATHINFO_DIRNAME),$filter))
                {
                    if(strstr($v, 'Pub'))
                     parse($v);
                }
            }
           
        }
    }
}
function parse($file)
{
//    echo $file,"\n";
    $f=file_get_contents($file);
    $mc=array();
    preg_match_all('/struct .*?\{.*?\}/s', $f, $mc);
    foreach ($mc[0] as $v) {
        $mc1=array();
         preg_match_all('/[^\{]\s+(\w*?)\s+(.*?[^\d^\)])\;/', $v, $mc1);
        $pstr='';
        $upstr='';
        print_r($mc1);
        foreach ($mc1[1] as $k=>$v)
        {
            switch (strtolower($v))
            {
                case 'uint8':
                    $pstr.='C';
                    $upstr.='C'.$mc1[2][$k].'/';
                break;
                case 'uint16':
                    $pstr.='S';
                    $upstr.='S'.$mc1[2][$k].'/';
                break;
                case 'uint32':
                    $pstr.='I';
                    $upstr.='I'.$mc1[2][$k].'/';
                break;
                case 'uint64':
                    $pstr.='II';
                    $upstr.='II'.$mc1[2][$k].'/';
                break;
                case 'int8':
                    $pstr.='c';
                    $upstr.='c'.$mc1[2][$k].'/';
                break;
                case 'int16':
                    $pstr.='s';
                    $upstr.='s'.$mc1[2][$k].'/';
                break;
                case 'int32':
                    $pstr.='i';
                    $upstr.='i'.$mc1[2][$k].'/';
                break;
                case 'int64':
                    $pstr.='ii';
                    $upstr.='ii'.$mc1[2][$k].'/';
                break;
                case 'char':
                break;
                default:
                break;
            }
        }
        $upstr=substr($upstr,0,-1);
        $upstr=str_replace('m_un','',$upstr);
        $upstr=str_replace('m_u','',$upstr);
        $upstr=str_replace('m_n','',$upstr);
        $upstr=str_replace('m_b','',$upstr);
        echo $pstr,"\n";
        echo $upstr,"\n";
    }
    
}
recusive_parse($dir."*",$filter);
