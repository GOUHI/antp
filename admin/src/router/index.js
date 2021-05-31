import Vue from 'vue'
import VueRouter from 'vue-router'
import Log from './log.js'

// 引入进度条
import NProgress from 'nprogress' // Progress 进度条
import 'nprogress/nprogress.css' // Progress 进度条样式


Vue.use(VueRouter)

const mainRouteMap = [
  {
    path: '/',
    name: 'default',
    component: ()=>import('../views/default.vue')
  },
  {
    name:'login',
    path:'/login',
    hidden:true,
    component:()=> import('@/views/Login')
  },
  {
    path: '/about',
    name: 'About',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import(/* webpackChunkName: "about" */ '../views/About.vue')
  }
]

const errorRouteMap = [
  { 
    name: '404', 
    path: '/404', 
    hidden: true, 
    component: ()=> import('@/components/404') 
  }
]

const router = new VueRouter({
  routes:[]
})

// 动态路由添加
router.addRoutes(mainRouteMap)
router.addRoutes(errorRouteMap)
router.addRoutes(Log);


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
