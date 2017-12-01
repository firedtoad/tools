<?php
include_once 'Enum.php';

$vecMap=array(
// 'Shopitem'=>array('key1'=>'ShopID','key2'=>'ShopItemID'),
// 'HeroExp'=>array('key1'=>'ExpIndex','key2'=>'Level'),
// 'Dropbox'=>array('key1'=>'DropboxID','key2'=>'ItemID'),
// 'TrialReward'=>array('key1'=>'Level','key2'=>'changePoint'),
// 'Growing'=>array('key1'=>'Prob','key2'=>'ID'),
// 'HeroRecycle'=>array('key1'=>'Grade','key2'=>'Quality'),
// 'GuildChapterFight'=>array('key1'=>'FightID','key2'=>'FightSequence'),
// 'BossDrop'=>array('key1'=>'GuildLv','key2'=>'Energy'),
// 'MonsterProperty'=>array('key1'=>'Job','key2'=>'MonsterLv'),
// 'SkillTree'=>array('key1'=>'Sex','key2'=>'PositionID'),
// 'Task'=>array('key1'=>'RoleLevel','key2'=>'TaskID'),
// 'LoginActReward'=>array('key1'=>'RewardType','key2'=>'Days'),
// 'SignReward'=>array('key1'=>'Month','key2'=>'Days'),
// 'Robot'=>array('key1'=>'roleLv','key2'=>'GiftID'),
// 'ShopReset'=>array('key1'=>'ShopID','key2'=>'ResetTime'),
// 'CampThroughItem'=>array('key1'=>'Level','key2'=>'Through'),
// 'DailySumPayReward'=>array('key1'=>'Day','key2'=>'Index'),
// 'Box'=>array('key1'=>'Level','key2'=>'Item'),
// 'Box'=>array('key1'=>'Level','key2'=>'Item'),
// 'DailySumPayReward'=>array('key1'=>'Day','key2'=>'Index'),
// 'CampReward'=>array('key1'=>'RewardID','key2'=>'Rank'),
// 'CampMineProduction'=>array('key1'=>'MineType','key2'=>'PlayerLevel'),
// 'TrialRewardVIP'=>array('key1'=>'Level','key2'=>'changePoint'),
// 'HeroStar'=>array('key1'=>'HeroID','key2'=>'Star'),
// 'HeroStarRecycle'=>array('key1'=>'Star','key2'=>'Quality'),
// 'SpeBox'=>array('key1'=>'ItemBoxID','key2'=>'ID'),

);

header ( "Content-Type:text/html;charset=UTF8" );
$dir = $folder.'*';
$d = glob ( $dir );
$str = '';
$kst = '';
$est = "\n\n";
$tmap='';
$len=50;
$str_len=1;
$vector_len=3;
$def='private:'."\n";
$loaders='';
$call_loaders='';
$load_def='';

$init_declare='';
$init_define='';
$vecmap_typedef='';
$vecmap_define='';
$getvecmap='';
$getvecmap_2param='';
$call_init_define='';


$find_func='';
$getmap='';
$init_templates='';
$reloaders='std::unordered_map<const char*,decltype(&XmlConfig::Dummy)> loaders={'."\n";
foreach ( $d as $f ) {
 $bname = basename ( $f );
 $bname = str_replace ( ".xml", '', $bname );
 $xml = simplexml_load_file ( $f );

 $keys = array_keys ( ( array ) $xml );
// print_r($keys[0]);
// echo $keys[0]===0;
// if($keys[0]===0)continue;
 $et = "extern std::array<KeyType,#>" . " $bname"."Keys;\n";
 $kt = "std::array<KeyType,#>" . " $bname"."Keys={{\n";
 $st = "struct $bname {\n";
 // $l_init_template='template class std::unordered_map<'.'uint32_t,'."$bname>;\n";
 // $init_templates.=$l_init_template;
 $lmap = 'typedef std::vector<'."$bname> C$bname"."Map;\n";
 // $lmap = 'extern template class std::unordered_map<'.'uint32_t,'."$bname>;\n".'typedef std::unordered_map<'.'uint32_t,'."$bname> C$bname"."Map;\n";
 $ldef="    C$bname"."Map $bname"."Map;\n";
 $keys_name=$bname."Keys";
 $map_name=$bname."Map";
 $lloader_def="    bool Load$bname();\n";
 $reloaders.="{\"$bname\",&XmlConfig::Load$bname},\n";
 $llfind="    const $bname* get$bname(uint32_t id)\n    {\n        return GetConfig($bname"."Map,id);\n    }\n";
 if(!isset($vecMap[$bname]))
 {
 $loader="bool XmlConfig::Load$bname()\n{\n    std::string pfile=cpath+\"$bname.xml\";\n    return  LoadCommon(pfile.c_str(),$keys_name,$map_name);\n}\n\n";
 }else{
     $loader="bool XmlConfig::Load$bname()\n{\n    std::string pfile=cpath+\"$bname.xml\";\n    if(LoadCommon(pfile.c_str(),$keys_name,ARRSIZE($keys_name),$map_name)){\n        return Init$bname"."MapMap();\n    }\n    return 1;\n}\n\n";
 }
 
 $lgetmap="    const C$map_name& get$map_name() const
    {
        return $map_name;
    }\n";
if(isset($vecMap[$bname]))
{
    $key1=$vecMap[$bname]['key1'];
    $key2=$vecMap[$bname]['key2'];
    $lvecmap_typedef = 'typedef std::unordered_map<'.'uint32_t,std::unordered_map<uint32_t,'."$bname>> C$bname"."std::vectorMap;\n";
    $lvecmap_define="    C$bname"."std::vectorMap $bname"."MapMap;\n";
    $linit_declare="    bool Init$bname"."MapMap();\n";
    $linit_define="bool XmlConfig::Init$bname"."MapMap()\n{\n    return InitMapMap(offsetof($bname,$key1),offsetof($bname,$key2),$bname"."Map,$bname"."MapMap);    \n}\n";
    $lgetvecmap="    std::unordered_map<uint32_t,$bname>& get$bname"."MapBy$key1(uint32_t id)\n    {\n        return $bname"."MapMap[id];    \n    }\n";
    $lgetvecmap_2param="    const $bname* get$bname"."by$key1"."And$key2(uint32_t $key1,uint32_t $key2)\n    {
        auto &mp=$bname"."MapMap[$key1];
        if(mp.size()>0)
        {
            auto it=mp.find($key2);
            if(it!=mp.end())
            {
                return &it->second;
            }else{
                return nullptr;
            }
        }   
        return nullptr;
    }\n";
    $init_declare.=$linit_declare;
    $init_define.=$linit_define;
    $vecmap_typedef.=$lvecmap_typedef;
    $vecmap_define.=$lvecmap_define;
    $getvecmap.=$lgetvecmap;
    $getvecmap_2param.=$lgetvecmap_2param;
    $lcall_init_define="    Init$bname"."MapMap();\n";
    //$call_init_define.=$lcall_init_define;
} 

 $getmap.=$lgetmap;
 $call_loader="    Load$bname();\n";
 $find_func.=$llfind;
 $call_loaders.=$call_loader;
 $load_def.=$lloader_def;
 $loaders.=$loader;
 $file = file_get_contents ( $f );
 $sp=strpos($file,'<!--');
 $ep=strpos($file,'-->');
 $file=substr($file,$sp, $ep-$sp);
 $file=explode("\n",$file);
// echo $f,"\n";
//// print_r($file);
//print_r($file);
 $ss=preg_split('/ {1,}/',$file[1]);
 $stype=preg_split('/ {1,}/', $file[2]);

if(count($ss)!=count($stype))
{
    print_r($ss);
    print_r($stype);
    echo $f;
    echo "comment don't match types";
//	continue;
}
 $lkey=$keys[0];
 var_dump($lkey);
 // exit();
 foreach ( $xml->$lkey as $kx => $r ) {
  
  $i = 0;
  $j = 0;
  $si=0;
  $m=0;
//     print_r($r->attributes ());
  foreach ( $r->attributes () as $k => $v ) {
   $type = gettype ( $v );

   $j++;
   if(!isset($stype[$m]))
   {
        print_r($r->attributes ());
       print_r($stype);
       print_r($file);
//   	   exit();
   }

   
   switch(strtolower($stype[$m++]))
   {
        
//    case 'in':
    case 'int':
        $ust = "    uint32_t " . $k . ";"; 
        $ust.=str_repeat(' ',$len-strlen($ust));
        
        $st.=$ust."//".$ss[$si++]."\n";
        $kt .= '    {"' . $k . '",0,' . "offsetof($bname,$k)". '},' . "\n";
    break;
    case 'string':
     $ust = "    std::string " . $k . ";";    //".$ss[$si++]."\n";
        $ust.=str_repeat(' ',$len-strlen($ust));
        $st.=$ust."//".$ss[$si++]."\n";
         $kt .= '    {"' . $k . '",1,' . "offsetof($bname,$k)" . '},' . "\n";
    break;
    case 'list:int':
         $ust = "    std::vector<int> " . $k . ";";    //".$ss[$si++]."\n";
         $ust.=str_repeat(' ',$len-strlen($ust));
         $st.=$ust."//".$ss[$si++]."\n";
         $kt .= '    {"' . $k . '",3,' . "offsetof($bname,$k)" . '},' . "\n";
        break;
   	case 'list:string':
         $ust = "    std::vector<std::string> " . $k . ";";    //".$ss[$si++]."\n";
         $ust.=str_repeat(' ',$len-strlen($ust));
         $st.=$ust."//".$ss[$si++]."\n";
         $kt .= '    {"' . $k . '",4,' . "offsetof($bname,$k)" . '},' . "\n";
        break;
   	case 'list:idcount':
         $ust = "    std::vector<IdCount> " . $k . ";";    //".$ss[$si++]."\n";
         $ust.=str_repeat(' ',$len-strlen($ust));
         $st.=$ust."//".$ss[$si++]."\n";
         $kt .= '    {"' . $k . '",5,' . "offsetof($bname,$k)" . '},' . "\n";
        break;
   	case 'float':
         $ust = "    float " . $k . ";";    //".$ss[$si++]."\n";
         $ust.=str_repeat(' ',$len-strlen($ust));
         $st.=$ust."//".$ss[$si++]."\n";
         $kt .= '    {"' . $k . '",6,' . "offsetof($bname,$k)" . '},' . "\n";
        break;
   	case 'list:float':
         $ust = "    std::vector<float> " . $k . ";";    //".$ss[$si++]."\n";
         $ust.=str_repeat(' ',$len-strlen($ust));
         $st.=$ust."//".$ss[$si++]."\n";
         $kt .= '    {"' . $k . '",7,' . "offsetof($bname,$k)" . '},' . "\n";
        break;
        default:
            print_r($ss);
            print_r($stype);
            echo $stype[$m-1];
            break;
   }
   
  }
  $et = str_replace ( "#", $j, $et );
  $kt = str_replace ( "#", $j, $kt );
  break;
 }
 
 $kt = substr ( $kt, 0, strlen ( $kt ) - 2 );
 $st .= "};\n";
 $kt .= "\n}};\n";

 $tmap.=$lmap;
 $def.=$ldef;
 $est .= $et;
 $kst .= $kt;
 $str .= $st;
 
 // break;
}
 $reloaders .= "};\n";
global $hss;
global $mss;
global $msss;
global $esss;
global $vsss;
global $asss;
global $rsss;
global $csss;
global $gsss;
global $tsss;
global $chsss;
global $ctsss;
global $trsss;
global $nasss;
global $cnsss;
global $rksss;
global $lgsss;
global $actsss;
global $cosss;
global $tolss;
global $pushss;
global $GuildBattleChatEnum;
global $specialbase;
global $ltorrybase;
global $TopArenaBaseEnum;
global $syssss;
global $wsss;
global $psss;
$header=<<<EOT
#ifndef __XMLSTRUCT_H__
#define __XMLSTRUCT_H__
#ifndef ARRSIZE
#define ARRSIZE(a) sizeof(a)/sizeof(a[0])
#endif
#include <bits/stringfwd.h>
#include <vector>
#include <array>
#include <cstdint>
#include <unistd.h>
#include <fstream>
#include <iostream>
#include <algorithm>
#pragma pack(push,1)
bool ReloadXmlConfig(const char* file);
class noncopy
{
protected:
    noncopy() {}
  ~noncopy() {}
private:
  noncopy( const noncopy& )=delete;
  noncopy& operator=( const noncopy& )=delete;
};
struct Dummy{
};
struct KeyType
{
    const char* name;
    uint8_t type;
    uint16_t addr;
    bool operator==(const KeyType &e)
    {
       return name==e.name;
    }
};

struct IdCount
{
    uint32_t id;
    uint32_t count;
    bool operator==(const IdCount &e)
    {
       return id==e.id&&count==e.count;
    }
};

template<class T>
void print_struct(T &t,KeyType *k,uint32_t kl)
{
    uint32_t i=0;
    uint8_t *p=(uint8_t*)&t;
    std::vector<int> *pv=NULL;
    std::vector<std::string> *ps=NULL;
    for(;i<kl;++i)
    {
        if(k[i].type==0)
        {
            std::cout<<k[i].name<<"="<<*(uint32_t*)(p+k[i].addr)<<std::endl;
        }else if(k[i].type==1)
        {
            std::cout<<k[i].name<<"="<<((std::string*)(p+k[i].addr))->c_str()<<std::endl;
        }else if(k[i].type==3)
        {
            std::cout<<"std::vector<int>:"<<k[i].name<<"="<<std::endl;
            pv=(std::vector<int>*)(p+k[i].addr);
            if(pv)
            {
                for(uint32_t ii: *pv)
                {
                     std::cout<<ii<<std::endl;
                }
            }

        }else if(k[i].type==4)
        {
            std::cout<<"std::vector<std::string>:"<<k[i].name<<"="<<std::endl;
            ps=(std::vector<std::string>*)(p+k[i].addr);
            if(ps)
            {
                for(std::string &s: *ps)
                {
                      std::cout<<s<<std::endl;
                }
            }

        }

    }
}

EOT;

$header_cpp=<<<EOT

#include <cstring>
#include<stddef.h>
#include <unordered_map>
#include <boost/uuid/random_generator.hpp>
#include"XmlConfig.h"
#include"rapidxml.hpp"
#include"rapidxml_utils.hpp"
#include"rapidxml_print.hpp"

EOT;
$mid=<<<EOT
    std::string cpath;
    static XmlConfig* instance;
   
    XmlConfig();
    XmlConfig(const char*  path);
public:
    bool Dummy(){return true;} 
    void setPath(const char* path)
    {
        cpath = path;
        if(access(path,0)==1)
        {
            std::cout<<"config dir not exists"<<std::endl;
            exit(1);
        }
    }
    
    void writeConfig(const char* file,const char* content)
    {
        std::string pfile=cpath+file;
        if(access(pfile.c_str(),0)==1)
        {
            std::cout<<"config file not exists"<<std::endl;
            return;
        }
        std::fstream fs(pfile.c_str(),std::ios_base::trunc|std::ios_base::out);
        fs<<content;
        fs.close();
    }
    
    static XmlConfig& Instance();
    bool LoadFile (const char*file);
    bool
    LoadAll();
    uint64_t UUID();
    template<class T>
    bool InitMapMap(uint32_t koffset1,uint32_t koffset2,std::vector<T> &smap,std::vector<std::vector<T>> &vecMap);
    template<class T,size_t N>
    bool
    LoadCommon(const char *file, std::array<KeyType,N> &keys,std::vector<T> &t1);
    template<typename T>
    const T* GetConfig(std::vector<T> &vec,uint32_t id)
    {
        if(vec.size()>id&&id>0)
        {
            return &vec[id-1];
        }
        const T * ret=nullptr;
        if(std::binary_search(vec.begin(),vec.end(),T{id},
        [&ret](const T& t1,const T& t2){
            if(*(uint32_t*)&t1==*(uint32_t*)&t2)
            {
                ret=&t1;
                return true;
            }
            return false;
        })){
           return ret;
        }
        return nullptr;
    }

EOT;

$pop="#pragma pack(pop)\n";

$cpp_mid=<<<EOT
template<typename T>
bool split(std::vector<T> &v,const char *val,const char * delim=",")
{
    if(val==nullptr)
    {
        return false;
    }
    char *dval=strdup(val);
    char *orgin=dval;
    char *t=nullptr;
    do{
        t=strsep(&dval,delim);
        if(t)
        {
            v.emplace_back(t);
        }
    }while(t);
    free(orgin);
    return true;
}

bool is_null(const std::string &s)
{
    std::string ls(s);
    std::transform(s.begin(),s.end(),ls.begin(),::tolower);
    return ls=="null"||ls=="0";
}

XmlConfig::XmlConfig ()
{
   // FileKeys["Buff"]=BuffMap;
}

bool XmlConfig::LoadAll()
{
#LOADERS
    return true;
}

XmlConfig& XmlConfig::Instance()
{
    static XmlConfig config;
    return config;
}

XmlConfig::XmlConfig (const char * path)
{
   cpath=path;
   if(access(path,0)==1)
   {
       std::cout<<"config dir not exists"<<std::endl;
       exit(1);
   }
}
bool
XmlConfig::LoadFile (const char *file)
{
  std::string pfile=cpath+"HeroProperty.xml";
  //return  LoadCommon(pfile.c_str(),HeroPropertyKeys,ARRSIZE(HeroPropertyKeys),HeroPropertyMap);
  return true;
}

static uint64_t
transfer (const char* src)
{

#ifdef OLD_VER_TRANS
  uint64_t ret = 0;
  int len = strlen (src);
  for (int i = 0; i < len; i += 2)
    {
      int t = 0;
      if (src[i] >= '0' && src[i] <= '9' && src[i + 1] >= '0'
          && src[i + 1] <= '9')
        {
          t = (src[i] - '0') * 16 + (src[i + 1] - '0');
        }
      else if (src[i] >= 'a' && src[i] <= 'f' && src[i + 1] >= 'a'
          && src[i + 1] <= 'f')
        {
          t = (src[i] - 'a') * 16 + (src[i + 1] - 'a');
        }

      ret = ret * 10 + t;
    }
  return ret;
#else
  uint64_t ret = 0;
  int len = strlen (src);
  for (int i = len/2; i < len; i += 2)
    {
      int t1 = 0, t2 = 0;
      if(src[i] >= '0' && src[i] <= '9')
          t1 = src[i] - '0';
      else if(src[i] >= 'a' && src[i] <= 'f')
          t1 = src[i] - 'a';
      else if(src[i] >= 'A' && src[i] <= 'F')
          t1 = src[i] - 'A';

      if(src[i+1] >= '0' && src[i+1] <= '9')
          t2 = src[i+1] - '0';
      else if(src[i+1] >= 'a' && src[i+1] <= 'f')
          t2 = src[i+1] - 'a';
      else if(src[i+1] >= 'A' && src[i+1] <= 'F')
          t2 = src[i+1] - 'A';
      ret = ret * 100 + t1 * 16 + t2;
    }
  return ret;


#endif

}
uint64_t
XmlConfig::UUID ()
{
    static boost::uuids::random_generator gen;
    boost::uuids::uuid uuid_t(gen());
    std::string LCardUUID;
    // LCardUUID.reserve(2*uuid_t.size());

    char temp[4] = {0};
    for (size_t i = 0; i < uuid_t.size(); i++)
    {
        sprintf (temp, "%02X", uuid_t.data[i]);
        LCardUUID.append(temp);
    }

    uint64_t value = transfer (LCardUUID.c_str());
    return value;
}

template<class T,size_t N>
bool
XmlConfig::LoadCommon (const char* file, std::array<KeyType,N> &keys,std::vector<T> &t1)
{


    rapidxml::xml_document<char> doc;
    rapidxml::file<char> f(file);
    doc.parse<0>(f.data());
    t1.clear();
    auto ti=doc.first_node();
    if (ti)
    {
        auto p = ti->first_node();
        int row = 0;
        while (p)
        {
            T t;
            uint8_t *ui = (uint8_t*) &t;
            std::string *s = nullptr;
            std::vector<float> *vf=nullptr;
            std::vector<int> *vi=nullptr;
            std::vector<std::string> *vs=nullptr;
            std::vector<IdCount> *vid=nullptr;
            for (const auto &key:keys)
            {
                auto f=p->first_attribute(key.name);
                if(!f)continue;
                const char *val = f->value();
                if (val)
                {
                    std::string sval(val);
                    std::vector<std::string> lv;
                    std::vector<int32_t> lvi;
                    switch (key.type)
                    {
                        case 0:
                            *(uint32_t*) (ui + key.addr ) = atoi(val);
                            break;
                        case 1:
                            s = (std::string*) (ui + key.addr );
                            if (s)
                            {
                                s->assign(val);
                            }
                            break;
                        case 3:
                        {
                            if(is_null(sval))
                            {
                                continue;
                            }
                            vi = (std::vector<int>*) (ui + key.addr );
                            if (vi)
                            {
                                std::vector<std::string> vecs;
                                if(!split(vecs,&sval[0]))
                                {
                                    std::cout << "wrong config xml format file=" << file
                                    << " key=" << key.name << "  row=" << row
                                    << std::endl;
                                }else{
                                    for(const auto &v:vecs)
                                    {
                                        vi->emplace_back(std::stoi(v));
                                    }
                                }
                            }
                        }
                            break;
                        case 4:
                            if(is_null(sval))
                            {
                                continue;
                            }
                            vs = (std::vector<std::string>*) (ui + key.addr );
                            if (vi)
                            {
                                if(!split(*vs,&sval[0]))
                                {
                                    std::cout << "wrong config xml format file=" << file
                                    << " key=" << key.name << "  row=" << row
                                    << std::endl;
                                }
                            }
                            break;
                        case 5:
                            if(is_null(sval))
                            {
                                continue;
                            }
                            vid = (std::vector<IdCount>*) (ui + key.addr );
                            if (vid)
                            {
                                std::vector<std::string> vecs;
                                if(!split(vecs,&sval[0]))
                                {
                                    std::cout << "wrong config xml format file=" << file
                                    << " key=" << key.name << "  row=" << row
                                    << std::endl;
                                    break;
                                }
                                for(auto &res:vecs)
                                {
                                    std::vector<std::string> tvec;
                                    if(split(tvec,&res[0],":"))
                                    {
                                        if(tvec.size()==2)
                                        {
//                                            IdCount idc={(uint32_t)std::stoul(tvec[0]),(uint32_t)std::stoul(tvec[1])};
                                            vid->emplace_back(IdCount{(uint32_t)std::stoul(tvec[0]),(uint32_t)std::stoul(tvec[1])});
                                        }
                                    }
                                }
                            }
                            break;
                        case 6:
                            *(float*) (ui + key.addr)=stof(sval);
                            break;
                        case 7:
                            if(is_null(sval))
                            {
                                continue;
                            }
                            vf = (std::vector<float>*) (ui + key.addr );
                            if (vi)
                            {
                                   std::vector<std::string> vecs;
                                   if(!split(vecs,&sval[0]))
                                   {
                                       std::cout << "wrong config xml format file=" << file
                                       << " key=" << key.name << "  row=" << row
                                       << std::endl;
                                   }else{
                                       for(const auto &v:vecs)
                                       {
                                           vf->emplace_back(std::stof(v));
                                       }
                                   }
                           }
                           break;
                        default:
                            break;
                    }
                }
                else
                {
//                    std::cout<<keys[i-1].name<<std::endl;
//                    std::cout<<keys[i].addr<<std::endl;
//                    std::cout<<i<<std::endl;
//                    std::cout<<keylen<<std::endl;
//                    std::cout<<keys[i].type<<std::endl;

                    std::cout << "wrong config xml format file=" << file
                            << " key=" << key.name << "  row=" << row
                            << std::endl;
                }
            }

            t1.emplace_back(t);
            p = p->next_sibling();
            row++;
        }
    }
    return true;
}
template<class T>
bool XmlConfig::InitMapMap(uint32_t koffset1,uint32_t koffset2, std::vector<T>& smap,
        std::vector<std::vector<T> >& vecMap)
{

    uint8_t *p=nullptr;
    vecMap.clear();
    for(auto &m:smap)
    {
        p=(uint8_t*)&m.second;
        uint32_t key1=*(uint32_t*)(p+koffset1);
        uint32_t key2=*(uint32_t*)(p+koffset2);
        auto &vm=vecMap[key1];
        vm[key2]=m.second;
    }
    return true;
}

$reloaders

bool ReloadXmlConfig(const char* file)
{
    auto loader=loaders.find(file);
    if(loader!=loaders.end())
    {
         (XmlConfig::Instance().*loader->second)();
        return true;
    }
    return false;
}

EOT;
echo "<pre>";
$class_def="class XmlConfig :public noncopy
{\n";

echo $str;
echo $kst;
echo $est;
echo $tmap;
echo $vecmap_typedef;
echo $def;
echo $vecmap_define;
echo $loaders;
echo $getvecmap;
echo $call_loaders;
echo $load_def;
echo $init_declare;
echo $find_func;
echo $init_define;
echo $reloaders;





$headstr=$header.$str;

$cpp_mid=str_replace('#LOADERS', $call_loaders.$call_init_define, $cpp_mid);
file_put_contents("XmlConfig.h", $headstr.$pop.$est.$tmap.$vecmap_typedef.$class_def.$def.$vecmap_define.$mid.$load_def.$init_declare.$getmap.$getvecmap.$getvecmap_2param.$find_func."\n};\n#endif");
file_put_contents("XmlConfig.cpp", $header_cpp.$kst.$loaders.$init_define.$cpp_mid);

file_put_contents('templates.cpp',$init_templates);
echo "</pre>";
