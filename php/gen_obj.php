<?php
$root='G:/dev/abseil-cpp/build/absl/';
$root='G:/msys64/usr/local/lib/';

function proccess_obj($file)
{
    $ext=pathinfo($file,PATHINFO_EXTENSION);
    if($ext!='obj')
    {
        return;
    }
    `cp $file lib/`;
}

function proccess($dir)
{
    $pattern=$dir.'/*';
    $files=glob($pattern);
    foreach ($files as $k=>$v)
    {
        if(is_dir($v))
        {
            proccess($v);
        }else{
            proccess_obj($v);
        }
    }
}
function ar($dir)
{
    $pt=$dir.'/libabsl*.a';
    $str=
'create libabsl_all.a
#R
save
end';
    $add=[];
    $files=glob($pt);
    
    foreach ($files as $k=>$v)
    {
        $name=basename($v);
        $add[]="addlib ".$name;
    }
    $str=str_replace('#R', join("\n", $add), $str);
    echo $str;
}
// proccess($root);
ar($root);