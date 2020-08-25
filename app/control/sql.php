<?php
  /*
  1.连接数据库
  2.查看连接成功与否
  3.设置中文
  4.sql语句
  5.执行sql语句
  6.处理结果集
  7.输出到页面
  8.关闭资源
  */
  $conn = @mysqli_connect('localhost','root','root','baidu');
  if(!$conn){
    exit("服务器连接失败！"); 
  }
  mysqli_query($conn,'set names utf8');
  $sql = "select id,name from news";
  $result = mysqli_query($conn,$sql);
  echo '<pre>';
  //执行成功之后
  if($result){
    $rows = [];
    while($row = mysqli_fetch_assoc($result)){
      $rows[] = $row;
    }
    echo '<ul>';
    foreach($rows as $v){
      echo "<li style='list-style:none'>". $v['id'] . "&nbsp;&nbsp;" . $v['name'] . "</li>";
    }
    echo "</ul>";
  }
  //关闭资源
  mysqli_close($conn);

?>