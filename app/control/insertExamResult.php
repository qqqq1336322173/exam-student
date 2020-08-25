<?php
include_once('./config.php');
include_once('../model/Db.class.php');
$db = Db::getInstance();
//从前端获取的数据
$student_id = $_POST['student_id'];
$question_list = json_decode($_POST['question_list']);//把json数据转换成数组
$tao_id = $_POST['tao_id'];
$course_id = $_POST['course_id'];
//执行查询语句
// var_dump($student_id,$question_list,$tao_id,$course_id);
//查询出这套题的所有答案
//查询每道题的分数，每道题的答案
$sql = "select id,res,answer,null as answerA,null as answerB,null as answerC,null answerD,null as answerE from exam_dan_questions WHERE tao_id = ${tao_id} and course_id = ${course_id} union all  select id,res,answer,null,null,null,null,null from exam_duo_questions WHERE tao_id = ${tao_id} and course_id = ${course_id} union all select id,res,answer,null,null,null,null,null from exam_pan_questions WHERE tao_id = ${tao_id} and course_id = ${course_id} union all select id,res,null,answerA,answerB,answerC,answerD,answerE from exam_tian_questions WHERE tao_id = ${tao_id} and course_id = ${course_id} union all select id,res,answer,null,null,null,null,null from exam_obj_questions WHERE tao_id = ${tao_id} and course_id = ${course_id};";
$result = $db->select($sql);
// var_dump($result);
if(!$result){
  if($result === false){
    $res = array('respCode'=>100,'msg'=>"查询错误");
  }else{
    $res = array('respCode'=>100,"question_list"=>[],'msg'=>"查询为空");
  }
  exit(json_encode($res));
}
$resSingle = 0;//单选
$resMore = 0;//多选
$resPan = 0;//判断题
$resTian = 0;//填空题
$resObj = 0;//简答题
$resTotal = 0;//总分数
$verify = 0;//0:不用老师审核都是客观题,1:需要老师审核，有主观题
//错误的题目
$erro_subject = [];
foreach($question_list as $k=>$v){
  if($v->type == 1){
    //如果答案一样，那就加分，
    //答案不一样就把它写入到错题中去
    if($v->answer == $result[$k]['answer']){
      $resSingle += $result[$k]['res'];
    }else{
      $erro_subject[] = array('type'=>$v->type,'tao_id'=>$v->id,'question_answer'=>$v->answer);
    }
  }else if($v->type == 2){
    //多选是所有的答案都匹配对了才加分，否则都扣分
    // print_r(implode(',',$v->answer));
    // echo '<br>____<br>';
    if(implode(',',$v->answer) == $result[$k]['answer']){
      $resMore += $result[$k]['res'];//加分
    }else{
      //存入错误的答案
      $erro_subject[] = array('type'=>$v->type,'tao_id'=>$v->id,'question_answer'=>$v->answer);
    }
  }else if($v->type == 3){
    //判断题的答案
    if($v->answer == $result[$k]['answer']){
      $resPan += $result[$k]['res'];//加分
    }else{
      //存入错误的答案
      $erro_subject[] = array('type'=>$v->type,'tao_id'=>$v->id,'question_answer'=>$v->answer);
    }
  }else{
    //有主观题
    $verify = 1;
  }
  // print_r($v->id);
  // echo "______<br>";
}
// print_r($resPan);
// print_r($erro_subject);
// print_r($question_list);
// print_r($result);
//写入到数据库
$join_time = time();
$erro_subject = json_encode($erro_subject);
$resTotal = $resSingle + $resMore + $resPan + $resTian + $resObj;
// var_dump($erro_subject);
// exit;
// echo $resTotal,$time,$verify;
//修改这个试卷的状态
/*
1.开启事务
2.执行sql
3.成功 commit
4.失败 rollback
*/
$db->query("start transaction");
$sql1 = "insert into exam_stu_result(student_id,course_id,tao_id,resSingle,resTian,resMore,resPan,resObj,resTotal,join_time,erro_subject,verify) values($student_id,$course_id,$tao_id,$resSingle,$resTian,$resMore,$resPan,$resObj,$resTotal,$join_time,'$erro_subject',$verify)";
$result1 = $db->insert($sql1);
$sql2 = "UPDATE exam_stu_course set page_state = 1 where student_id = ${student_id} and course_id = ${course_id} and tao_id = ${tao_id}";
$result2 = $db->edit($sql2);
if($result1 && $result2){
  $db->query('commit');
  $res = array('respCode'=>200,'msg'=>"考试完成",'resTotal'=>$resTotal,'verify'=>0);
}else{
  $db->query('rollback');
  $res = array('respCode'=>100,'msg'=>"查询失败");
}
exit(json_encode($res));


?>