<template lang="html">
  <div class="choose-course">
    <div class="container">
      <div class="row">
        <div v-for="item in tao_list" :data-start-time="item.start_time" :data-end-time="item.end_time" :key="item.tao_id" @click.stop="gotoExam(item.tao_id,$event)" class="clo-sm-7 col-md-5 col-lg-5 caption text-center bg-info text-white mr-3 my-3">
          {{item.tname}}
          <!-- <p>{{item.start_time}}</p> -->
          <p :data-start-time="item.start_time" :data-end-time="item.end_time">{{item.join_time}}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Message, MessageBox } from 'element-ui';
import httpServer from '@/components/httpServer/httpServer.js'

export default {
  /*
  先渲染课程
  点击课程跳转到对应的考试
  */
  data () {
    return {
      tao_list: []
    };
  },
  created () {

  },
  mounted () {
    //把课程id存入本地
    sessionStorage.course_id = this.$router.currentRoute.query.course_id;
    //获取课程
    httpServer({
      url: 'http://127.0.0.1/exam-student/app/control/query.php?exam_query_tao',
      method: 'get'
    }, {
      stuId: localStorage.stuId,
      course_id: this.$router.currentRoute.query.course_id
    })
      .then((res) => {
        //点击在线考试，出来这个考生的目前可以参加的所有的科目
        res = res.data;
        if (res.respCode == 200) {
          this.tao_list = res.tao_list
        }
        console.log(res)
      })
      .catch((err) => {

      })
    // console.log(1)
  },
  methods: {
    //进入对应的题目
    gotoExam (id, emit) {
      //判断这个考试时间是否过了
      //拿到后台去判断
      httpServer({
        method: 'get',
        url: 'http://127.0.0.1/exam-student/app/control/isGotoExamCard.php'
      }, {
        tao_id: id,
        student_id: localStorage.stuId,
        course_id: this.$router.currentRoute.query.course_id
      })
        .then((res) => {
          res = res.data;
          console.log(res)
          /*
          如果这个时间在考试时间内，可以进行考试，否则
          */
          if (res.respCode == 200) {
            //把开始时间和结束时间存在本地
            console.log(emit.target);
            // console.log(res[0]['start_time'])
            sessionStorage.startTime = emit.target.getAttribute("data-start-time");
            sessionStorage.endTime = emit.target.getAttribute("data-end-time");
            sessionStorage.instId = 0;
            //把这套题存入本地
            sessionStorage.tao_id = id;
            this.$router.push({ path: `/main/exam/${id}/${this.$router.currentRoute.query.course_id}/0` })
          } else {
            MessageBox(res.msg, '提示', {
              confirmButtonText: '确定',
              type: 'error'
            });
          }
          //this.$router.push({ path: `/main/exam/${id}/0` })
        })
        .catch((err) => {

        })
      // console.log(id)

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
