<?php
namespace T\Controller;
use Think\Controller;
use Lib\Image;
class DemoController extends Controller{
    public function index(){    
	
	$img = I('img');
	$w = I('w');
	$h = I('h');
	$arr = array($w,$h);
	
	
	echo Image::getUrl($img,$arr);
	die();
	
	
	
	die();
	Image::getUrl($v->image, array(200),'user'); 
	die();
	$fp2=fopen($filename, 'a');
	fwrite($fp2,$img);
	fclose($fp2);

    }
}
