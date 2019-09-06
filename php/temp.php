<?php
$str=<<<T
#ifndef T_H_
#define T_H_
#include<EASTL/vector_map.h>

T;
$call='';
if(!file_exists('te'))
{
    mkdir('te',777,true);
}
foreach (range('A','Z') as $k=>$v)
{
    foreach (range('a','a') as $ka=>$va)
    {
        $class=$v.$va;
        $str.=<<<ET
template<typename T>
struct $class
{
    T t;
};
void test$class();

ET;
        $src=<<<E
#include "T.h"
void test$class()
{
    eastl::vector_map<int,Aa<int>> ma;
    eastl::vector_map<int,Ba<int>> mb;
    eastl::vector_map<int,Ca<int>> mc;
    eastl::vector_map<int,Da<int>> md;
    eastl::vector_map<int,Ea<int>> me;
    eastl::vector_map<int,Fa<int>> mf;
    eastl::vector_map<int,Ha<int>> mh;
    eastl::vector_map<int,Ia<int>> mi;
    eastl::vector_map<int,Ja<int>> mj;
    eastl::vector_map<int,Ka<int>> mk;
    eastl::vector_map<int,La<int>> ml;
    eastl::vector_map<int,Ma<int>> mm;
    eastl::vector_map<int,Na<int>> mn;
    eastl::vector_map<int,Oa<int>> mo;
    eastl::vector_map<int,Pa<int>> mp;
    ma[1]=Aa<int>();
    mb[1]=Ba<int>();
    mc[1]=Ca<int>();
    md[1]=Da<int>();
    me[1]=Ea<int>();
    mf[1]=Fa<int>();
    mh[1]=Ha<int>();
    mi[1]=Ia<int>();
    mj[1]=Ja<int>();
    mk[1]=Ka<int>();
    ml[1]=La<int>();
    mm[1]=Ma<int>();
    mn[1]=Na<int>();
    mo[1]=Oa<int>();
    mp[1]=Pa<int>();
}
E;
        
    $fstr=$class.'.cxx';
    file_put_contents('te/'.$fstr, $src);
    $call.=
<<<E
test$class();

E;
    }
}
$str.='#endif ';

file_put_contents('te/T.h', $str);
echo $call;

