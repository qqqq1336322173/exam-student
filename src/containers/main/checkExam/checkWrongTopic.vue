<template lang="html">
    <div class="exam-card">
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
                <div class="answer-item">
                  <el-radio disabled type="primary" v-model="radio" :label="0">{{currentQuestion.optionA}}</el-radio>
                </div>
                <div class="answer-item" >
                  <el-radio disabled type="primary" v-model="radio" :label="1">{{currentQuestion.optionB}}</el-radio>
                </div>
                <div class="answer-item" >
                  <el-radio disabled type="primary" v-model="radio" :label="2">{{currentQuestion.optionC}}</el-radio>
                </div>
                <div class="answer-item" >
                  <el-radio disabled type="primary" v-model="radio" :label="3">{{currentQuestion.optionD}}</el-radio>
                </div>
              </div>
            </div>

            <!-- 多选 -->
            <div class="q-multiple" v-show="type == 2">
              <div class="question">
                {{currentQuestion.subject}}
              </div>
                <div class="answer">
                  <el-checkbox-group v-model="checkList">
                  <div class="answer-item" >
                      <el-checkbox disabled type="primary"  label="0">{{currentQuestion.optionA}}</el-checkbox>
                    </div>
                    <div class="answer-item" >
                      <el-checkbox disabled type="primary"  label="1">{{currentQuestion.optionB}}</el-checkbox>
                    </div>
                    <div class="answer-item" >
                      <el-checkbox disabled type="primary"  label="2">{{currentQuestion.optionC}}</el-checkbox>
                    </div>
                    <div class="answer-item" >
                      <el-checkbox disabled type="primary"  label="3">{{currentQuestion.optionD}}</el-checkbox>
                    </div>
                  </el-checkbox-group>
                </div>   
            </div>

            <!-- 判断 -->
            <div class="q-true-or-false" v-show="type == 3">
              <div class="question">
                {{currentQuestion.subject}}
              </div>
              <div class="answer">
                <div class="answer-item" >
                  <el-radio disabled v-model="multipleData"  label="0">正确</el-radio>
                </div>
                <div class="answer-item" >
                  <el-radio disabled v-model="multipleData" label="1">错误</el-radio>
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
import { Message, MessageBox } from "element-ui";
import httpServer from '@/components/httpServer/httpServer.js'

export default {
  /*
  先渲染课程
  点击课程跳转到对应的考试
  */
  data () {
    return {
      errorTopicList: [],
      dialogVisible: false,
      currentQuestion: {},
      questionList: [],
      type: 1,
      textarea: '',
      spaceNum: [],
      radio: 0,
      checkList: ["0", "1"],
      //判断题
      multipleData: 0,
      flag: 0,//默认是下一道题 1 下一道题  -1表示上一道题 0表示当前
      /*
      radio:{
        0:'radio1'
      }
      */
      // radio: {
      //   0: 'radio0'//v-model="radio0"   radio0:''  radio[radioi]
      // },//单选的结果
      /*
      checkbox:{
        0:[],
        1:[],
      }
      
      */
      checkbox: ['zs', 'ls'],

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
  created () {

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
  mounted () {
    // console.log(new Date())
    //获取这个学生的考试的分数,必须是这个成绩都出完了之后才能查看
    let { result_id, course_id, tao_id, questionId } = this.$router.currentRoute.query;
    questionId = Number(questionId);
    //第一道题
    this.questionId = questionId + 1;
    console.log(this.$router.currentRoute.query)
    httpServer({
      url: "http://127.0.0.1/exam-student/app/control/stuResult.php?getStuResult",
      method: "get"
    }, {
      result_id,
      course_id,
      tao_id
    }).then(res => {
      res = res.data;
      if (res.respCode == 200) {
        /*
        营造怎么样的数据
        erroSubjectList: [
          {
            answer: "1",
            optionA: "parseInt()",
            optionB: "parseFloat( )",
            optionC: "Number( )",
            optionD: "toString()",
            question_answer: 0,
            subject: "数字转变为字符串的函数是"
            tao_id: "4",
            type: "1"
          }
       ]

       */
        this.questionList = res.erroSubjectList;
        this.initExam();
        // this.type = Number(res.erroSubjectList[questionId]['type']);
        // this.currentQuestion = res.erroSubjectList[questionId];
        // // this.questionId = questionId
        // this.radio = Number(res.erroSubjectList[questionId].answer);
        // console.log(typeof this.radio)
      }
      console.log(res)
    }).catch(err => {

    })
  },
  methods: {
    lastQuestion () {
      let { result_id, course_id, tao_id, questionId } = this.$route.query;
      //当时第一个的时候
      console.log(questionId)
      if (questionId == 0) {
        Message.success({
          showClose: true,
          message: "这是第一道题啦",
          type: 'success'
        });
        return;
      }
      this.questionId = questionId;
      questionId--;//递减
      this.$router.push(`/main/exam/checkWrongTopic?result_id=${result_id}&course_id=${course_id}&tao_id=${tao_id}&questionId=${questionId}`);
    },
    nextQuestion () {
      let { result_id, course_id, tao_id, questionId } = this.$route.query;
      // let questionId = this.questionId;
      console.log(questionId)
      // this.radio[questionId] = 'radio' + questionId
      // console.log(this.radio)
      // this.saveAnswer(true);
      //console.log(questionId)
      console.log(this.$route.query, questionId)
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
      this.questionId = questionId + 1;//第二道题
      this.$router.push(`/main/exam/checkWrongTopic?result_id=${result_id}&course_id=${course_id}&tao_id=${tao_id}&questionId=${questionId}`);
    },
    initExam () {
      console.log(new Date())
      console.log(this.questionId)
      this.type = Number(this.questionList[this.questionId - 1]['type']);
      this.currentQuestion = this.questionList[this.questionId - 1];
      /*
      设置不同的答案
      */
      this.setAnswer(this.type);
    },
    setAnswer (type) {
      switch (type) {
        //设置单选题的答案
        case 1:
          this.radio = Number(this.questionList[this.questionId - 1].answer);
          break;
        //设置多选题的答案
        case 2:
          //1,2
          this.checkList = this.questionList[this.questionId - 1].answer.split(',');
          break;
        case 3:
          this.multipleData = this.questionList[this.questionId - 1].answer
        default:

          break;

      }
      console.log(this.checkList)
    }
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
.changePass {
  width: 60%;
  margin: 0 auto;
  margin-top: 50px;
}
</style>
