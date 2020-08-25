<?php
include_once('./config.php');
include_once('../model/Db.class.php');
$db = Db::getInstance();
//查询
$methodGetStuResult = $_GET['getStuResult'];
if(isset($methodGetStuResult)){
  $result_id = $_GET['result_id'];
  $course_id = $_GET['course_id'];
  $tao_id = $_GET['tao_id'];
  $sql = "select erro_subject from exam_stu_result where id = ${result_id};";
  $result = $db->select($sql);
  /*
  array(1) { [0]=> array(1) { ["erro_subject"]=> string(180) "[{"type": "1", "tao_id": "5"}, {"type": "1", "tao_id": "6"}, {"type": "2", "tao_id": "3"}, {"type": "2", "tao_id": "4"}, {"type": "3", "tao_id": "3"}, {"type": "3", "tao_id": "4"}]" } }
  */
  if(!$result){
    $res = array('respCode'=>100,'msg'=>"查询错误");
    exit(json_encode($res));
  }
  /*
  //存入错误的答案
  Array (
    [0] => stdClass Object ( [type] => 1 [tao_id] => 4 [question_answer] => 0 ) 
    [1] => stdClass Object ( [type] => 1 [tao_id] => 5 [question_answer] => 2 ) 
    [2] => stdClass Object ( [type] => 1 [tao_id] => 6 [question_answer] => 0 ) 
    [3] => stdClass Object ( [type] => 2 [tao_id] => 3 [question_answer] => Array ( [0] => 0 [1] => 1 ) ) 
    [4] => stdClass Object ( [type] => 3 [tao_id] => 3 [question_answer] => 0 ) )
  */
  $erroSubjectList = json_decode($result[0]['erro_subject']);
  /*
  把这套题的所有的题目都查询处理然后进行匹配
  */
  /**
   * $sql = select id,subject,type,optionA,optionB,optionC,optionD,null as count,answer,null as answerA,null as answerB,null as answerC,null as answerD,null as answerE from exam_dan_questions WHERE tao_id = 1 and course_id = 1 union all  select id,subject,type,optionA,optionB,optionC,optionD,null,answer,null,null,null,null,null from exam_duo_questions WHERE tao_id = 1 and course_id = 1 union all select id,subject,type,null,null,null,null,null,answer,null,null,null,null,null from exam_pan_questions WHERE tao_id = 1 and course_id = 1 union all select id,subject,type,null,null,null,null,count,null,answerA,answerB,answerC,answerD,answerE from exam_tian_questions WHERE tao_id = 1 and course_id = 1  union all select id,subject,type,null,null,null,null,null,answer,null,null,null,null,null from exam_obj_questions WHERE tao_id = 1 and course_id =1;
   * */
  $sql1 = "select id,subject,type,optionA,optionB,optionC,optionD,null as count,answer,null as answerA,null as answerB,null as answerC,null as answerD,null as answerE from exam_dan_questions WHERE tao_id = ${tao_id} and course_id = ${course_id} union all  select id,subject,type,optionA,optionB,optionC,optionD,null,answer,null,null,null,null,null from exam_duo_questions WHERE tao_id = ${tao_id} and course_id = ${course_id} union all select id,subject,type,null,null,null,null,null,answer,null,null,null,null,null from exam_pan_questions WHERE tao_id = ${tao_id} and course_id = ${course_id} union all select id,subject,type,null,null,null,null,count,null,answerA,answerB,answerC,answerD,answerE from exam_tian_questions WHERE tao_id = ${tao_id} and course_id = ${course_id}  union all select id,subject,type,null,null,null,null,null,answer,null,null,null,null,null from exam_obj_questions WHERE tao_id = ${tao_id} and course_id = ${course_id}";
  // echo $sql1;
  $result1 = $db->select($sql1);
  if(!$result1){
    $res = array('respCode'=>100,'msg'=>"查询错误");
    exit(json_encode($res));
  }
  foreach($erroSubjectList as &$v){
    //匹配题目
    //如果这道题的type = 1  tao_id == $result1下面的id
    foreach($result1 as $k1=>$v1){
      if($v->type == $v1['type'] && $v->tao_id == $v1['id']){
        $v->subject = $v1['subject'];
        $v->optionA = $v1['optionA'];
        $v->optionB = $v1['optionB'];
        $v->optionC = $v1['optionC'];
        $v->optionD = $v1['optionD'];
        $v->answer = $v1['answer'];
        if($v1['answerA']){
          $v->answerA = $v1['answerA'];
        }
        if($v1['answerB']){
          $v->answerB = $v1['answerB'];
        }
        if($v1['answerC']){
          $v->answerC = $v1['answerC'];
        }
        if($v1['answerD']){
          $v->answerD = $v1['answerD'];
        }
        if($v1['answerE']){
          $v->answerE = $v1['answerE'];
        }
        break;
      }
    }
  }
  $res = array('respCode'=>200,'msg'=>"查询成功",'erroSubjectList'=>$erroSubjectList);
  exit(json_encode($res));
}
//添加
//修改