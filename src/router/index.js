import Vue from 'vue'
import Router from 'vue-router'
//一级路由
import login from '@/containers/login/login'
//二级路由
import main from '@/containers/main/main'
import changePass from '@/containers/main/personalCenter/changePass'
import homepage from '@/containers/main/homepage/index'
//三级路由
//import exam from '@/containers/main/examCard/index'
import examCourse from '@/containers/main/examCard/course'
import examCard from '@/containers/main/examCard/index'
import examTao from '@/containers/main/examCard/tao'
import examSelectTao from '@/containers/main/checkExam/selectTao'
import examCheckWrongTopic from '@/containers/main/checkExam/checkWrongTopic'
import examCheckRanking from '@/containers/main/checkExam/checkRanking'
Vue.use(Router)
const originalPush = Router.prototype.push;
Router.prototype.push = function (location) {
  console.log(location)
  // console.log(originalPush)
  return originalPush.call(this, location).catch(err => err)
};

export default new Router({
  // mode: 'hash',
  routes: [
    {
      path: '/login',
      name: 'login',
      component: login,
    },
    {
      path: '/main',
      name: 'main',
      component: main,
      children: [
        {
          path: '/',
          name: 'examCourse',
          component: examCourse
        },
        {
          path: 'exam/course',
          name: 'examCourse',
          component: examCourse
        },
        {
          path: 'homepage',
          component: homepage,
        },
        {
          path: 'personalCenter/changePass',
          name: 'changePass',
          component: changePass,
        },
        {
          path: 'exam/tao',
          name: 'examTao',
          component: examTao
        },
        {
          path: 'exam/:tao_id/:course_id/:questionId',
          name: 'examGotoExam',
          component: examCard
        },
        //这个学生下面的所有的试卷
        {
          path: 'exam/selectTao',
          name: 'examSelectTao',
          component: examSelectTao
        },
        //查看成绩里面的错题集
        {
          path: 'exam/checkWrongTopic',
          name: 'examCheckWrongTopic',
          component: examCheckWrongTopic
        },
        {
          path: 'exam/checkRanking',
          name: 'examCheckRanking',
          component: examCheckRanking
        }
      ]
    }
  ]
})
