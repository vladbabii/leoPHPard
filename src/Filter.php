<?php
namespace Leophpard;

class Filter {

    public function filter($url,&$search){

        $this->filterStart($url,$search);
        if(count($search)==0){ return ; }

        $this->filterEnd($url,$search);
        if(count($search)==0){ return ; }

        $this->filterSlashes($url,$search);
        if(count($search)==0){ return ; }

        $this->filterMiddle($url,$search);
        if(count($search)==0){ return ; }

    }

    protected function filterStart($url,&$search){
        $index=0;
        $done=false;
        $maxIndex=strlen($url);
        while(!$done){
            $url_char=substr($url,$index,1);
            if(count($search)==1){
                return ;
            }
            foreach($search as $i=>&$path){
                $p_char=substr($path,$index,1);
                if($p_char=='{'){
                    $done=true;
                }
                if($p_char!=='{' && $p_char !== $url_char){
                    unset($search[$i]);
                }
            }
            $index++;
            if($index>=$maxIndex){
                return ;
            }
        }
    }

    protected function filterEnd($url,&$search){
        $index=1;
        $done=false;
        $maxIndex=strlen($url);
        while(!$done){
            $url_char=substr($url,(-1)*$index,1);
            //echo 'Pos -'.$index.' char: '.$url_char.PHP_EOL;
            if(count($search)==1){
                return ;
            }
            foreach($search as $i=>&$path){
                $p_char=substr($path,(-1)*$index,1);
                if($p_char=='}'){
                    $done=true;
                }
                if($p_char!=='}' && $p_char !== $url_char){
                    unset($search[$i]);
                }
            }
            $index++;
            if($index>=$maxIndex){
                return ;
            }
        }
    }

    protected function filterSlashes($url,&$search){
        $url_count = substr_count($url,'/');
        foreach($search as $i=>&$path){
            if($url_count != substr_count($path,'/')){
                unset($search[$i]);
            }
        }
    }

    protected function filterMiddle($url,&$search){
        $pieces=explode('/',$url);
        $pCount=count($pieces)-1;
        $posCache=array();
        $posNext=array();
        foreach($pieces as $pIndex=>$pString){
            if($pIndex>0 && $pIndex<$pCount){
                foreach($search as $i=>&$path){
                    if(!isset($posCache[$i])) {
                        $posCache[$i] = strpos($path, '/');
                        $posCache[$i] = strpos($path,'/',$posCache[$i]);
                    }else{
                        $posCache[$i] = $posNext[$i];
                    }
                    $posNext[$i] = stripos($path,'/',$posCache[$i]+1);
                    if(
                        substr($path,$posCache[$i]+1,1)!='{'
                        && strcmp($pString,substr($path,$posCache[$i]+1,$posNext[$i]-$posCache[$i]-1))!==0
                    ) {
                        unset($search[$i]);
                    }
                }
            }
        }
    }
}
