import Vue from 'vue'
// 引入ant
import Antd from 'ant-design-vue';
import 'ant-design-vue/dist/antd.css';
import api from './api'
import App from './App.vue'
import router,{errorRouteMap} from './router'
import store from './store'
import utils from './utils'

import tree from './utils/modules/tree'
import menuApi from './api/modules/menu'


// 引入样式初始化
import './assets/css/normalize.css'

Vue.config.productionTip = false

Object.defineProperty(Vue.prototype, '$api', { value: api });
Object.defineProperty(Vue.prototype, '$utils', { value: utils });

Vue.use(Antd);

async function initApp() {
  const res = await menuApi.getMneuTree();
  const mainRouteMap = tree.dataToTree(res,[],0)
  console.log(mainRouteMap);
  // 将数据赋值，后续用于菜单渲染
  router.options.routes = mainRouteMap
  mainRouteMap.map(item => router.addRoute(item))
  errorRouteMap.map(item => router.addRoute(item))
  // router.addRoutes(mainRouteMap)
  new Vue({
    router,
    store,
    render: h => h(App)
  }).$mount('#app')
}

initApp();

// new Vue({
//   router,
//   store,
//   render: h => h(App)
// }).$mount('#app')

