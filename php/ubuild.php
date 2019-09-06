<?php
$dir='G:/work/server/server/Server/GameServer/src';
$exts=['cc','cpp','c','cxx'];
function  process_dir($dir)
{
   global  $exts;
   $pt=$dir.'/*';
   $files=glob($pt);
   $arr=[];
   foreach ($files as $k=>$v)
   {
       if(is_dir($v))
       {
           process_dir($v);
       }else{
          
           $ext=pathinfo($v,PATHINFO_EXTENSION);
           if(in_array($ext, $exts))
           {
              $bname=basename($v);
              if($bname!='Unity.cc')
              {
                  $arr[]=$bname;
              }
           }
       }
   }
   if(count($arr))
   {
       $ufile=$dir.'/Unity.cc';
       echo $ufile,"\n";
       array_walk($arr, function(&$v){
           $v='#include "'.$v.'"';
       });
//        echo join("\n", $arr),"\n";
       unlink($ufile);
       file_put_contents($ufile, join("\n", $arr));
   }
} 
process_dir($dir);