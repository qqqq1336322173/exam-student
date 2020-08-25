<?php
include_once('./config.php');
include_once('../model/Db.class.php');
$db = Db::getInstance();
$query_method = $_GET['exam_query'];
$query_tao_method = $_GET['exam_query_tao'];
$query_get_question_method = $_GET['get_question'];
$query_get_exam_res = $_GET['get_exam_res'];
//查询排名
$query_get_exam_rank = $_GET['get_exam_rank'];
if(isset($query_method)){
  $student_id = $_GET['stuId'];
  //执行查询语句
  $sql ="SELECT DISTINCT cname,exam_course.id from exam_stu_course left join exam_course on exam_stu_course.course_id = exam_course.id where exam_stu_course.student_id = ${student_id}";
  // echo $sql;
  $result = $db->select($sql);
  if($result && count($result) > 0){
    $res = array('respCode'=>200,"course_list"=>$result,'msg'=>"查询成功");
  }else{
    $res = array('respCode'=>100,'msg'=>"查询失败");
  }
  exit(json_encode($res));
}
if(isset($query_tao_method)){
  $course_id = $_GET['course_id'];
  $student_id = $_GET['stuId'];
  //执行查询语句
  $sql ="select tname,exam_tao.id as tao_id,exam_tao.start_time,exam_tao.end_time from exam_stu_course left join exam_tao on exam_stu_course.tao_id = exam_tao.id where exam_stu_course.student_id = ${student_id} and exam_tao.course_id = ${course_id}";
  // echo $sql;
  $result = $db->select($sql);
  if($result && count($result) > 0){
    //对时间做下处理
    foreach($result as $key=>$value){
      $result[$key]['new_start_time'] = date('Y/m/d H:i',$value['start_time']);
      $result[$key]['new_end_time'] = date('Y/m/d H:i',$value['end_time']);
      $result[$key]['join_time'] = $result[$key]['new_start_time'] . '-' . $result[$key]['new_end_time'];
    }
    $res = array('respCode'=>200,"tao_list"=>$result,'msg'=>"查询成功");
  }else{
    $res = array('respCode'=>100,'msg'=>"查询失败");
  }
  exit(json_encode($res));
}
//查询考试的题目
if(isset($query_get_question_method)){
  $tao_id = $_GET['tao_id'];
  // echo $tao_id;
  // exit;
  $course_id = $_GET['course_id'];
  $res = [];
  // echo $course_id;
  $sql = "select id,subject,type,optionA,optionB,optionC,optionD,null as count from exam_dan_questions WHERE tao_id = ${tao_id} and course_id = ${course_id} union all  select id,subject,type,optionA,optionB,optionC,optionD,null from exam_duo_questions WHERE tao_id = ${tao_id} and course_id = ${course_id} union all select id,subject,type,null,null,null,null,null from exam_pan_questions WHERE tao_id = ${tao_id} and course_id = ${course_id} union all select id,subject,type,null,null,null,null,count from exam_tian_questions WHERE tao_id = ${tao_id} and course_id = ${course_id} union all select id,subject,type,null,null,null,null,null from exam_obj_questions WHERE tao_id = ${tao_id} and course_id = ${course_id};";
  
  // $sql = "select * from a";
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
  
  $array_choice = [];
  foreach($result as $k=>$v){ 
    if($v['optionA']){
      array_push($array_choice,$v['optionA']);
    }
    if($v['optionB']){
      array_push($array_choice,$v['optionB']);
    }
    if($v['optionC']){
      array_push($array_choice,$v['optionC']);
    }
    if($v['optionD']){
      array_push($array_choice,$v['optionD']);
    }
    $result[$k]['choice'] = $array_choice;
    $array_choice = [];
  }
  $res = array('respCode'=>200,"question_list"=>$result,'msg'=>"查询成功");
  exit(json_encode($res));  
}
//查询这个学生考试的所有科目和成绩，可以渲染的科目
if(isset($query_get_exam_res)){
  $student_id = $_GET['student_id'];
  $sql = "select exam_stu_result.id,tao_id,exam_stu_result.course_id,cname,tname,start_time,end_time,resTotal from exam_stu_result left join exam_tao on exam_stu_result.tao_id = exam_tao.id left join exam_course on exam_stu_result.course_id = exam_course.id where exam_stu_result.student_id = ${student_id} and exam_stu_result.verify = 0;";
  $result = $db->select($sql);
  if(!$result){
    if($result === false){
      $res = array('respCode'=>100,'msg'=>"查询失败");
      exit(json_encode($res));
    }else{
      $res = array('respCode'=>100,'msg'=>"查询为空",'selectExamList'=>[]);
      exit(json_encode($res));
    }
  }
  foreach($result as &$v){
    //2020/08/06 10:50-2023/10/10 00:46
    $v['join_time'] = date("Y/m/d h:i",$v['start_time']) . "-" . date("Y/m/d h:i",$v['end_time']);
  }
  $res = array('respCode'=>200,'msg'=>"查询成功",'selectExamList'=>$result);
  exit(json_encode($res));

}
//查询排名
if(isset($query_get_exam_rank)){
  $result_id = $_GET['result_id'];
  $student_id = $_GET['student_id'];
  // echo $result_id,$student_id;
  $sql = "SELECT
      new.student_id,
      new.resTotal,
      new.rank
      FROM
      (
        SELECT
          a.student_id,
          a.resTotal,
          @rownum := @rownum + 1 AS num_tmp,
          @incrnum := CASE
        WHEN @rowtotal = a.resTotal THEN
          @incrnum
        WHEN @rowtotal := a.resTotal THEN
          @rownum
        END AS rank
        FROM
          (
            SELECT
              student_id,
              COUNT(*) AS resTotal
            FROM
              checkAllStudentExam
                                    where (select glass_id from exam_student where id = ${student_id}) and verify = 0 and id = ${result_id}
            GROUP BY
              student_id
            ORDER BY
              resTotal
          ) AS a,
          (
            SELECT
              @rownum := 0 ,@rowtotal := NULL ,@incrnum := 0
          ) r
          ) AS new where student_id = ${student_id}
  ";
  $result = $db->select($sql);
  if(!$result){
    $res = array('respCode'=>100,'msg'=>"查询失败");
    exit(json_encode($res));
  }
  $res = array('respCode'=>200,'msg'=>"查询成功",'checkRankingList'=>$result);
  exit(json_encode($res));
}

?>