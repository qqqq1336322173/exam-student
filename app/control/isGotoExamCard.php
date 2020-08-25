<?php
include_once('./config.php');
include_once('../model/Db.class.php');
$db = Db::getInstance();
$tao_id = $_GET['tao_id'];
$student_id = $_GET['student_id'];
$course_id = $_GET['course_id'];
//先查这个学生这套题考了没？
$sql1 = "select page_state FROM exam_stu_course where student_id = ${student_id} and course_id = ${course_id} and tao_id = ${tao_id}";
$result = $db->select($sql1);
if(!$result){
  $res = array('respCode'=>100,'msg'=>"查询失败");
  exit(json_encode($res));
}
if(count($result) > 0){
  if($result[0]['page_state'] == 1){
    $res = array('respCode'=>100,'msg'=>"抱歉，该考试已提交了试卷，如想在提交，请联系对应的老师");
    exit(json_encode($res));
  }
}
//查考试时间过了没？
$sql2 ="select start_time,end_time from exam_tao where id = ${tao_id}";
$result = $db->select($sql2);
if($result && count($result) > 0){
  $now_time = time();
  if($now_time >= $result[0]['start_time'] && $now_time <= $result[0]['end_time']){
    $res = array('respCode'=>200,'time_list'=>$result,'msg'=>"可以进入考试");
  }else if($now_time < $result[0]['start_time']){
    $res = array('respCode'=>100,'msg'=>"抱歉，考试还未开启");
  }else{
    $res = array('respCode'=>100,'msg'=>"考试时间已过");
  }
}else{
  $res = array('respCode'=>100,'msg'=>"查询失败");
}
exit(json_encode($res));
//var_dump($res);
// $res = array('respCode'=>1,"studentInfo"=>array(),'msg'=>"登录成功");
// exit(json_encode($res));
?>