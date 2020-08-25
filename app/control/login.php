<?php
include_once('./config.php');
include_once('../model/Db.class.php');
$db = Db::getInstance();
// var_dump($_POST);
$sname = trim($_POST['username']);
$pwd = md5(md5(trim($_POST['password'])));
$sql ="select id as stuId,sname from exam_student where sname = '${sname}' and pwd = '${pwd}'";
$result = $db->select($sql);
if($result && count($result) > 0){
  $res = array('respCode'=>200,"studentInfo"=>$result,'msg'=>"登录成功");
}else{
  $res = array('respCode'=>100,'msg'=>"登录失败");
}
exit(json_encode($res));
//var_dump($res);
// $res = array('respCode'=>1,"studentInfo"=>array(),'msg'=>"登录成功");
// exit(json_encode($res));
?>