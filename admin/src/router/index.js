import Vue from 'vue'
import VueRouter from 'vue-router'

// 引入进度条
import NProgress from 'nprogress' // Progress 进度条
import 'nprogress/nprogress.css' // Progress 进度条样式


Vue.use(VueRouter)

const mainRouteMap = [
  {
    path:'/user',
    hideInMenu:true,
    component: {render:h=> h("router-view")},
    children:[
      {
        path:'/',
        redirect:'/user/login'
      },
      {
        path:'/user/login',
        name:'login',
        component:()=> import(/* webpackChunkName: "login" */ '@/views/User/Login')
      },
      {
        path:'/user/register',
        name:'register',
        component:()=> import(/* webpackChunkName: "register" */ '@/views/User/Register')
      }
    ]
  },
  // 菜单组件
  {
    path:'/',
    component:()=>import('@/components/layout/BasicLayout'),
    children:[
      // dashboard
      {
        path:'/',
        redirect:'/dashboard'
      },{
        path:'/dashboard',
        name:'dashboard',
        meta:{icon:'dashboard' ,title:'仪表盘'},
        component:{ render: h=> h("router-view")},
        children:[
          {
            path:'/dashboard/analysis',
            name:'analysis',
            meta:{title:'分析页面'},
            component:()=>import(/* webpackChunkName: "analysis" */ '@/views/Dashboard/Analysis')
          }

        ]
      },
      // form
      {
        path:'/form',
        name:'form',
        meta:{icon:'form' ,title:'表单'},
        component:{ render: h=> h("router-view")},
        children:[
          {
            path:'/form/basic-form',
            name:'basicform',
            meta:{title:'基础表单'},
            component:()=>import(/* webpackChunkName: "basicform" */ '@/views/Forms/BasicForm')
          },
          {
            path:'/form/step-form',
            name:'stepform',
            meta:{title:'分布表单'},
            hideChildrenInMenu:true,
            component:()=>import(/* webpackChunkName: "form" */ '@/views/Forms/StepForm'),
            children:[
              {
                path:'/form/step-form',
                redirect:'/form/step-form/info'
              },
              {
                path:'/form/step-form/info',
                name:'info',
                component:()=> import(/* webpackChunkName: "form" */ '@/views/Forms/StepForm/Step1')
              },
              {
                path:'/form/step-form/confirm',
                name:'confirm',
                component:()=> import(/* webpackChunkName: "form" */ '@/views/Forms/StepForm/Step2')
              },
              {
                path:'/form/step-form/result',
                name:'result',
                component:()=> import(/* webpackChunkName: "form" */ '@/views/Forms/StepForm/Step3')
              }
            ]
          },
        ]
      },
    ]
  }
]

const errorRouteMap = [
  { 
    name: '404',
    hideInMenu:true,
    path: '*', 
    hidden: true, 
    component: ()=> import('@/components/404') 
  }
]

const router = new VueRouter({
  routes:[]
})
// 动态路由添加
router.options.routes = mainRouteMap
router.addRoutes(mainRouteMap)
router.addRoutes(errorRouteMap)
// 循环moduls目录下进行动态路由添加
// const files = require.context('./modules', false, /\.js$/);
// files.keys().forEach(key => {
//   router.addRoutes(files(key).default);
// });


// 路由全局前置守卫
router.beforeEach(async (to, from, next) => {
  NProgress.start();
  next();
});

// 路由全局后置守卫
router.afterEach(() => {
  NProgress.done();
});

export default router
