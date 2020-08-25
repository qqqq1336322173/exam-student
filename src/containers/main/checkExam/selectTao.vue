<template lang="html">
  <el-table
      :data="taoData"
      style="width: 100%">
      <el-table-column
        prop="cname"
        label="考试科目"
        width="120">
      </el-table-column>
      <el-table-column
        prop="tname"
        label="考试名称"
        width="200">
      </el-table-column>
      <el-table-column
        prop="join_time"
        label="考试时间"
        width="280">
      </el-table-column>
      <!-- <el-table-column
        prop="name"
        label="姓名"
        width="180">
      </el-table-column> -->
      <!-- <el-table-column
        prop="date"
        label="试卷提交时间"
        width="300">
      </el-table-column> -->
      <el-table-column
        prop="resTotal"
        label="成绩（分）" width="180">
      </el-table-column>
       <el-table-column
        label="错题集">
         <template slot-scope="scope">
          <el-button @click.native.prevent="checkWrongTopic(scope.$index,taoData)" type="primary" size="small">查看错题</el-button>
        </template>
      </el-table-column>
       <el-table-column
        prop="selectError"
        label="班级排名">
         <template slot-scope="scope1">
  <el-button @click.native.prevent="checkRanking(scope1.$index,taoData)"
             type="primary"
             size="small">查看排名</el-button>
</template>
      </el-table-column>
  </el-table>
</template>

<script>
import { Message, MessageBox } from "element-ui";
import httpServer from '@/components/httpServer/httpServer.js'

export default {
  /*
  先渲染课程
  点击课程跳转到对应的考试
  */
  data () {
    return {
      taoData: []
    }
  },
  created () {

  },
  mounted () {
    //获取这个学生的考试的分数,必须是这个成绩都出完了之后才能查看

    httpServer({
      url: 'http://127.0.0.1/exam-student/app/control/query.php?get_exam_res',
      method: 'get'
    }, {
      student_id: localStorage.stuId
    }).then(res => {
      res = res.data;//重新赋值
      if (res.respCode == 200) {
        this.taoData = res.selectExamList;//把拿过来的值赋值给数据
      } else {

      }
      console.log(res)
    }).catch(err => {

    })
  },
  methods: {
    checkWrongTopic (index, taoData) {
      let result_id = taoData[index]['id'],
        course_id = taoData[index]['course_id'],
        tao_id = taoData[index]['tao_id'];
      console.log(course_id, tao_id)
      //假如考试100分就没必要看错题
      if (taoData[index]['resTotal'] == 100) {
        Message.success({
          showClose: true,
          message: "没有错题",
          type: "success"
        })
        return false;
      }
      this.$router.push(`/main/exam/checkWrongTopic?result_id=${result_id}&course_id=${course_id}&tao_id=${tao_id}&questionId=0`);
    },
    //查看班级的排名
    checkRanking (index, taoData) {
      //查询它所在的班级
      //查询到属于这个班级的所有的学生的成绩
      /*
      创建了一个视图
      create view checkAllStudentExam as select exam_stu_result.student_id,exam_stu_result.resTotal from exam_stu_result left join  exam_student on exam_stu_result.student_id = exam_student.id where (select glass_id from exam_student where id = 1) and exam_stu_result.verify = 0;
      执行查询视图
      select * from checkAllStudentExam;
      */
      //然后进行排名
      /*
      查询排名
      SELECT
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
            GROUP BY
              student_id
            ORDER BY
              resTotal
          ) AS a,
          (
            SELECT
              @rownum := 0 ,@rowtotal := NULL ,@incrnum := 0
          ) r
      ) AS new 
      */
      //传递到后台的参数
      //result_id,student_id
      let result_id = taoData[index]['id'];
      this.$router.push(`/main/exam/checkRanking?result_id=${result_id}`);

    }
  }
}
</script>

<style lang="css">
.changePass {
  width: 60%;
  margin: 0 auto;
  margin-top: 50px;
}
</style>
