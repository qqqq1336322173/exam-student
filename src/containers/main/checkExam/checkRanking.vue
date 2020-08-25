<template lang="html">
  <el-card class="box-card">
    <div slot="header" class="clearfix">
      <span>班级排名</span>
      <el-button style="float: right; padding: 3px 0" type="text" @click="goBack">返回</el-button>
    </div>
    <div  class="text item">
       班级排名:<el-span style="padding: 0px 8px">第{{checkRankingData}}名</el-span>
    </div>
  </el-card>
</template>
<script>
import { Message, MessageBox } from "element-ui";
import httpServer from '@/components/httpServer/httpServer.js'
export default {
  data () {
    return {
      checkRankingData: 0
    }
  },
  mounted () {
    let { result_id } = this.$route.query,
      student_id = localStorage.stuId;
    httpServer({
      url: 'http://127.0.0.1/exam-student/app/control/query.php?get_exam_rank',
      method: 'get'
    }, {
      result_id,
      student_id
    }).then((res) => {
      console.log(res);
      res = res.data;
      if (res.respCode == 200) {
        //排名赋值
        this.checkRankingData = res.checkRankingList[0].rank;
      }
    }).catch(err => err)
  },
  methods: {
    goBack () {
      history.go(-1);
    }
  }
}
</script>  
<style>
.text {
  font-size: 14px;
}

.item {
  margin-bottom: 18px;
}

.clearfix:before,
.clearfix:after {
  display: table;
  content: "";
}
.clearfix:after {
  clear: both;
}

.box-card {
  width: 480px;
}
</style>