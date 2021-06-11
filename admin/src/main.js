import Vue from 'vue'
// 引入ant
import Antd from 'ant-design-vue';
import 'ant-design-vue/dist/antd.css';
import api from './api'
import App from './App.vue'
import router from './router'
import store from './store'
import utils from './utils'


// 引入样式初始化
import './assets/css/normalize.css'

Vue.config.productionTip = false

Object.defineProperty(Vue.prototype, '$api', { value: api });
Object.defineProperty(Vue.prototype, '$utils', { value: utils });

Vue.use(Antd);

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
