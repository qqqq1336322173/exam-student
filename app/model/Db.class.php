<?php
    class Db{
        private $conn;
        public static $install = null; 
        // 初始化魔术方法
        public function __construct(){
            // 连接数据库
            // 把配置文件包起来
            include_once('../config/db.php');
            $this->conn = @mysqli_connect($db['host'],$db['username'],$db['pwd'],$db["database"]);
            if(!$this->conn){
                exit('抱歉，数据库连接失败！');
            }else{
                // echo '数据库连接成功';
                mysqli_query($this->conn,'set names utf-8');
            }
        }
         // 禁止这个对象被克隆
         private function clone(){

        }
        public static function getInstance(){
            if(is_null(self::$install)){
                self::$install = new Db();
            }
            return self::$install;//返回这个数据库对象
        }
        public function select($sql){
            $data = [];
            $result = mysqli_query($this->conn,$sql);
            if($result){
                while ($row = mysqli_fetch_assoc($result)){
                    $data[] = $row;
                }
            }else{
              return false;
            }
            return $data;
        }
        public function edit($sql){
            $result = mysqli_query($this->conn,$sql);
            return $result;
        }
        public function query($sql){
          $result = mysqli_query($this->conn,$sql);
          return $result;
        }
        public function insert($sql){
          $result = mysqli_query($this->conn,$sql);
          return $result;
      }
    }
?>