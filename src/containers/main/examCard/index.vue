<template lang="html">
    <div class="exam-card">
      <div class="card-header clearfix">
        <div class="time f-l">
          <i class="el-icon-time"></i>
          <span v-cloak>{{hour}}:{{min}}:{{sec}}</span>
        </div>
        <div class="answer-card f-l m-l-20" @click="dialogVisible = true">
          <i class="el-icon-date"></i>
          <span>答题卡</span>
        </div>
        <div class="submit-paper f-r">
          <el-button type="primary" round @click="submitPaperNow">交卷</el-button>
        </div>
      </div>
      <div class="card-content container">
        <el-card class="box-card">
          <div slot="header" class="clearfix">
            <span>第{{questionId}}题（{{typeText}}）</span>
            <el-button style="float: right; padding: 3px 0" type="text" @click="nextQuestion">下一题</el-button>
            <el-button style="float: right; padding: 3px 0" type="text" @click="lastQuestion">上一题</el-button>
          </div>
          <div>
            <!-- 单选 -->
            <div class="q-single" v-show="type == 1">
              <div class="question">
                {{currentQuestion.subject}} ( &nbsp;&nbsp; )
              </div>
              <div class="answer">
                <div class="answer-item" v-for="(item,index) in currentQuestion.choice">
                  <el-radio  v-model="radio[radioi]" :label="index">{{item}}</el-radio>
                </div>
              </div>
            </div>

            <!-- 多选 -->
            <div class="q-multiple" v-show="type == 2">
              <div class="question">
                {{currentQuestion.subject}}
              </div>
              <div class="answer">
                <div class="answer-item" v-for="(item,index) in currentQuestion.choice">
                  <!-- 多选题 
                  checkbox:{
                    0:
                  }
                  -->
                  <el-checkbox v-model="checked[checki]"   :label="index">{{item}}</el-checkbox>
                </div>
              </div>
            </div>

            <!-- 判断 -->
            <div class="q-true-or-false" v-show="type == 3">
              <div class="question">
                {{currentQuestion.subject}}
              </div>
              <div class="answer">
                <div class="answer-item">
                  <el-radio v-model="panduanData[pani]"  label="0">正确</el-radio>
                </div>
                <div class="answer-item">
                  <el-radio v-model="panduanData[pani]" label="1">错误</el-radio>
                </div>
              </div>
            </div>

            <!-- 填空 -->
            <div class="q-fill-in" v-show="type == 4">
              <div class="question">
                {{currentQuestion.subject}}
              </div>
              <div class="answer">
                <div class="answer-item">
                  <el-input
                    type="textarea"
                    :rows="5"
                    placeholder="请输入答案"
                    v-model="tiankongData[tiani]">
                  </el-input>
                  <div class='alert alert-heading'>
                    <h6>温馨提示:</h6>从第二个答案开始，答案与答案之间要换行
                  </div>
                </div>
              </div>
            </div>

            <!-- 简答 -->
            <div class="q-short-answer" v-show="type == 5">
              <div class="question">
                {{currentQuestion.subject}}
              </div>
              <div class="answer">
                <div class="answer-item">
                  <el-input
                    type="textarea"
                    :rows="15"
                    placeholder="请输入答案"
                    v-model="jiandaData[jiandai]">
                  </el-input>
                </div>
              </div>
            </div>

            <!-- 程序 -->
            <!-- <div class="q-program" v-show="type == 6">
              <div class="question">
                {{currentQuestion.subject}}
              </div>
              <div class="answer">
                <div class="answer-item">
                  <el-input
                    type="textarea"
                    :rows="15"
                    placeholder="请输入答案"
                    v-model="textarea">
                  </el-input>
                </div>
              </div>
            </div> -->
          </div>

        </el-card>
      </div>
      <!-- <el-dialog
        title="答题卡"
        :visible.sync="dialogVisible"
        width="50%"
        >
        <el-button v-for="(item,index) in questionList" key="index" type="primary" class="stu-answer-item" @click="clickAnswerCard(index)">{{index+1}}</el-button>
      </el-dialog> -->
    </div>
</template>

<script>
import { Message, MessageBox } from 'element-ui';
import httpServer from '@/components/httpServer/httpServer.js'
/*
1.单选题
2.多选题
3.判断题
4.填空题
5.简答题
*/
export default {
  data () {
    return {
      dialogVisible: false,
      currentQuestion: {},
      questionList: [],
      type: 1,
      textarea: '',
      spaceNum: [],
      flag: 0,//默认是下一道题 1 下一道题  -1表示上一道题 0表示当前
      /*
      radio:{
        0:'radio1'
      }
      */
      radio: {
        0: 'radio0'//v-model="radio0"   radio0:''  radio[radioi]
      },//单选的结果
      /*
      checkbox:{
        0:[],
        1:[],
      }
      
      */
      checkbox: [],
      checked: {
        0: [],
        1: [],
        2: []
      },//这个变量必须是唯一的
      questionId: 0,
      checki: 0,
      pani: 0,
      radioi: 0,
      tiani: 0,//填空
      jiandai: 0,//简答
      instId: 0,
      startTime: '',
      endTime: '',
      transformChar: ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'],
      hour: 0,
      min: 0,
      sec: 0,
      timer: 0,
      onblurTime: 0,
      openNextFlag1: false,//默认关闭的单选
      openNextFlag2: false,//多选
      openNextFlag3: false,//判断题
      openNextFlag4: false,//填空题
      openNextFlag5: false,//简答题
      panduanData: {
        // 0: 'panduan0'
      },
      tiankongData: {

      },
      jiandaData: {

      },
      flagBtn: false
    }
  },
  computed: {
    typeText: function () {
      switch (this.type) {
        case 1://填空
          return '单选题';
          break;
        case 2://简答
          return '多选题';
          break;
        case 3://程序
          return '判断题';
          break;
        case 4://选择
          return '填空题';
          break;
        case 5://判断
          return '程序题';
          break;
        // case 3://多选
        //   return '多选题';
        //   break;
      }
    }
  },
  methods: {
    //设置初始化数据
    setInit () {
      // console.log(1)
      let obj_duo = {}, i = 0, j = 0, obj_pan = {};
      // console.log(this.questionList)
      for (let item of this.questionList) {
        if (item.type == 2) {//多选题
          obj_duo[i] = [];
          i++;
        } else if (item.type == 3) {//判断题
          obj_pan[j] = 'panduan' + j;
          j++;
        } else if (item.type == 4) {//填空题
          let count_arr = [];
          for (let i = 0; i < item.count; i++) {
            count_arr[i] = "_________";
          }
          item.subject += " " + count_arr.join('、')
        }
      }
      //重组下数据
      // console.log(this.questionList)
      // console.log(obj_pan)
      this.checked = obj_duo;
      this.panduanData = obj_pan;
      // console.log(this.panduanData)

    },
    initExam () {
      // console.log(1111)
      let that = this;
      window.onblur = function () {
        // that.onblurTime++;
        // if (that.onblurTime == 2) {

        //   that.submitPaper();
        //   MessageBox("你已交卷", '提示', {
        //     confirmButtonText: '确定',
        //     type: 'error'
        //   });
        // }
        // else {
        //   MessageBox("考试期间不能离开此页面哦~这一次只是提醒，下次离开就会直接交卷了哦", '提示', {
        //     confirmButtonText: '确定',
        //     type: 'error'
        //   });
        // }

      }
      // this.timer = window.setInterval(() => {

      //   let timeLast = new Date(this.endTime).getTime() - new Date().getTime();
      //   let hour = parseInt(timeLast / (1000 * 60 * 60));
      //   let min = parseInt((timeLast % (1000 * 60 * 60)) / (1000 * 60));
      //   let sec = parseInt(((timeLast % (1000 * 60 * 60)) % (1000 * 60)) / 1000);
      //   this.hour = hour < 10 ? '0' + hour : hour;
      //   this.min = min < 10 ? '0' + min : min;
      //   this.sec = sec < 10 ? '0' + sec : sec;

      //   if (this.hour == 0 && this.min == 0 && this.sec == 0) {
      //     window.clearInterval(this.timer);
      //     this.submitPaper();
      //   }

      // }, 1000)
      /*
      1.当用户进入到第一题的时候 单选的 v-model="radio0"
      2.对应的 radio = {0:radio0}
      3.进入第二题的时候， 单选的 v-model = radio1;
      4.对应的 radio = {1:radio1};
    


      多选
      checked:{
        'checked0':[],
        'checked1':[]
      }
    */
      this.questionId = parseInt(this.$route.params.questionId) + 1;//当前题号
      // console.log(this.$route.params.questionId, this.questionList)
      let currentQuestion = this.questionList[this.$route.params.questionId];
      /*有多少个多选题就要生成多少个空对象
      checked: {
        0: []
      }*/
      // this.questionList.forEach((item, key) => {
      //   if (item.type == 2) {
      //     this.checked[i] = [];
      //     i++;
      //   }
      //   // console.log(key, item)
      // })
      // console.log(this.checked)

      this.type = parseInt(currentQuestion.type);
      //this.type = parseInt(this.currentQuestion.type);
      //如果是上一道题
      //初始化状态
      // console.log(this.flag)
      if (this.flag == 0) {
        if (this.type == 1 || this.type == 2) {
          this.openNextFlag1 = this.openNextFlag2 = true;
          // console.log(this.openNextFlag1, this.openNextFlag2)
        }
      }
      if (this.flag == -1) {
        this.lastType();
      }
      if (this.flag == 1) {
        this.nextType();
      }
      // console.log(this.checkbox, this.radio)
      //分流如果是单选，如果是多选
      //如果是多选的时候
      // if (this.type == 2) {
      //   this.checki++;
      // }
      // //如果是单选的时候
      // console.log(this.type)
      // if (this.type == 1) {
      //   this.radioi++;
      // }
      // if (this.type == 2) {
      //   if (this.checki > 0) {
      //     this.checki--;
      //   }
      // }
      this.currentQuestion = currentQuestion;
      // console.log(this.checked)
    },
    submitPaper () {
      window.onblur = null;
      this.saveAnswer(false);
      let answer = {};
      for (let i = 0; i < this.questionList.length; i++) {
        /*
        1.单选题
        2.多选题
        3.判断题
        4.填空题
        5.简答题
      */
        switch (this.questionList[i].type) {
          case 1://1.单选题
            answer[i] = this.transformChar[this.questionList[i].answer];
            break;
          case 2://多选题
            for (let j = 0; j < this.questionList[i].answer; j++) {
              if (this.questionList[i].answer[j]) {
                answer[i] += this.transformChar[j];
              }
            }
            break;
          case 3://判断
            if (this.questionList[i].answer == '0') {
              answer[i] = '1';
            }
            else {
              answer[i] = '0';
            }
            break;
          case 4://填空
          case 5://程序
        }

      }

      httpServer({
        url: '/exam/submit_paper'
      }, {
        instId: this.instId,
        answer: answer
      })
        .then((res) => {
          if (res.data.respCode == '1') {
            this.$router.push(`/main/homepage`);
          }
        })
    },
    lastType () {
      // if (this.radioi == 0) {
      //   this.radioi = -1;
      // }
      // if (this.checki == 0) {
      //   this.checki = -1;
      // }
      //如果是单选,如果是多选
      switch (this.type) {
        case 1:
          //console.log(this.openNextFlag1)
          this.radioi = this.openNextFlag1 ? --this.radioi : this.radioi;
          this.radioi = this.radioi < 0 ? 0 : this.radioi
          this.openNextFlag1 = true;
          this.openNextFlag2 = false;
          this.openNextFlag3 = false;
          this.openNextFlag4 = false;
          this.openNextFlag5 = false;
          break;
        case 2:
          this.checki = this.openNextFlag2 ? --this.checki : this.checki;
          this.checki = this.checki < 0 ? 0 : this.checki;
          this.openNextFlag2 = true;
          this.openNextFlag1 = false;
          this.openNextFlag3 = false;
          this.openNextFlag4 = false;
          this.openNextFlag5 = false;
          break;
        case 3:
          this.pani = this.openNextFlag3 ? --this.pani : this.pani;
          this.pani = this.pani < 0 ? 0 : this.pani;
          this.openNextFlag3 = true;
          this.openNextFlag1 = false;
          this.openNextFlag2 = false;
          this.openNextFlag4 = false;
          this.openNextFlag5 = false;
          break;
        case 4:
          this.tiani = this.openNextFlag4 ? --this.tiani : this.tiani;
          this.tiani = this.tiani < 0 ? 0 : this.tiani;
          this.openNextFlag4 = true;
          this.openNextFlag1 = false;
          this.openNextFlag2 = false;
          this.openNextFlag3 = false;
          this.openNextFlag5 = false;
          break;
        case 5:
          this.jiandai = this.openNextFlag5 ? --this.jiandai : this.jiandai;
          this.jiandai = this.jiandai < 0 ? 0 : this.jiandai;
          this.openNextFlag5 = true;
          this.openNextFlag1 = false;
          this.openNextFlag2 = false;
          this.openNextFlag3 = false;
          this.openNextFlag4 = false;
          break;
        default:
          console.log('不变');
          // this.openNextFlag1 = false;
          // this.openNextFlag2 = false;
          break;
      }

    },
    nextType () {
      //如果开关关这就返回
      // if (!this.openNextFlag) {
      //   return;
      // }
      //如果是单选
      switch (this.type) {
        case 1:
          this.radioi = this.openNextFlag1 ? ++this.radioi : this.radioi;
          this.openNextFlag1 = true;
          this.openNextFlag2 = false;
          this.openNextFlag3 = false;
          this.openNextFlag4 = false;
          this.openNextFlag5 = false;
          // this.radio[this.radioi] = 'radio' + this.radioi;
          //this.radio[this.radioi] = 'radio' + this.radioi;
          break;
        case 2:
          //把上一次的数据保存起来
          // console.log(this.checki)
          this.checki = this.openNextFlag2 ? ++this.checki : this.checki;
          // this.checkbox[this.checki][this.checki] = [];
          this.openNextFlag2 = true;
          this.openNextFlag1 = false;
          this.openNextFlag3 = false;
          this.openNextFlag4 = false;
          this.openNextFlag5 = false;
          break;
        case 3:
          this.pani = this.openNextFlag3 ? ++this.pani : this.pani;
          this.openNextFlag3 = true;
          this.openNextFlag1 = false;
          this.openNextFlag2 = false;
          this.openNextFlag4 = false;
          this.openNextFlag5 = false;
          break;
        case 4:
          this.tiani = this.openNextFlag4 ? ++this.tiani : this.tiani;
          this.openNextFlag4 = true;
          this.openNextFlag1 = false;
          this.openNextFlag2 = false;
          this.openNextFlag3 = false;
          this.openNextFlag5 = false;
          break;
        case 5:
          this.jiandai = this.openNextFlag5 ? ++this.jiandai : this.jiandai;
          this.openNextFlag5 = true;
          this.openNextFlag1 = false;
          this.openNextFlag2 = false;
          this.openNextFlag3 = false;
          this.openNextFlag4 = false;
          break;
        default:
          console.log('不变');
          break;
      }
      // console.log(this.pani, this.type, this.openNextFlag3);
    },
    clickAnswerCard (index) {

    },
    //上一道题
    lastQuestion () {
      this.flag = -1;//设置为上一道题
      let questionId = parseInt(this.$route.params.questionId);

      if (questionId == 0) {
        Message.success({
          showClose: true,
          message: "这是第一道题啦",
          type: 'success'
        });
        return;
      }

      //如果类型是多选的时候，如果多选的时候this.checki == 0就让它等于0
      questionId--;
      this.questionId = questionId;
      let tao_id = sessionStorage.tao_id,
        course_id = sessionStorage.course_id;
      this.checkbox = this.checked[this.checki];
      // console.log(this.checkbox)
      this.$router.push(`/main/exam/${tao_id}/${course_id}/${this.questionId}`);
    },
    //下一道题
    nextQuestion () {
      this.flag = 1;//设置为下一道题
      let questionId = parseInt(this.$route.params.questionId);
      // this.radio[questionId] = 'radio' + questionId
      // console.log(this.radio)
      // this.saveAnswer(true);
      //console.log(questionId)

      if (this.questionList.length - 1 == questionId) {
        Message.success({
          showClose: true,
          message: "这是最后一题啦",
          type: 'success'
        });
        return;
      }
      questionId++;
      // this.radioi++;
      this.questionId = questionId;
      let tao_id = sessionStorage.tao_id,
        course_id = sessionStorage.course_id;

      this.$router.push(`/main/exam/${tao_id}/${course_id}/${this.questionId}`);
    },
    //交卷
    submitPaperNow () {
      if (this.flagBtn) {
        Message.success({
          showClose: true,
          message: "你已交过试卷，不能重复交卷！",
          type: 'success'
        });
        return false;
      }
      MessageBox('是否要交卷', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.flagBtn = true;//把开关开启来
        //开关关上
        this.$message({
          type: 'success',
          message: '交卷成功!'
        });
        //设置答案
        this.setAnswerPaper();
        //往后台提交数据
        this.confirmAnswerPaper();

      }).catch((err) => {
        console.log(err)
        this.flagBtn = false;//把开关开启来
        this.$message({
          type: 'info',
          message: '已取消交卷'
        });
      })
    },
    //设置答案
    setAnswerPaper () {
      /*
      [{
        id:1,
        answer:
      }]
      {
        0:1,
        1:0

      }
      */
      let i = 0, j = 0, k = 0, m = 0, n = 0;
      this.questionList.forEach(item => {
        // console.log(item)
        switch (Number(item.type)) {
          //设置单选的答案
          case 1:
            // console.log(typeof this.radio[i])
            /**
             * 1. 0:'radio0' 如果没有值肯定是 '' 有值并且类型是string也是 ''
             */
            item.answer = this.radio[i] >= 0 ? this.radio[i] : '';
            i++;
            break;
          //设置多选的答案
          case 2:
            let arr = this.checked[j]
            item.answer = arr.length >= 0 ? this.checked[j] : '';
            j++;
            break;
          //设置判断的答案
          case 3:
            item.answer = this.panduanData[k] >= 0 ? this.panduanData[k] : '';
            k++;
            break;
          //设置填空的答案
          case 4:
            try {
              let tian = this.tiankongData[m];
              item.answer = tian.split(/\n/) || '';
            } catch (e) {
              item.answer = '';
            }
            m++;
            break;
          //设置简答的答案
          case 5:
            item.answer = this.jiandaData[n] ? this.jiandaData[n] : '';
            n++;
            break;
          default:
            break;
        }
      })
      //console.log(this.jiandaData, this.questionList)
      // console.log(this.radio, this.checked, this.panduanData, this.tiankongData, this.jiandaData)
    },
    //往后台提交数据
    confirmAnswerPaper () {
      //地址
      httpServer({
        url: 'http://127.0.0.1/exam-student/app/control/insertExamResult.php'
      }, {
        question_list: JSON.stringify(this.questionList),
        student_id: localStorage.stuId,
        tao_id: this.$route.params.tao_id,
        course_id: this.$route.params.course_id
      }).then(res => {
        //成功后跳转，失败提示重新提交
        console.log(res)
        res = res.data;
        if (res.respCode == 200) {
          //主观题直接显示考了多少分
          if (res.verify == 0) {
            //显示的分数
            Message.success({
              showClose: false,
              message: "你本次考的成绩是" + res.resTotal + "分",
              type: 'success'
            })
          } else {
            //跳转到
            Message.success({
              showClose: true,
              message: '已提交成功。',
              type: 'success'
            })
            setTimeout(() => {
              this.$router.push('/main/exam/selectTao');
            }, 3000)
          }
        } else {
          this.flagBtn = false;
        }
        //3s后跳转到考试的页面
        // setTimeout(() => {
        //   this.$router.push('/main/exam/course')
        // }, 3000)
      }).catch(err => {

      })
    }
  },
  mounted () {
    //提示用户是否离开此页面（关闭、刷新或者点击后退等）
    // window.addEventListener("beforeunload", () => {
    //   sessionStorage.questions = JSON.stringify(this.questionList);
    // });
    // //console.log(typeof sessionStorage.questions);
    // //通过卷子id把所有的题目拿下来
    // console.log(sessionStorage.questions.length)

    httpServer({
      url: 'http://127.0.0.1/exam-student/app/control/query.php?get_question',
      method: 'get'
    }, {
      tao_id: this.$route.params.tao_id,
      course_id: this.$route.params.course_id
    })
      .then((res) => {
        res = res.data;
        //console.log(res.question_list)
        //设置初始化
        this.questionList = res.question_list;
        this.setInit();
        this.startTime = sessionStorage.startTime;
        this.endTime = sessionStorage.endTime;
        this.instId = sessionStorage.instId;
        // if (this.type == 2) {
        //   this.checki = 0;
        // }
        // sessionStorage.startTime = respData.startTime;
        // sessionStorage.endTime = respData.endTime;
        this.initExam();

      })
      .catch((error) => {

      })




  },
  watch: {
    $route (to, from) {
      //路由之间的跳转
      // this.radio[parseInt(this.$route.params.questionId)] = 'radio' + parseInt(this.$route.params.questionId)
      this.initExam();
    }
  }
}
</script>

<style lang="css">
[v-cloak] {
  display: none;
}
.card-header {
  padding: 10px;
  font-size: 20px;
  margin-top: 30px;
}
.answer-card {
  cursor: pointer;
}
.time,
.answer-card {
  margin-top: 10px;
}
.answer-item {
  padding: 10px;
}
.question {
  padding: 10px 0;
}
.card-content {
  width: 90%;
  margin: 0 auto;
  margin-top: 40px;
}

.card-content .box-card {
  min-height: 500px;
}

.el-button.stu-answer-item {
  margin: 10px;
  width: 60px;
  height: 60px;
}
</style>
