<?php
namespace T\Controller;
use Think\Controller;
use Lib\Image;
class ImagechangeController extends Controller{
    public function index(){    
    
	return;

	  
	$filename = "/Library/Server/Web/Data/Sites/yijuanupload/demo/aaa.png";
	ob_start();
	readfile("http://fs.719kj.com/project/571f229b7d4d6.png");
	$img = ob_get_contents();
	ob_end_clean();
	$size = strlen($img);
	$fp2=fopen($filename, 'a');
	fwrite($fp2,$img);
	fclose($fp2);
	
	


    }
    
    
    
    public function checkImage(){
    	
    
    
    
    }
}
