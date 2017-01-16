<?php
namespace Leophpard;

class Filter {

    protected $charStartRegex = '{';
    protected $charEndRegex = '}';
    protected $charSeparator = '/';
    protected $order = array('Start','End','Slashes','Middle');
    protected $state = array();

    public function filter($url,&$search){
        $this->clearState();
        $this->state['done']=array();
        foreach($this->order as $index=>$name){
            $function='filter'.$name;
            $this->$function($url,$search);
            $this->state['done'][]=$name;
            if(count($search)==0){ return ; }
        }
    }

    public function setOrder($order=array()){
        $accepted=array('Start','End','Slashes','Middle');
        if(is_null($order)){
            $this->order=$accepted;
            return ;
        }
        if(!is_array($order) || count($order)==0){
            throw new \Exception('Order need an array as parameter and at least one element in it');
        }
        $validOrder=array();
        foreach($order as $index=>$name){
            if(in_array($name,$accepted)){
                $validOrder[]=$name;
            }else{
                throw  new \Exception('Function "'.$name.'" is not a valid option; please use: '.implode(', '.$accepted));
            }
        }
        $this->order=$validOrder;
    }

    /**
     * Set character for start of regex, character for end of regex, and url separator
     * If you set one of the values to null, the current setting will not be changed
     *
     * @param string $start
     * @param string $end
     * @param string $separator
     * @throws \Exception
     */
    public function configure($start='{',$end='}',$separator='/'){
        if(is_null($start)){
            $start=$this->charStartRegex;
        }
        if(is_null($end)){
            $end=$this->charEndRegex;
        }
        if(is_null($separator)){
            $separator=$this->charSeparator;
        }
        if(
            !is_string($separator)
            || !is_string($start)
            || !is_string($end)
            || strlen($separator) != 1
            || strlen($start) != 1
            || strlen($end) != 1
        ){
            throw new \Exception(__CLASS__.' '.__FUNCTION__.' method only accepts strings with lenght=1 as parameters');
        }
        $this->charSeparator = $separator;
        $this->charStartRegex = $start;
        $this->charEndRegex = $end;
    }

    protected function clearState(){
        $this->state=array();
    }

    protected function stepDone($name=''){
        if(!is_array($this->state['done']) || count($this->state['done'])==0){
            return false;
        }
        return in_array($name,$this->state['done']);
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
                if($p_char==$this->charStartRegex){
                    $done=true;
                }
                if($p_char!==$this->charStartRegex && $p_char !== $url_char){
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
            if(count($search)==1){
                return ;
            }
            foreach($search as $i=>&$path){
                $p_char=substr($path,(-1)*$index,1);
                if($p_char==$this->charEndRegex){
                    $done=true;
                }
                if($p_char!==$this->charEndRegex && $p_char !== $url_char){
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
        $url_count = substr_count($url,$this->charSeparator);
        foreach($search as $i=>&$path){
            if($url_count != substr_count($path,'/')){
                unset($search[$i]);
            }
        }
    }

    protected function filterMiddle($url,&$search){
        $pieces=explode('/',$url);

        $pStart = 1;
        $pEnd=count($pieces)-1;

        if(!$this->stepDone('Start')){
            $pStart--;
        }
        if(!$this->stepDone('End')){
            $pEnd++;
        }

        $posCache=array();
        $posNext=array();
        foreach($pieces as $pIndex=>$pString){
            if($pIndex>=$pStart && $pIndex<$pEnd){
                foreach($search as $i=>&$path){
                    if(!isset($posCache[$i])) {
                        $posCache[$i] = strpos($path, $this->charSeparator);
                        $posCache[$i] = strpos($path, $this->charSeparator, $posCache[$i]);
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
