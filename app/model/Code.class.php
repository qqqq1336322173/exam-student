<?php
	class SecCreateCode{
		function showImage(){	
			//定义要创建图片的类型为png
			header ('Content-Type: image/png');
			//创建一个真彩色图像
			$image=imagecreatetruecolor(100, 30);
			//定义图片的颜色
			$color=imagecolorallocate($image, 255, 255, 255);
			//填充画布颜色
			imagefill($image, 20, 20, $color);
			$code='';
			//生成随机4个数
			 for($i=0;$i<4;$i++){
			     $fontSize=8;
			     $x=rand(5,10)+$i*100/4;//生成横坐标位置，防止横向不重叠
			     $y=rand(5, 15);
			     $data='abcdefghijklmnopqrstuvwxyz123456789';//定义字符串
			     $string=substr($data,rand(0, strlen($data)),1);//使用substr随机截取一个字符
			    $code.=$string;//将截取出来的字符拼接成字符串
			     $color=imagecolorallocate($image,rand(0,120), rand(0,120), rand(0,120));//产生一个随机色
			     imagestring($image, $fontSize, $x, $y, $string, $color);//将字符放到画布上
			 }
			
			 $_SESSION['code']=$code;//将随机产生的字符串存储在session里
			setcookie(session_name(),session_id(),time()+3600,"/");//设置session的过期时间和路径
			//生成200个点
			 for($i=0;$i<200;$i++){
			        $pointColor=imagecolorallocate($image, rand(100, 255), rand(100, 255), rand(100, 255));//生成一个随机色，作为点的颜色
			        imagesetpixel($image, rand(0, 100), rand(0, 30), $pointColor);//将点放到画布
			 }
			 //生成横线
			 for($i=0;$i<2;$i++){
			        $linePoint=imagecolorallocate($image, rand(150, 255), rand(150, 255), rand(150, 255));//生成随机色，作为横线的颜色
			    imageline($image, rand(10, 50), rand(10, 20), rand(80,90), rand(15, 25), $linePoint);
			 }
			 imagepng($image); //在浏览器上显示图片
			  imagedestroy($image);//销毁图片
		}
	}
?>