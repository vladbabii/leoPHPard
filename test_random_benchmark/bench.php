<?php
require_once '../src/autoload.php';

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

$datax=file_get_contents('random_data.txt');
$datax=str_replace("\r\n","",$datax);
$datax=str_replace("\n","",$datax);
$datal=strlen($datax);
$datap=0;

function getRandomWord($len = 10) {
    global $datax;
    global $datap;
    global $datal;
    if($datap+$len>$datal){
        $datap=rand(0,$datal/2);
    }
    $datap+=$len;
    return substr($datax,$datap-$len,$len);
}

$routes = array();

for($i=1;$i<=500;$i++){
    $pieces=array();
    $maxLen=rand(4,7);
    for($j=0;$j<$maxLen;$j++){
        $x=rand(0,100);
        if($j>1 && $j<$maxLen-1 && $x<=50) {
            $pieces[] = '{[a-z]{1,12}\\i}';
        }else{
            $pieces[] = getRandomWord(rand(4, 10));

        }
    }
    $routes[]=implode('/',$pieces);
}

for($i=1;$i<=500;$i++){
    $pieces=array();
    $maxLen=rand(4,7);
    for($j=0;$j<$maxLen;$j++){
        $x=rand(0,100);
        if($j>1 && $j<$maxLen-1 && $x<=50) {
            $pieces[] = getRandomWord(rand(4, 10));
        }else{
            $pieces[] = '{[a-z]{1,12}}';

        }
    }
    $routes[]=implode('/',$pieces);
}

$search=$routes;
$url=$routes[$i-1];

echo 'Matching last URL: '.$url.PHP_EOL;
$url=explode('/',$url);
foreach($url as $k=>$v){
    if(substr($v,0,1)=='{'){
        $url[$k]=getRandomWord(rand(4,10));
    }
}
$url=implode('/',$url);
echo 'Search url with value: '.$url.PHP_EOL;
echo 'List size: '.count($search).PHP_EOL;

$LF = new \Leophpard\Filter;

$time_0 = microtime_float();
$LF->filter($url,$search);
$time_1 = microtime_float();
echo 'Remaining routes: '.count($search).PHP_EOL;
echo 'Total runtime (ms): '.(float)(($time_1-$time_0)*1000).PHP_EOL;
