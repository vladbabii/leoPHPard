<?php
use PHPUnit\Framework\TestCase;


class AnalyzerAdvancedTestCase extends TestCase
{

    public function testCreation()
    {
        $LF = new Leophpard\Filter;
        $search = array();
        $LF->filter('',$search);
    }

    public function testOnlyTextMatch(){
        $LF = new Leophpard\Filter;
        $url= 'a/dac/d';
        $search = array(
             'b/d/noc'
            ,'some/other/url'
            ,'a/dac/d'
        );
        $LF->filter($url,$search);
        $this->assertEquals(1,count($search));
        $this->assertEquals('a/dac/d',$search[2]);
    }

    public function testSimpleWithRegex()
    {
        $url='a/b/c';
        $search=array(
             'one'  => 'z/c/v'
            ,325    => 'a/b/{something}'
            ,'bla'  => 'a/{somethinc}/d'
        );
        $LF = new Leophpard\Filter;
        $LF->filter($url,$search);

        $this->assertEquals(1,count($search));
        $this->assertEquals(true,isset($search[325]));
        $this->assertEquals('a/b/{something}',$search[325]);
    }

    public function testCustomRegex(){
        $url='a/b/c';
        $search=array(
            'one'  => 'z/c/v'
            ,325    => 'a/b/#something#'
            ,'bla'  => 'a/#somethinc#/d'
        );
        $LF = new Leophpard\Filter;
        $LF->configure('#','#',null);
        $LF->filter($url,$search);

        $this->assertEquals(1,count($search));
        $this->assertEquals(true,isset($search[325]));
        $this->assertEquals('a/b/#something#',$search[325]);
    }

}
