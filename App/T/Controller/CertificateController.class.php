<?php
namespace T\Controller;
use Think\Controller;
use Lib\Image\UploadedFile;
use Lib\UserSession;
class CertificateController extends Controller{
    //定义第一行的宽度和高度
    private $oneWidth =  170;
    private $oneHeight = 10;
    //定义第二，三，四行的宽度
    private $otherWidth = 100;
    private $lineHeight = 55;
    //定义每行的行高
    //定义字的大小
    
    /**
     *  获取捐款证书
     * @param string $name 捐款人姓名
     * @param string $orgname 发起组织姓名
     * @param string $concurname 发起的求助名称
     * @param int $time 捐款的时间 
     * @param float $mon 捐款的数额
     */
    public function index($name='刘志敏',$orgname='中青公益',$concurname='求助儿童教育基金十万',$time='1300000000',$mon='50',$number='11111111111110'){
        $nw = $this->get_name_position($name);
        //计算姓名的位置
        $namewidth =  $nw[0];
        $nameheight = $nw[1];  
        $year = date("Y",$time);
        $month = date('m',$time);
        $day = date('d',$time);
        
        //获取第一行的内容
        $oneContent = '感谢您于'.$year.'年'.$month.'月'.$day.'日，为';
        $leng = mb_strlen($orgname,'utf-8'); 
        $twoContent = '';
        if($leng < 9){
            $oneContent .= '"'.$orgname.'"的"';
            $num = 8-$leng;
            if($num>0){
                $oneContent .= mb_substr($concurname,0,$num,'utf-8');
            }
            $twoContent .= mb_substr($concurname,$num,20,'utf-8').'"捐款'.$mon.'元';
        }elseif($leng==9){
            $oneContent .= '"'.$orgname.'"';
            $twoContent .= '的"'.$concurname.'"捐款'.$mon.'元';
        }else{
            $oneContent .= '"'.mb_substr($orgname,0,9,'utf-8').'"';
            $twoContent .= '的"'.$concurname.'"捐款'.$mon.'元';
        }
        
        
        
        //获取第二行的内容
        $threeContent = "涓滴之劳，益暖人心。";
        //定义第三行的位置
        $threeWidth =  100;
        $threeHeight = 120;
        
        //定义编号的位置
        $codewidth = 915;
        $codeheight = -343;
        
        //定义年月日的高度
        $dateheight = 293; 
        //定义年月日的长度
        $ywidth = 820;
        $mwidth = 925;
        $dwidth = 990;
        
        //定义编号的位置
        $codewidth = 920;
        $codeheight = -343;
        
        
        
        $image_water = \Think\Image::IMAGE_WATER_WEST;
        $image = new \Think\Image();
        $imgname = IMAGE_SERVER_PATH . '/zsTemplate/fwzs.png';
        $newname = IMAGE_SERVER_PATH . '/zsTemplate/zqgyzs'.time().'.png';
        $font = '../../../think_zhiyuanjia/App/Font/STHeiti.ttf';
        $color = '#7b8289';
        $image = $image->open($imgname)->text($name, $font, 50, $color, $image_water, array($namewidth, $nameheight));
        $image = $image->text($oneContent, $font, 31, $color, $image_water, array($this->oneWidth, $this->oneHeight));
        $image = $image->text($twoContent, $font, 31, $color, $image_water, array($this->otherWidth, 10+$this->lineHeight));
        $image = $image->text($threeContent, $font, 31, $color, $image_water, array($this->otherWidth, 65+$this->lineHeight));
        $image = $image->text($number, $font, 21, $color, $image_water, array($codewidth, $codeheight));
        $y = date("Y",time());
        $m = date("m",time());
        $d = date("d",time());
        $image = $image->text($y, $font, 23, $color, $image_water, array($ywidth, $dateheight));
        $image = $image->text($m, $font, 23, $color, $image_water, array($mwidth, $dateheight));
        $image = $image->text($d, $font, 23, $color, $image_water, array($dwidth, $dateheight));
      
        $image->save($newname);
        $img = file_get_contents($newname, true);
        header("Content-Type: image/jpeg;text/html; charset=utf-8");
        echo $img;
        
        
        
    }
    
    //获取姓名的位置
    private function get_name_position($name){
        $nameline = mb_strlen($name, 'UTF8');
        if ($nameline == 2) {
            $namewidth = 540;
            $nameheight = -75;
        } elseif ($nameline == 3) {
            $namewidth = 490;
            $nameheight = -75;
        } else {
            $namewidth = 435;
            $nameheight = -75;
        }
        return array($namewidth,$nameheight);
    }
    
}