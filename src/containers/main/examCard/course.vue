<template lang="html">
  <div class="choose-course">
    <div class="container">
      <div class="row">
        <div v-for="item in course_list" :key="item.id" @click="tao(item.id)" class="clo-sm-6 col-md-4 col-lg-3 caption text-center bg-info text-white mr-3 my-3">
          {{item.cname}}
        </div>
        <!--<div class="clo-sm-6 col-md-4 col-lg-3 caption text-center bg-info text-white mr-3">
          JavaScript
        </div> 
         <div class="clo-sm-6 col-md-4 col-lg-3 caption text-center bg-info text-white mr-3">
          php
        </div> 
        <div class="clo-sm-6 col-md-4 col-lg-3 caption text-center bg-info text-white mr-3">
          java
        </div>  -->
      </div>
    </div>
  </div>
</template>

<script>
import httpServer from '@/components/httpServer/httpServer.js'

export default {
  /*
  先渲染课程
  点击课程跳转到对应的考试
  */
  data () {
    return {
      course_list: []
    };
  },
  created () {

  },
  mounted () {
    //获取课程
    httpServer({
      url: 'http://127.0.0.1/exam-student/app/control/query.php?exam_query',
      method: 'get'
    }, {
      stuId: localStorage.stuId,
    })
      .then((res) => {
        //点击在线考试，出来这个考生的目前可以参加的所有的科目
        res = res.data;
        this.course_list = res.course_list
        console.log(res)
        // let respData = res.data;
        // // let respData = {
        // //   "respCode": "1",
        // //   "paperId": 38,
        // //   "instId" : 26,
        // // }
        // sessionStorage.instId = respData.instId;
        // if (res.data.respCode == '1') {

        // }

      })
      .catch((err) => {

      })
    // console.log(1)
  },
  methods: {
    //获取考试的科目
    tao (id) {

      //跳转到考试的卷子页面
      this.$router.push({ path: '/main/exam/tao', query: { course_id: id } });
      //this.$router.push('/main/exam/tao?course_id=');
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
